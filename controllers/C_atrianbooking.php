<?php
include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';
include '../models/M_antrianbooking.php';

class C_atrianbooking
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    public $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_antrianbooking($conn, $conn2, $config);
    }

    public function getAntrian($data)
    {
        $dataantrian = $this->model->getAntrianBooking($data["pasien_norm"]);
        template("./../views/v_nomerurut.php", $dataantrian);
    }
}

$antrianbooking = new C_atrianbooking($conn, $conn2, $config);
$data = $_GET;
$antrianbooking->getAntrian($data);
