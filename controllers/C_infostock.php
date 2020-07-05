<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infostock.php';

class C_infostock
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infostock($conn, $conn2, $config);
    }
}

$infostock = new C_infostock($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    
    default:
        templateAdmin($conn2, '../views/info/v_infostock.php', NULL, "INFO", "INFO STOCK");
        break;
}