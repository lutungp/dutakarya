<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_hutang.php';

class C_hutang
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

        $this->model = new M_hutang($conn, $conn2, $config);
    }

    public function getHutang()
    {
        $result = $this->model->getHutang();

        echo json_encode($result);
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $jenis = isset($data['jenis']) ? $data['jenis'] : 'pabrik';
        $result = $this->model->getRekanan($search, $jenis);
        echo json_encode($result);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        $hutang_id = isset($data['id']) ? $data['id'] : 0;
        if ($hutang_id > 0) {
            $result['datahutang'] = $this->model->getDataHutang($hutang_id);
            $result['datahutangdetail'] = $this->model->getDataHutangDetail($hutang_id);
        }
        
        templateAdmin($this->conn2, '../views/hutang/v_formhutang.php', json_encode($result), 'KEUANGAN', 'PELUNASAN HUTANG');
    }
}

$hutang = new C_hutang($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'gethutang':
        $hutang->getHutang();
        break;
    // case 'submit':
    //     $hutang->simpanhutang($_POST);
    //     break;
    case 'formtransaksi':
        $hutang->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $hutang->getRekanan($_GET);
        break;
    // case 'getbarang':
    //     $hutang->getBarang();
    //     break;
    default:
        templateAdmin($conn2, '../views/hutang/v_hutang.php', NULL, 'KEUANGAN', 'PELUNASAN HUTANG');
    break;
}
