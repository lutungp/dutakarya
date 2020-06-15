<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_gajikaryawan.php';
require_once "../vendor/autoload.php";

class C_gajikaryawan
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    public $model = false;
    private $mpdf = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_gajikaryawan($conn, $conn2, $config);
        $this->mpdf = new \Mpdf\Mpdf();
    }

    public function getGajiKaryawan($data)
    {
        $limit = $data["limit"];
        $offset = $data["offset"];
        $username = $_SESSION['USERNAME'];
        $datagaji = $this->model->getGajiKaryawan($offset, $limit, $username);

        echo json_encode($datagaji);
    }

    public function exportPdf($data)
    {
        $username = $_SESSION['USERNAME'];
        $gaji_id = $data['id'];
        $user = $this->model->getDataGaji($gaji_id);

        $content = '<html>';
        $content .= '</html>';
        $content .= '<html>';
        $content .= '<header>';
        $content .= '<style>';
        $content .= 'body{margin:0;}';
        $content .= '.koprshaji {font-family:Arial; font-weight: bold; color: #0b4848;}
                     .header-title {font-family:Arial; font-weight: bold; text-align: center; font-size: 16px;}
                     span.cls_002{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_002{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_003{font-family:"Calibri Bold",serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
div.cls_003{font-family:"Calibri Bold",serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
div.cls_005{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
            .tablegaji {
                margin-top : 10px; 
                border : 1px solid;
                width: 100%;
                cellspacing : 0;
                cellpadding : 0;
                font-family:Arial,serif;
                font-size:11px;
                color:rgb(0,0,0);
                font-weight:normal;
                font-style:normal;
                text-decoration: none;
                border-collapse: collapse;
            }
            
            .center {
                text-align : center;
            }
            
            .colheader {
                border: 1px solid black;
            }';
        $content .= '</style>';
        $content .= '<title>Gaji Karyawan</title>';
        $content .= '</header>';
        $content .= '<body>';
        $content .= '<table width="100%">';
        $content .= '<tr>';
        $content .= '<td width=25% align=center>';
        $content .= '<img src="../assets/img/RSHAJI-310x310.png"/ height="65px" width="60px">';
        $content .= '<span class=koprshaji><p>RUMAH SAKIT HAJI</p>';
        $content .= '<p>JAKARTA</p></span>';
        $content .= '</td>';
        $content .= '<td class=header-title>STRUK GAJI KARYAWAN RS HAJI JAKARTA';
        $content .= '</td>';
        $content .= '</td>';
        $content .= '<td width=25% align=center>';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '<hr style="height:3px;border-width:0;color:gray;background-color:gray">';
        $content .= '<table width="100%">';
        $content .= '<tr>';
        $content .= '<td align=right width=20%><span class="cls_002">Nama :</span></td>';
        $content .= '<td>'.$user->gaji_nama.'</td>';
        $content .= '<td align=right width=20%><span class="cls_002">UNIT :</span></td>';
        $content .= '<td>'.$user->gaji_unitpeg.'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td align=right width=20%><span class="cls_002">NOPEG :</span></td>';
        $content .= '<td>'.$user->gaji_nopeg.'</td>';
        $content .= '<td align=right width=20%><span class="cls_002">GOL/PANGKAT :</span></td>';
        $content .= '<td>'.$user->gaji_golpangkat_peg.'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td align=right width=20%><span class="cls_002">No. Rekening :</span></td>';
        $content .= '<td>'.$user->gaji_norekening.'</td>';
        $content .= '<td align=right width=20%></td>';
        $content .= '<td></td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '<table class="tablegaji">';
        $content .= '<tr>';
        $content .= '<td class="center colheader" colspan="3">GAJI DAN TUNJANGAN</td>';
        $content .= '<td class="center colheader" colspan="3">INSENTIF</td>';
        $content .= '<td class="center colheader" colspan="6">POTONGAN KARYAWAN</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;">GAJI RSHJ</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_nilai = $user->gaji_nilai > 0 ? number_format($user->gaji_nilai) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_nilai.'</td>';
        $content .= '<td style="padding:5px; width:90px;">Jasa Yan</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_jasalayanan = $user->gaji_jasalayanan > 0 ? number_format($user->gaji_jasalayanan) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_jasalayanan.'</td>';
        $content .= '<td style="padding:5px; width:90px;">Sim Wa</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potsimwa = $user->gaji_potsimwa > 0 ? number_format($user->gaji_potsimwa) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potsimwa.'</td>';
        $content .= '<td style="padding:5px; width:90px;">BPJS TK</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potbpjstk = $user->gaji_potbpjstk > 0 ? number_format($user->gaji_potbpjstk) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potbpjstk.'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;">Kehadiran</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_kehadiran = $user->gaji_kehadiran > 0 ? number_format($user->gaji_kehadiran) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_kehadiran.'</td>';
        $content .= '<td style="padding:5px; width:90px;">Shift</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_shift = $user->gaji_shift > 0 ? number_format($user->gaji_shift) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_shift.'</td>';

        $content .= '<td style="padding:5px; width:90px;">Pot. Koperasi</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potkoperasi = $user->gaji_potkoperasi > 0 ? number_format($user->gaji_potkoperasi) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potkoperasi.'</td>';
        $content .= '<td style="padding:5px; width:90px;">BPJS Pensiun</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potbpjspensiun = $user->gaji_potbpjspensiun > 0 ? number_format($user->gaji_potbpjspensiun) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potbpjspensiun.'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;">Tunj. Transport</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_transport = $user->gaji_transport > 0 ? number_format($user->gaji_transport) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_transport.'</td>';
        $content .= '<td style="padding:5px; width:90px;">On Call</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_oncall = $user->gaji_oncall > 0 ? number_format($user->gaji_oncall) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_oncall.'</td>';

        $content .= '<td style="padding:5px; width:90px;">Pot. Parkir</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potparkir = $user->gaji_potparkir > 0 ? number_format($user->gaji_potparkir) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potparkir.'</td>';
        $content .= '<td style="padding:5px; width:90px;">Pajak</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $gaji_potpajak = $user->gaji_potpajak > 0 ? number_format($user->gaji_potpajak) : "-";
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">'.$gaji_potpajak.'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;">T. Operasional</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Lembur</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">162,594</td>';

        $content .= '<td style="padding:5px; width:90px;">SIMPOK</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Pot. Sekolah</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;">Rapel/MOD</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Uang Cuti</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';

        $content .= '<td style="padding:5px; width:90px;">Pot. Kesehatan</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Pot. Lain-lain</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';

        $content .= '<td style="padding:5px; width:90px;">Pot. Absensi</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Pot. FKK</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '</tr>';
        
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';

        $content .= '<td style="padding:5px; width:90px;">ZIS</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Pot. IBI</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';

        $content .= '<td style="padding:5px; width:90px;">Pot. Qurban</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;">Pot. SP</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';

        $content .= '<td style="padding:5px; width:90px;">Infaq Masjid</td>';
        $content .= '<td style="padding:5px; text-align: center;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;">-</td>';
        $content .= '<td style="padding:5px; width:90px;"></td>';
        $content .= '<td style="padding:5px; text-align: center;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;"></td>';
        $content .= '</tr>';
           
        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:90px;border-top: 1px solid black;">JUMLAH(1)</td>';
        $content .= '<td style="padding:5px; text-align: center;border-top: 1px solid black;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;border-top: 1px solid black;"><b>4,250,500</b></td>';
        $content .= '<td style="padding:5px; width:90px;border-top: 1px solid black;">TOTAL(2)</td>';
        $content .= '<td style="padding:5px; text-align: center;border-top: 1px solid black;">:</td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;border-top: 1px solid black;"><b>497,417</b></td>';

        $content .= '<td style="padding:5px; width:90px;border-top: 1px solid black;"></td>';
        $content .= '<td style="padding:5px; text-align: center;border-top: 1px solid black;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-top: 1px solid black;">TOTAL(3) :</td>';
        $content .= '<td style="padding:5px; width:90px;border-top: 1px solid black;"><b>234,045</b></td>';
        $content .= '<td style="padding:5px; text-align: left;border-top: 1px solid black;"></td>';
        $content .= '<td style="text-align:right;padding:5px;border-right:1px solid;border-top: 1px solid black;"></td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:40px;border-top: 1px solid black;text-align: right;" colspan="3" rowspan="3">
                        <p><b>THR TAHUN 2020&nbsp;&nbsp;:</p></td>';
        $content .= '<td style="padding:5px; width:50px;border-top: 1px solid black;border-right: 1px solid black;text-align: right;" colspan="3" rowspan="3">
                    <b>2,120,000</b></td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px; width:40px;border-top: 1px solid black;text-align: right;" colspan="3">
                        <p><b>TOTAL TERIMA (1+2)-3&nbsp;&nbsp;:</p></td>';
        $content .= '<td style="padding:5px; width:50px;border-top: 1px solid black;border-right: 1px solid black;text-align: right;" colspan="3">
                    <b>4,513,872</b></td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;">Saldo Simwa</td>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;">:</td>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;text-align:right;">-</td>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;">Saldo Simpok</td>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;">:</td>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;text-align:right;">-</td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td style="padding:5px;border-top: 1px solid black;" colspan="12">
                    "Sesungguhnya jika kamu bersyukur, pasti Kami akan menambah (nikmat) kepadamu, dan jika kamu mengingkari (nikmat-Ku). Maka Sesungguhnya azab-Ku sangat pedih"</td>';
        $content .= '</tr>';

        $content .= '</table>';
        $content .= '</body>';
        $content .= '</html>';
        
        $this->mpdf->AddPage("L","","","","","5","5","5","5","","","","","","","","","","","","B5");
        $this->mpdf->WriteHTML($content);
        $this->mpdf->Output();
    }

}

$antrianbooking = new C_gajikaryawan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'exportpdf':
        $antrianbooking->exportPdf($_GET);
        break;
    case 'getgajikaryawan':
        $antrianbooking->getGajiKaryawan($_POST);
        break;
    default:
        templateAdmin($conn2, '../views/v_gajikaryawan.php', $data = "", $active1 = "INFO", $active2 = "INFO GAJI KARYAWAN");
        break;
}

