<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_pelunasan.php';

class C_pelunasan
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

        $this->model = new M_pelunasan($conn, $conn2, $config);
    }

    public function getPelunasan()
    {
        $data = $this->model->getPelunasan();

        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        templateAdmin($this->conn2, '../views/pelunasan/v_formpelunasan.php', json_encode(NULL), 'KEUANGAN', 'PELUNASAN');
    }
    
}

$pelunasan = new C_pelunasan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getpelunasan':
        $pelunasan->getPelunasan();
        break;
    case 'formtransaksi':
        $pelunasan->formTransaksi($_GET);
        break;
    default:
        templateAdmin($conn2, '../views/pelunasan/v_pelunasan.php', NULL, 'KEUANGAN', 'PELUNASAN');
    break;
}

