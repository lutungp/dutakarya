<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infohutang.php';

class C_infohutang
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infohutang($conn, $conn2, $config);
    }

    public function getRekanan()
    {
        $data = $this->model->getRekanan();
        echo json_encode($data);
    }

    public function getHutangLunas($tanggal, $data)
    {
        $rekananArr = isset($data['rekananArr']) ? implode(',', $data['rekananArr']) : '';
        $tanggal = $tanggal <> '' ? $tanggal : explode(' - ', $data['tanggal']);
        $data = $this->model->getHutangLunas($tanggal, $rekananArr);
        echo json_encode($data);
    }

    public function getMaklon($tanggal, $data)
    {
        $rekananArr = isset($data['rekananArr']) ? implode(',', $data['rekananArr']) : '';
        $barangArr = isset($data['barangArr']) ? implode(',', $data['barangArr']) : '';
        $tanggal = $tanggal <> '' ? $tanggal : explode(' - ', $data['tanggal']);
        $tagih = isset($data['tagih']) ? $data['tagih'] : '';
        $data = $this->model->getMaklon($tanggal, $rekananArr, $barangArr, $tagih);
        echo json_encode($data);
    }

    public function getBarang()
    {
        $data = $this->model->getBarang();
        echo json_encode($data);
    }
}

$infohutang = new C_infohutang($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getrekanan':
        $infohutang->getRekanan();
        break;
    case 'gethutanglunas':
        $tanggal = isset($_GET['tanggal']);
        $infohutang->getHutangLunas($tanggal, $_POST);
        break;
    case 'getmaklon':
        $tanggal = isset($_GET['tanggal']);
        $infohutang->getMaklon($tanggal, $_POST);
        break;
    case 'getbarang':
        $infohutang->getBarang();
        break;
    default:
        templateAdmin($conn2, '../views/info/v_infohutang.php', NULL, "INFO", "INFO HUTANG");
        break;
}