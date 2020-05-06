<?php 

include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';
include '../models/M_jadwaldokter.php';

class C_jadwaldokter
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
        $this->model = new M_jadwaldokter($conn, $conn2, $config);
    }

    public function jadwaldokter($tanggal)
    {
        $hariIndo = ["SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU", "MINGGU"];
        $layanan_kode = "RJ001";
        $resJadwalDokter = $this->model->getJadwalDokter($layanan_kode, $tanggal);
        $dataJadwalDokter = [];
        if($resJadwalDokter){
            while($row = sqlsrv_fetch_array($resJadwalDokter, SQLSRV_FETCH_ASSOC)) {
                $hari = (integer)$row["FN_HARI"];
                $dataJadwalDokter[] = array(
                    "FS_KD_DOKTER" =>  $row["FS_KD_DOKTER"],
                    "FS_NM_PEG" => $row["FS_NM_PEG"],
                    "FS_JAM_MULAI" => $row["FS_JAM_MULAI"],
                    "FS_JAM_SELESAI" => $row["FS_JAM_SELESAI"],
                    "FS_KD_LAYANAN" => $row["FS_KD_LAYANAN"],
                    "FN_HARI" => $hariIndo[$hari-1],
                );
            }
        }
        
        template('../views/v_jadwaldokter.php', $dataJadwalDokter);
    }
}

$jadwaldokter = new C_jadwaldokter($conn, $conn2, $config);
$tanggal = isset($_GET["tanggal"]) ? $_GET["tanggal"] : date("Y-m-d");
$jadwaldokter->jadwaldokter($tanggal);