<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infopenerimaan.php';

class C_infopenerimaan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infopenerimaan($conn, $conn2, $config);
    }

    public function getPenerimaan($tanggal, $data)
    {
        $rekananArr = isset($data['rekananArr']) ? implode(',', $data['rekananArr']) : '';
        $barangArr = isset($data['barangArr']) ? implode(',', $data['barangArr']) : '';
        $tanggal = $tanggal <> '' ? $tanggal : explode(' - ', $data['tanggal']);
        $data = $this->model->getPenerimaan($tanggal, $rekananArr, $barangArr);
        echo json_encode($data);
    }

    public function getBarang()
    {
        $data = $this->model->getBarang();
        echo json_encode($data);
    }

    public function getRekanan()
    {
        $data = $this->model->getRekanan();
        echo json_encode($data);
    }

}

$infopenerimaan = new C_infopenerimaan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getpenerimaan':
        $tanggal = isset($data['tanggal']) ? $data['tanggal'] : '';
        $infopenerimaan->getPenerimaan($tanggal, $_POST);
        break;

    case 'getbarang':
        $infopenerimaan->getBarang();
        break;
    
    case 'getrekanan':
        $infopenerimaan->getRekanan();
        break;

    default:
        templateAdmin($conn2, '../views/info/v_infopenerimaan.php', NULL, "INFO", "INFO PENERIMAAN");
        break;
}