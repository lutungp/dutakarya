<?php

include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';

class C_booking
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
    }

    public function booking()
    {
        template('../views/v_booking.php');
    }

    public function create($data)
    {

        $sqlurutan = "SELECT COUNT(*) AS urutan FROM t_booking_hospital WHERE bookinghosp_tanggal = '". $data["pesan_tanggal"]. "' AND bookinghosp_aktif = 'Y' ORDER BY bookinghosp_urutan DESC LIMIT 1";
        $urutan = $this->conn2->query($sqlurutan);
        $urutan = mysqli_fetch_object($urutan);
        $field = ["pasien_norm", "pasien_nama", "bookinghosp_tanggal", "layanan_kode", "pasien_email", "pasien_telp", "bookinghosp_urutan", "bookinghosp_created_by", "bookinghosp_created_date"];
        $dataSave = [$data["pasien_norm"], $data["pasien_nama"], $data["pesan_tanggal"], "RJ001", $data["pasien_email"], $data["pasien_tlp"], ($urutan->urutan+1), "PASIEN", date("Y-m-d H:i:s")];
        
        $action = query_create($this->conn2, 't_booking_hospital', $field, $dataSave);

        $pasiennorm = "3172724" . str_pad($data["pasien_norm"], 8, "0", STR_PAD_LEFT);
        if($action == "200"){
            $datares = array(
                "code"  => "200",
                "urutan" => $urutan->urutan + 1,
                "pasien_norm" => $data["pasien_norm"],
                "pasien_nama" => $data["pasien_nama"]
            );
        } else {
            $datares = array(
                "code"  => "202",
                "urutan" => "",
            );
        }

        return $datares;
    }
}

$booking = new C_booking($conn, $conn2);
$action = isset($_GET["action"]) ? $_GET["action"] : '';
switch ($action) {    
    case 'create':
        $booking = new C_booking($conn, $conn2);
        $action = $booking->create($_POST);
        if($action["code"] == "200"){
            header("Location: " . $config['base_url'] . "/controllers/C_antrianbooking.php?urutan=" . $action['urutan'] . "&pasien_norm=" . $action['pasien_norm']);
        } else {
            template('../views/v_booking.php');
        }
        break;
    case 'nourut' :
        header("Location: " . $config['base_url'] . "/controllers/C_antrianbooking.php?pasien_norm=" . $_GET['rm']);
        break;
    default:
        $booking->booking();
        break;
}