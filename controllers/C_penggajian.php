<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_penggajian.php';

require_once "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class C_penggajian
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
        $this->model = new M_penggajian($conn, $conn2, $config);

        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "./controllers/C_login.php");
        }
    }

    public function getPenggajian($username)
    {
        templateAdmin($this->conn2, '../views/v_penggajian.php');
    }

    public function getDataPenggajian($data)
    {
        $bulan = $data["bulan"];
        $tahun = $data["tahun"];
        $sql = $this->model->getDataPenggajian($bulan, $tahun);
        echo json_encode($sql);
    }

    public function importGaji($data, $dataget)
    {
        $file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        if(isset($_FILES['importexcel']['name']) && in_array($_FILES['importexcel']['type'], $file_mimes)) {
         
            $arr_file = explode('.', $_FILES['importexcel']['name']);
            $extension = end($arr_file);
         
            if('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
         
            $spreadsheet = $reader->load($_FILES['importexcel']['tmp_name']);
             
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $bulan = [
                '' => 0,
                'JANUARI' => 1,
                'FEBRUARI' => 2,
                'MARET' => 3,
                'APRIL' => 4,
                'MEI' => 5,
                'JUNI' => 6,
                'JULI' => 7,
                'AGUSTUS' => 8,
                'SEPTEMBER' => 9,
                'OKTOBER' => 10,
                'NOVEMBER' => 11,
                'DESEMBER' => 12,
            ];
            /* validasi bulan */
            $validasi = $this->model->validasiGaji($bulan[$dataget["bulan"]], $dataget["tahun"]);
            $lewati = isset($dataget["lewati"]) ? true : false;
            if ($validasi == "sudah ada") {
                
                echo "202";
            }
            exit();
            for($i = 1;$i < count($sheetData);$i++) {
                $j = 0;

                $sql = "SELECT user_id FROM m_user WHERE m_user.user_pegawai = '".$sheetData[$i][$j++]."'";
                $qpeg = $this->conn2->query($sql);
                $rpeg = $qpeg->fetch_object();
                $field = [ "gaji_bulan", "gaji_tahun", "m_user_id", "gaji_nama", "gaji_nopeg", "gaji_unitpeg", "gaji_golpangkat_peg", "gaji_norekening", "gaji_pokok", "gaji_kehadiran", "gaji_transport", "gaji_operasional", "gaji_rapel", "gaji_jasalayanan", "gaji_shift", "gaji_lembur", "gaji_oncall", "gaji_uangcuti", "gaji_potsimwa", "gaji_potkoperasi", "gaji_potparkir", "gaji_potsimpok", "gaji_potkesehatan", "gaji_potabsensi", "gaji_potzis", "gaji_potqurban", "gaji_potinfaqmasjid", "gaji_potbpjstk", "gaji_potbpjspensiun", "gaji_potpajak", "gaji_potsekolah", "gaji_potlain", "gaji_potfkk", "gaji_potsp", "gaji_potibi", "gaji_nilai", "gaji_insentif", "gaji_potongan", "gaji_diterima"];
                $dataSave = [ $bulan[$dataget["bulan"]], $dataget["tahun"], $rpeg->user_id, $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++],
                    $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++],
                    $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++],
                    $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++],
                    $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], $sheetData[$i][$j++], 
                    $sheetData[$i][$j++], $sheetData[$i][$j++]];
                if ($rpeg->user_id > 0) {
                    query_create($this->conn2, 't_gaji', $field, $dataSave);
                }
            }
        }
    }

}

$username = $_SESSION["USERNAME"];
$profile = new C_penggajian($conn, $conn2, $username);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getdatapenggajian':
        $profile->getDataPenggajian($_GET);
        break;
    case 'importgaji':
        $profile->importGaji($_POST, $_GET);
        break;
    default:
        $profile->getPenggajian($username);
    break;
}