<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_hargabrg.php';

class C_hargabrg
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_hargabrg($conn, $conn2, $config);
    }

    public function getBarangHna()
    {
        $data = $this->model->getBarangHna();
        return $data;
    }

    public function formTransaksi()
    {
        $result = NULL;
        templateAdmin($this->conn2, '../views/baranghna/v_formhargabrg.php', json_encode($result), "MASTER", "SETUP HARGA BRG");
    }

    public function getbarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

}

$hargabrg = new C_hargabrg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getbaranghna':
        $hargabrg->getBarangHna();
        break;
    
    case 'getbarang':
        $hargabrg->getBarang();
        break;

    case 'formtransaksi':
        $hargabrg->formTransaksi();
        break;

    default:
        templateAdmin($conn2, '../views/baranghna/v_hargabrg.php', NULL, "MASTER", "SETUP HARGA BRG");
    break;
}