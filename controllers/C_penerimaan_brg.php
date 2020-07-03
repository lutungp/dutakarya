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
        templateAdmin($this->conn2, '../views/penerimaanbrg/v_penerimaan_brg.php', NULL, 'TRANSAKSI', 'PENERIMAAN BARANG');
    }

    public function getPenerimaan()
    {
        $data = $this->model->getPenerimaan();
        echo json_encode($data);
    }

    public function formTransaksi()
    {
        templateAdmin($this->conn2, '../views/penerimaanbrg/v_formpenerimaan_brg.php', NULL, 'TRANSAKSI', 'PENERIMAAN BARANG');
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $result = $this->model->getRekanan($search);
        echo json_encode($result);
    }

    public function getBarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }
}

$penerimaan = new C_penerimaan_brg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getpenerimaan':
        $penerimaan->getPenerimaan();
        break;
    case 'submit':
        $penerimaan->simpanPenerimaan($_POST);
        break;
    case 'formtransaksi':
        $penerimaan->formTransaksi($_POST);
        break;
    case 'getrekanan':
        $penerimaan->getRekanan($_GET);
        break;
    case 'getbarang':
        $penerimaan->getBarang();
        break;
    default:
        $penerimaan->Penerimaan();
    break;
}
