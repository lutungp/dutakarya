<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_kontrakrekanan.php';

class C_kontrakrekanan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_kontrakrekanan($conn, $conn2, $config);
    }

}

$kontrakrekanan = new C_kontrakrekanan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    default:
        templateAdmin($conn2, '../views/kontrak/v_kontrakrekanan.php', NULL, "KEUANGAN", "KONTRAK REKANAN");
    break;
}