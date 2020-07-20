<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infotagihan.php';

class C_infotagihan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infotagihan($conn, $conn2, $config);
    }

    public function getRekanan()
    {
        $data = $this->model->getRekanan();
        echo json_encode($data);
    }

    public function getPenagihan($tanggal, $data)
    {
        $rekananArr = isset($data['rekananArr']) ? implode(',', $data['rekananArr']) : '';
        $tanggal = $tanggal <> '' ? $tanggal : explode(' - ', $data['tanggal']);
        $data = $this->model->getPenagihan($tanggal, $rekananArr);
        echo json_encode($data);
    }
}

$infotagihan = new C_infotagihan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getrekanan':
        $infotagihan->getRekanan();
        break;
    case 'getpenagihan':
        $tanggal = isset($_GET['tanggal']);
        $infotagihan->getPenagihan($tanggal, $_POST);
        break;
    case 'getpengiriman':
        $tanggal = isset($_GET['tanggal']);
        $infotagihan->getPengiriman($tanggal, $_POST);
        break;
    default:
        templateAdmin($conn2, '../views/info/v_infotagihan.php', NULL, "INFO", "INFO PENAGIHAN");
        break;
}