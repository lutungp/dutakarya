<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_pegawai.php';

class C_pegawai
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_pegawai($conn, $conn2, $config);
    }

    public function getPegawai()
    {
        $result = $this->model->getPegawai();
        echo json_encode($result);
    }

    public function createPegawai($data)
    {
        $fieldSave = ["pegawai_nama", "pegawai_bagian", "pegawai_alamat", "pegawai_notelp", "pegawai_created_by", "pegawai_created_date", "pegawai_revised"];
        $dataSave = [$data["pegawai_nama"], $data["pegawai_bagian"], $data['pegawai_alamat'], $data['pegawai_notelp'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
        $action = query_create($this->conn2, 'm_pegawai', $fieldSave, $dataSave);
        $this->saveJadwal($action, $data['rows']);
        if ($action > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function updatePegawai($data)
    {
        $fieldSave = ["pegawai_nama", "pegawai_bagian", "pegawai_notelp", "pegawai_alamat", "pegawai_updated_by", "pegawai_updated_date", "pegawai_revised"];
        $dataSave = [$data["pegawai_nama"], $data["pegawai_bagian"], $data["pegawai_notelp"], $data["pegawai_alamat"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "pegawai_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE pegawai_id = " . $data['pegawai_id'];
        $action = query_update($this->conn2, 'm_pegawai', $field, $where);
        $this->saveJadwal($data['pegawai_id'], $data['rows']);

        if ($action > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function deletePegawai($data)
    {
        $fieldSave = ["pegawai_aktif", "pegawai_void_alasan", "pegawai_void_by", "pegawai_void_date"];
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
        $where = "WHERE pegawai_id = " . $data['pegawai_id'];
        $action = query_update($this->conn2, 'm_pegawai', $field, $where);
        $this->saveJadwal($data['pegawai_id'], $data['rows']);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function saveJadwal($pegawai_id, $data)
    {
        foreach ($data as $key => $val) {
            $jadwalpeg_aktif = $val['jadwalpeg_aktif'] == 'true' ? 'Y' : 'N';
            if ($val['jadwalpeg_id'] > 0) {
                $fieldSave = ["m_pegawai_id", "hari", "jadwalpeg_aktif", "jadwalpeg_updated_by", "jadwalpeg_updated_date", "jadwalpeg_revised"];
                $dataSave = [$pegawai_id, $val["hari"], $jadwalpeg_aktif, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "jadwalpeg_revised+1"];
                $field = "";
                foreach ($fieldSave as $key => $value) {
                    $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                    if (!preg_match("/revised/i", $value)) {
                        $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                    } else {
                        $field .= "$value = $dataSave[$key]" . $regex . " ";
                    }
                }
                $where = "WHERE jadwalpeg_id = " . $val['jadwalpeg_id'];
                $action = query_update($this->conn2, 't_jadwalpeg', $field, $where);
            } else {
                $fieldSave = ["m_pegawai_id", "hari", "jadwalpeg_aktif", "jadwalpeg_created_by", "jadwalpeg_created_date", "jadwalpeg_revised"];
                $dataSave = [$pegawai_id, $val["hari"], $jadwalpeg_aktif, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
                $action = query_create($this->conn2, 't_jadwalpeg', $fieldSave, $dataSave);
            }
        }
    }

    public function getJadwal($data)
    {
        $pegawai_id = $data['pegawai_id'];
        $result = $this->model->getJadwal($pegawai_id);

        echo json_encode($result);
    }

}

$pegawai = new C_pegawai($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getpegawai':
        $pegawai->getPegawai();
        break;
    
    case 'createpegawai':
        if($_POST["pegawai_id"] == 0) {
            $pegawai->createPegawai($_POST);
        } else {
            $pegawai->updatePegawai($_POST);
        }
        
        break;
    
    case 'deletepegawai':
        $pegawai->deletepegawai($_POST);
        break;
    
    case 'getjadwal':
        $pegawai->getJadwal($_POST);
        break;
    
    default:
        templateAdmin($conn2, '../views/v_pegawai.php', NULL, "MASTER", "MASTER PEGAWAI");
        break;
}