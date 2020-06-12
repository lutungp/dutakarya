<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_gajikaryawan.php';

class C_gajikaryawan
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    public $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_gajikaryawan($conn, $conn2, $config);
    }

    public function getGajiKaryawan($data)
    {
        $limit = $data["limit"];
        $offset = $data["offset"];
        $username = $_SESSION['USERNAME'];
        $datagaji = $this->model->getGajiKaryawan($offset, $limit, $username);

        echo json_encode($datagaji);
    }

}

$antrianbooking = new C_gajikaryawan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getgajikaryawan':
        $antrianbooking->getGajiKaryawan($_POST);
        break;
    default:
        templateAdmin($conn2, '../views/v_gajikaryawan.php', $data = "", $active1 = "INFO", $active2 = "INFO GAJI KARYAWAN");
        break;
}

