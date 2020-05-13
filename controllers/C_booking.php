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

        $sqlurutan = "SELECT COUNT(*) AS urutan FROM t_booking_hospital WHERE bookinghosp_tanggal = '". date("Y-m-d", strtotime($data["pesan_tanggal"])). "' ORDER BY bookinghosp_urutan DESC LIMIT 1";
        
        $qurutan = $this->conn2->query($sqlurutan);
        $urutan = 0;
        if($qurutan){
            $urutan = mysqli_fetch_object($qurutan);
            $urutan = $urutan->urutan;
        }
        
        $pesan_jam = explode(" - ", $data["pesan_jam"]);
        $field = ["pasien_norm", "pasien_nama", "pegawai_kode", "pegawai_nama", "bookinghosp_tanggal", "bookinghosp_jammulai", "bookinghosp_jamselesai", "layanan_kode", "pasien_email", "pasien_telp", "bookinghosp_urutan", "bookinghosp_created_by", "bookinghosp_created_date"];
        $dataSave = [$data["pasien_norm"], $data["pasien_nama"], $data["m_pegawai_kode"], $data["m_pegawai_nama"], date("Y-m-d", strtotime($data["pesan_tanggal"])), $pesan_jam[0], $pesan_jam[1], "TM001", $data["pasien_email"], $data["pasien_tlp"], ($urutan+1), "PASIEN", date("Y-m-d H:i:s")];
        $action = query_create($this->conn2, 't_booking_hospital', $field, $dataSave);
        
        $pasiennorm = "3172724" . str_pad($data["pasien_norm"], 8, "0", STR_PAD_LEFT);

        $sqltarif = "SELECT * FROM m_tarif_detail WHERE FS_KD_SMF = '". $data["kode_smf"]. "' AND tarifdet_aktif = 'Y'";
        $qtarif = $this->conn2->query($sqltarif);
        if($qtarif) {
            $number =  random_int(100, 1000); // membuat kode unik angka yg akan ditambahkan pada total tagihan
            foreach($qtarif as $key => $val){
                $fieldtrf = ["tagihanpasien_tanggal", "pasien_norm", "tagihanpasien_kodeunik", "t_bookinghosp_id", "fs_kd_tarif", "tagihanpasien_total", "tagihanpasien_created_by", "tagihanpasien_created_date"];
                $dataSavetrf = [date("Y-m-d", strtotime($data["pesan_tanggal"])), $data["pasien_norm"], $number, $action, $val["FS_KD_TARIF"], $val["tarifdet_total"], "PASIEN", date("Y-m-d H:i:s")];
                query_create($this->conn2, 't_tagihan_pasien', $fieldtrf, $dataSavetrf);
            }
        }
        
        if($action > 0){
            $datares = array(
                "code"  => "200",
                "urutan" => $urutan + 1,
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
        // exit();
    }

    public function nonAktifPendaftaran($pasien_norm)
    {
        $sql = "UPDATE t_booking_hospital SET bookinghosp_status = 'EXPIRED' WHERE pasien_norm = '" . $pasien_norm . "'";
        $this->conn2->query($sql);
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
    case 'expiredbayar' :
        $booking = new C_booking($conn, $conn2);
        $action = $booking->nonAktifPendaftaran($_POST["pasien_norm"]);
        break;
    default:
        $booking->booking();
        break;
}