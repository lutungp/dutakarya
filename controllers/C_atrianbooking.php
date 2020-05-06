<?php
include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';

class C_atrianbooking
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getAntrian($data)
    {
        template("./../views/v_nomerurut.php");
    }
}

$antrianbooking = new C_atrianbooking($conn, $conn2, $config);
$data = $_GET;
$antrianbooking->getAntrian($data);
