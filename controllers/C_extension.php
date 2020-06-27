<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_extension.php';

class C_extension
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_extension($conn, $conn2, $config);
    }

    public function getExtension()
    {
        templateAdmin($this->conn2, '../views/v_extension.php', NULL, "INFO", "EXTENSION");
    }
}

$extension = new C_extension($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : '';
switch ($action) {
    default:
        $extension->getExtension();
        break;
}