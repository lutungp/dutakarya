<?php

include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';

class C_dashboard
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

        echo $_SESSION["USERNAME"];
    }
}

$jadwaldokter = new C_dashboard($conn, $conn2, $config);
