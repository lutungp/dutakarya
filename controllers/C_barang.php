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

    public function createBarang($data, $satkonv, $bahanbaku)
    {
        $fieldSave = ["barang_nama", "barang_kode", "m_satuan_id", "barang_created_by", "barang_created_date", "barang_revised"];
        $dataSave = [$data["barang_nama"], $data["barang_kode"], $data["m_satuan_id"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
        $barang_id = query_create($this->conn2, 'm_barang', $fieldSave, $dataSave);
        if ($barang_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
        $this->simpanSatKonv($barang_id, $satkonv);
        $this->simpanBahan($barang_id, $bahanbaku);
    }

    public function updateBarang($data, $satkonv, $bahanbaku)
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
        $barang_id = $data['barang_id'];
        $this->simpanSatKonv($barang_id, $satkonv);
        $this->simpanBahan($barang_id, $bahanbaku);
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

    public function simpanBahan($barang_id, $bahanbaku)
    {
        $fieldSave = ["bahanbrg_aktif"];
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
        $where = "WHERE m_brg_id = " . $barang_id;
        $action = query_update($this->conn2, 'm_bahanbrg', $field, $where);

        foreach ($bahanbaku as $key => $val) {
            if ($val["bahanbrg_id"] > 0) {
                $fieldUpdate = ["bahanbrg_ketika", "bahanbrg_qty", "m_brg_id", "m_barang_id", "bahanbrg_aktif", "bahanbrg_updated_by", "bahanbrg_updated_date", "bahanbrg_revised"];
                $dataSave = [$val["bahanbrg_ketika"], $val["bahanbrg_qty"], $barang_id, $val["m_barang_id"], "Y", $_SESSION['USER_ID'], date('Y-m-d H:i:s'), 'bahanbrg_revised+1'];

                $field = '';
                foreach ($fieldUpdate as $key => $value) {
                    $regex = (integer)$key < count($fieldUpdate)-1 ? "," : "";
                    if (!preg_match("/revised/i", $value)) {
                        $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                    } else {
                        $field .= "$value = $dataSave[$key]" . $regex . " ";
                    }
                }
                $where = " WHERE bahanbrg_id = " . $val['bahanbrg_id'];
                query_update($this->conn2, 'm_bahanbrg', $field, $where);
            } else {
                $fieldSave = ["bahanbrg_ketika", "bahanbrg_qty", "m_brg_id", "m_barang_id", "bahanbrg_aktif", "bahanbrg_created_by", "bahanbrg_created_date"];
                $dataSave = [$val["bahanbrg_ketika"], $val["bahanbrg_qty"], $barang_id, $val["m_barang_id"], "Y", $_SESSION['USER_ID'], date('Y-m-d H:i:s')];
                
                $satkonv_id = query_create($this->conn2, 'm_bahanbrg', $fieldSave, $dataSave);
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

    public function getHarga($barang_id)
    {
        $data = $this->model->getHarga($barang_id);
        echo json_encode($data);
    }

    public function getBahanBaku($barang_id)
    {
        $data['bahanbrg'] = $this->model->getBrgBahanBaku($barang_id);
        $data['barang'] = $this->model->getBahanBaku();
        echo json_encode($data);
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
        $dataSatKonv = isset($_POST['dataSatKonv']) ? $_POST['dataSatKonv'] : [];
        $dataBahan = isset($_POST['dataBahan']) ? $_POST['dataBahan'] : [];
        if($_POST['dataForm']["barang_id"] == 0) {
            $barang->createBarang($_POST['dataForm'], $dataSatKonv, $dataBahan);
        } else {
            $barang->updateBarang($_POST['dataForm'], $dataSatKonv, $dataBahan);
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
    
    case 'getharga':
        $barang->getHarga($_POST["barang_id"]);
        break;
    
    case 'getbahanbaku':
        $barang->getBahanBaku($_POST['barang_id']);
        break;
    
    default:
        templateAdmin($conn2, '../views/v_barang.php', NULL, "MASTER", "MASTER BARANG");
        break;
}