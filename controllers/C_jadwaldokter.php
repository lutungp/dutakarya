<?php 

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
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

    public function getJadwalDokter()
    {
        $hariIndo = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
        $layanan_kode = "RJ001";
        $resJadwalDokter = $this->model->getJadwalDokter2();
        $dataJadwalDokter = [];
        if($resJadwalDokter){
            while($row = sqlsrv_fetch_array($resJadwalDokter, SQLSRV_FETCH_ASSOC)) {
                $dataJadwalDokter[] = array(
                    "FS_KD_DOKTER" =>  $row["FS_KD_DOKTER"],
                    "FS_NM_PEG" => $row["FS_NM_PEG"],
                    "FS_KD_LAYANAN" => $row["FS_KD_LAYANAN"],
                    "FS_KD_SMF" => $row["FS_KD_SMF"] == null ? '' : $row["FS_KD_SMF"],
                    "FS_NM_SMF" => $row["FS_NM_SMF"] == null ? '' : $row["FS_NM_SMF"],
                    "MINGGU" => $row["1"] == null ? '' : $row["1"],
                    "SENIN" => $row["2"] == null ? '' : $row["2"],
                    "SELASA" => $row["3"] == null ? '' : $row["3"],
                    "RABU" => $row["4"] == null ? '' : $row["4"],
                    "KAMIS" => $row["5"] == null ? '' : $row["5"],
                    "JUMAT" => $row["6"] == null ? '' : $row["6"],
                    "SABTU" => $row["7"] == null ? '' : $row["7"],
                );
            }
        }

        echo json_encode($dataJadwalDokter);
    }

    public function getJmlPasien($tgl, $kodedokter)
    {
        $action = $this->model->getJmlPasien($tgl, $kodedokter);
        return $action;
    }
}

$jadwaldokter = new C_jadwaldokter($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
switch ($action) {
    case 'getjadwaldokter':
        $jadwaldokter->getJadwalDokter();
        break;
    
    case 'getjmlpasien':
        $action = $jadwaldokter->getJmlPasien($_GET["tgl"], $_GET["kodok"]);
        echo $action;
        break;

    default:
        $tanggal = isset($_GET["tanggal"]) ? $_GET["tanggal"] : date("Y-m-d");
        $jadwaldokter->jadwaldokter($tanggal);
        break;
}

