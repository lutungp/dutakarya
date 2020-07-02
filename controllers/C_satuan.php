<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_satuan.php';

class C_satuan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_satuan($conn, $conn2, $config);
    }

    public function getSatuan()
    {
        $result = $this->model->getSatuan();
        echo json_encode($result);
    }

    public function createSatuan($data)
    {
        $fieldSave = ["satuan_nama", "satuan_kode", "satuan_created_by", "satuan_created_date", "satuan_revised"];
        $dataSave = [$data["satuan_nama"], $data["satuan_kode"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 0];
        $action = query_create($this->conn2, 'm_satuan', $fieldSave, $dataSave);
        if ($action > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function updateSatuan($data)
    {
        $fieldSave = ["satuan_nama", "satuan_kode", "satuan_created_by", "satuan_created_date", "satuan_revised"];
        $dataSave = [$data["satuan_nama"], $data["satuan_kode"], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "satuan_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE satuan_id = " . $data['satuan_id'];
        $action = query_update($this->conn2, 'm_satuan', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function deleteSatuan($data)
    {
        $fieldSave = ["satuan_aktif", "satuan_void_alasan", "satuan_void_by", "satuan_void_date"];
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
        $where = "WHERE satuan_id = " . $data['satuan_id'];
        $action = query_update($this->conn2, 'm_satuan', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }
}

$user = new C_satuan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getsatuan':
        $user->getSatuan();
        break;
    
    case 'createsatuan':
        if($_POST["satuan_id"] == 0) {
            $user->createSatuan($_POST);
        } else {
            $user->updateSatuan($_POST);
        }    
        break;
    
    case 'deletesatuan':
        $user->deleteSatuan($_POST);
        break;
    
    default:
        templateAdmin($conn2, '../views/v_satuan.php', NULL, "MASTER", "MASTER SATUAN");
        break;
}