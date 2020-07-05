<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_barang.php';

class C_barang
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_barang($conn, $conn2, $config);
    }

    public function getBarang()
    {
        $result = $this->model->getBarang();
        echo json_encode($result);
    }

    public function createBarang($data, $satkonv)
    {
        $fieldSave = ["barang_nama", "barang_kode", "m_satuan_id", "barang_created_by", "barang_created_date", "barang_revised"];
        $dataSave = [$data["barang_nama"], $data["barang_kode"], $data["m_satuan_id"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
        $barang_id = query_create($this->conn2, 'm_barang', $fieldSave, $dataSave);
        if ($barang_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
        $this->simpanSatKonv($barang_id);
    }

    public function updateBarang($data, $satkonv)
    {
        $fieldSave = ["barang_nama", "barang_kode", "m_satuan_id", "barang_created_by", "barang_created_date", "barang_revised"];
        $dataSave = [$data["barang_nama"], $data["barang_kode"], $data["m_satuan_id"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "barang_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE barang_id = " . $data['barang_id'];
        $action = query_update($this->conn2, 'm_barang', $field, $where);
        $this->simpanSatKonv($data['barang_id'], $satkonv);
        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function simpanSatKonv($barang_id, $satkonv)
    {
        /* Non aktif satkov */
        $fieldSave = ["satkonv_aktif"];
        $dataSave = ["N"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE m_barang_id = " . $barang_id;
        $action = query_update($this->conn2, 'm_satuan_konversi', $field, $where);

        $fieldSave = ["m_barang_id", "m_satuan_id", "satkonv_nilai", "satkonv_created_by", "satkonv_created_date"];
        $fieldUpdate = ["m_barang_id", "m_satuan_id", "satkonv_nilai", "satkonv_aktif", "satkonv_updated_by", "satkonv_updated_date", "satkonv_revised"];

        foreach ($satkonv as $key => $val) {
            if ($val['satkonv_id'] == 0) {
                $dataSave = [$barang_id, $val["m_satuan_id"], $val["satkonv_nilai"], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                $satkonv_id = query_create($this->conn2, 'm_satuan_konversi', $fieldSave, $dataSave);
            } else {
                $dataSave = [$barang_id, $val["m_satuan_id"], $val["satkonv_nilai"], "Y", $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "satkonv_revised+1"];
                $field = "";
                foreach ($fieldUpdate as $key => $value) {
                    $regex = (integer)$key < count($fieldUpdate)-1 ? "," : "";
                    if (!preg_match("/revised/i", $value)) {
                        $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                    } else {
                        $field .= "$value = $dataSave[$key]" . $regex . " ";
                    }
                }
                $where = " WHERE satkonv_id = " . $val['satkonv_id'];
                query_update($this->conn2, 'm_satuan_konversi', $field, $where);
            }
        }
    }

    public function deleteBarang($data)
    {
        $fieldSave = ["barang_aktif", "barang_void_alasan", "barang_void_by", "barang_void_date"];
        $dataSave = ["N", $data["alasan"], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE barang_id = " . $data['barang_id'];
        $action = query_update($this->conn2, 'm_barang', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    
    public function getSatuan($data)
    {
        $aktif = isset($data['aktif']) ? $data['aktif'] : '';
        $result = $this->model->getSatuan($aktif);
        echo json_encode($result);
    }

    public function getSatkonv($barang_id)
    {
        $datasatkonv = $this->model->getSatkonv($barang_id);
        echo json_encode($datasatkonv);
    }

}

$barang = new C_barang($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getbarang':
        $barang->getBarang();
        break;
    
    case 'createbarang':
        if($_POST['dataForm']["barang_id"] == 0) {
            $barang->createBarang($_POST['dataForm'], $_POST['dataSatKonv']);
        } else {
            $barang->updateBarang($_POST['dataForm'], $_POST['dataSatKonv']);
        }    
        break;
    
    case 'getsatuan':
        $barang->getSatuan($_POST);
        break;
    
    case 'deletebarang':
        $barang->deleteBarang($_POST);
        break;

    case 'getsatkonv':
        $barang->getSatkonv($_POST["barang_id"]);
        break;
    
    default:
        templateAdmin($conn2, '../views/v_barang.php', NULL, "MASTER", "MASTER BARANG");
        break;
}