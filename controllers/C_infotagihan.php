<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infotagihan.php';

class C_infotagihan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infotagihan($conn, $conn2, $config);
    }
}

$infotagihan = new C_infotagihan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    default:
        templateAdmin($conn2, '../views/info/v_infotagihan.php', NULL, "INFO", "INFO TAGIHAN");
        break;
}