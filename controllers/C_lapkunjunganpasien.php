<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
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
        $layanan = $this->model->getLayanan();
        $data["layanan"] = json_encode($layanan);
        templateAdmin($this->conn2, '../views/v_lapkunjungan.php', $data, "TRANSAKSI", "KUNJUNGAN PASIEN");
    }

    public function getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $data)
    {
        $data = $this->model->getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $data);

        echo json_encode($data);
    }

    public function getChartData($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $data)
    {
        $data = $this->model->getChartData($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan);
        echo json_encode($data);
    }
}

$kunjunganpasien = new C_lapkunjunganpasien($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
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
    
    case 'getchartdata':
        $tglawal = isset($_POST["tglawal"]) ? $_POST["tglawal"] : "";
        $tglakhir = isset($_POST["tglakhir"]) ? $_POST["tglakhir"] : "";
        $instalasi = isset($_POST["instalasi"]) ? $_POST["instalasi"] : "";
        $layanan = isset($_POST["layanan"]) ? $_POST["layanan"] : "";
        $groupjaminan = isset($_POST["groupjaminan"]) ? $_POST["groupjaminan"] : "";
        $tipejaminan = isset($_POST["tipejaminan"]) ? $_POST["tipejaminan"] : "";
        $kunjunganpasien->getChartData($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, NULL);
        break;

    default:
        $kunjunganpasien->lapKunjunganPasien();
        break;
}
