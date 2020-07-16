<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_rekanan.php';

class C_supplier
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_rekanan($conn, $conn2, $config);
    }

    public function getRekanan()
    {
        $result = $this->model->getRekanan();
        echo json_encode($result);
    }

    public function createRekanan($data)
    {
        $fieldSave = ["rekanan_nama", "rekanan_kode", "rekanan_telp", "rekanan_email", "rekanan_alamat", "rekanan_created_by",  "rekanan_created_date", "rekanan_revised"];
        $dataSave = [$data["rekanan_nama"],  $data["rekanan_kode"], $data["rekanan_telp"], $data["rekanan_email"], nl2br($data["rekanan_alamat"]), $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
        
        $action = query_create($this->conn2, 'm_rekanan', $fieldSave, $dataSave);
        if ($action > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function updateRekanan($data)
    {
        $fieldSave = ["rekanan_nama",  "rekanan_kode", "rekanan_telp", "rekanan_email", "rekanan_alamat", "rekanan_created_by",  "rekanan_created_date", "rekanan_revised"];
        $dataSave = [$data["rekanan_nama"], $data["rekanan_kode"], $data["rekanan_telp"], $data["rekanan_email"], nl2br($data["rekanan_alamat"]), $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "rekanan_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE rekanan_id = " . $data['rekanan_id'];
        $action = query_update($this->conn2, 'm_rekanan', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function deleteRekanan($data)
    {
        $fieldSave = ["rekanan_aktif", "rekanan_void_alasan", "rekanan_void_by", "rekanan_void_date"];
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
        $where = "WHERE rekanan_id = " . $data['rekanan_id'];
        $action = query_update($this->conn2, 'm_rekanan', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }
}

$user = new C_supplier($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getrekanan':
        $user->getRekanan();
        break;
    
    case 'createrekanan':
        if($_POST["rekanan_id"] == 0) {
            $user->createRekanan($_POST);
        } else {
            $user->updateRekanan($_POST);
        }    
        break;
    
    case 'deleterekanan':
        $user->deleteRekanan($_POST);
        break;
    
    default:
        templateAdmin($conn2, '../views/v_rekanan.php', NULL, "MASTER", "MASTER REKANAN");
        break;
}