<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_penerimaan_brg.php';

class C_penerimaan_brg
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

        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "./controllers/C_login");
        }

        $this->model = new M_penerimaan_brg($conn, $conn2, $config);
    }

    public function Penerimaan()
    {
        templateAdmin($this->conn2, '../views/v_penerimaan_brg.php');
    }
}

$penerimaan = new C_penerimaan_brg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'submit':
        $penerimaan->simpanPenerimaan($_POST);
        break;
    default:
        $penerimaan->Penerimaan();
    break;
}
