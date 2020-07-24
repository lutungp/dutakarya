<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_dashboard.php';

class C_dashboard
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;

    private $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_dashboard($conn, $conn2, $config);
        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "/controllers/C_login.php");
        }
    }

    public function getJadwal($data)
    {
        $tanggalreal = $data['tanggal'];
        $tanggal = strtotime(date('Y-m-d', strtotime($data['tanggal'])));
        $day = date('N', $tanggal);
        $week = getWeeks($tanggalreal, $day) - 1;
        $month = intval(date('m', $tanggal));
        $year = intval(date('Y', $tanggal));
        $rit = $data['rit'];
        $rekananArr = '';
        if (isset($data['rekanan'])) {
            $rekananArr = str_replace('[', '', $data['rekanan']);
            $rekananArr = str_replace(']', '', $rekananArr);
        }
        $barangArr = '';
        if (isset($data['barang'])) {
            $barangArr = str_replace('[', '', $data['barang']);
            $barangArr = str_replace(']', '', $barangArr);
        }
        $data = $this->model->getJadwal($day, $week, $month, $year, $tanggalreal, $rit, $barangArr, $rekananArr);

        echo json_encode($data);
    }
}

$dashboard = new C_dashboard($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getjadwal':
        $dashboard->getJadwal($_POST);
        break;
    default:
        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "/controllers/C_login");
        } else {
            templateAdmin($conn2, '../views/v_dashboard.php'); 
        }
        break;
}
