<?php
include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';
include '../models/M_lapkunjunganpasien.php';

class C_lapkunjunganpasien
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
        $this->model = new M_lapkunjunganpasien($conn, $conn2, $config);
    }

    public function lapKunjunganPasien()
    {
        templateAdmin($this->conn2, '../views/v_lapkunjungan.php', NULL, "TRANSAKSI", "KUNJUNGAN PASIEN");
    }

    public function getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $data)
    {
        $data = $this->model->getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $data);

        
    }
}

$kunjunganpasien = new C_lapkunjunganpasien($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
// $tglawal = 


switch ($action) {
    case 'getPerLayanan':
        $tglawal = isset($_GET["tglawal"]) ? $_GET["tglawal"] : "";
        $tglakhir = isset($_GET["tglakhir"]) ? $_GET["tglakhir"] : "";
        $instalasi = isset($_GET["instalasi"]) ? $_GET["instalasi"] : "";
        $layanan = isset($_GET["layanan"]) ? $_GET["layanan"] : "";
        $groupjaminan = isset($_GET["groupjaminan"]) ? $_GET["groupjaminan"] : "";
        $tipejaminan = isset($_GET["tipejaminan"]) ? $_GET["tipejaminan"] : "";
        $kunjunganpasien->getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $_GET);
        break;

    default:
        $kunjunganpasien->lapKunjunganPasien();
        break;
}
