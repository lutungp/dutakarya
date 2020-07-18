<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_pelunasan.php';

class C_pelunasan
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

        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "./controllers/C_login");
        }

        $this->model = new M_pelunasan($conn, $conn2, $config);
    }

    public function getPelunasan()
    {
        $data = $this->model->getPelunasan();

        echo json_encode($data);
    }

    public function formTransaksi($data)
    {    
        $result = NULL;
        $pelunasan_id = isset($data['id']) ? $data['id'] : 0;
        if ($pelunasan_id > 0) {
            $result['datapelunasan'] = $this->model->getDataPelunasan($pelunasan_id);
            $result['datapelunasandetail'] = $this->model->getDataPelunasanDetail($pelunasan_id);
        }
        
        templateAdmin($this->conn2, '../views/pelunasan/v_formpelunasan.php', json_encode($result), 'KEUANGAN', 'PELUNASAN');
    }

    public function getPenagihan($data)
    {
        $m_rekanan_id = $data['m_rekanan_id'];
        $pelunasan_tgl = isset($data['pelunasan_tgl']) ? date('Y-m-d', strtotime($data['pelunasan_tgl'])) : '';
        $data = $this->model->getPenagihan($m_rekanan_id, $pelunasan_tgl);

        echo json_encode($data);
    }

    public function submit($data)
    {
        $pelunasan_id = $data['pelunasan_id'];
        $pelunasan_no = $data['pelunasan_no'];
        $pelunasan_tgl = $data['pelunasan_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];
        $action = false;
        if ($pelunasan_id > 0) {
            $fieldSave = ['pelunasan_tgl', 'm_rekanan_id', 'pelunasan_updated_by', 'pelunasan_updated_date', 'pelunasan_revised'];
            $dataSave = [$pelunasan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'pelunasan_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE pelunasan_id = " . $data['pelunasan_id'];
            $action = query_update($this->conn2, 't_pelunasan', $field, $where);
        } else {
            $pelunasan_no = getPenomoran($this->conn2, 'KW', 't_pelunasan', 'pelunasan_id', 'pelunasan_no', $pelunasan_tgl);
            $fieldSave = ['pelunasan_no', 'pelunasan_tgl', 'm_rekanan_id', 'pelunasan_created_by', 'pelunasan_created_date'];
            $dataSave = [$pelunasan_no, $pelunasan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $pelunasan_id = query_create($this->conn2, 't_pelunasan', $fieldSave, $dataSave);
        }

        if (isset($data['rows'])) {
            $penagihanArr = [];
            foreach ($data['rows'] as $key => $val) {
                $pelunasandet_id = $val['pelunasandet_id'];
                if ($pelunasandet_id > 0) {
                    $fieldSave = ['t_pelunasan_id', 't_penagihan_id', 'pelunasandet_tagihan', 'pelunasandet_bayar', 'pelunasandet_updated_by', 'pelunasandet_updated_date', 'pelunasandet_revised'];
                    $dataSave = [$pelunasan_id, $val['t_penagihan_id'], $val['pelunasandet_tagihan'], $val['pelunasandet_bayar'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'pelunasandet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE pelunasandet_id = " . $val['pelunasandet_id'];
                    $action = query_update($this->conn2, 't_pelunasan_detail', $field, $where);
                } else {
                    $fieldSave = ['t_pelunasan_id', 't_penagihan_id', 'pelunasandet_tagihan', 'pelunasandet_bayar', 'pelunasandet_created_by', 'pelunasandet_created_date'];
                    $dataSave = [$pelunasan_id, $val['t_penagihan_id'], $val['pelunasandet_tagihan'], $val['pelunasandet_bayar'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $action = query_create($this->conn2, 't_pelunasan_detail', $fieldSave, $dataSave);
                }


                /* update penagihan status penagihan */
                $penagihanArr[] = array(
                    't_penagihan_id' => $val['t_penagihan_id'],
                    'operator' => $val['pelunasandet_bayarold'] < $val['pelunasandet_bayar'] ? '+' : '-',
                    'pelunasandet_bayar' => $val['pelunasandet_bayar']
                );
            }
            $this->updateStatusPenagihan($penagihanArr);
        }

        if ($action) {
            $result['code'] = 200;
            $result['id'] = $pelunasan_id;
        } else {
            $result['code'] = 202;
        }

        echo json_encode($result);
    }

    public function updateStatusPenagihan($penagihanArr)
    {
        $fieldSave = ['t_pelunasandet_bayar'];
        foreach ($penagihanArr as $key => $valpenagihan) {
            $dataSave = ['t_pelunasandet_bayar'.$valpenagihan['operator'].$valpenagihan['pelunasandet_bayar']];
            $field = '';
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
            $where = "WHERE penagihan_id = " . $valpenagihan['t_penagihan_id'];
            query_update($this->conn2, 't_penagihan', $field, $where);
        }
    }

    public function batal($data)
    {
        $pelunasan_id = $data['pelunasan_id'];
        $fieldSave = ['pelunasan_aktif', 'pelunasan_void_by', 'pelunasan_void_date', 'pelunasan_void_alasan'];
        $dataSave = ['N', $_SESSION["USER_ID"], date("Y-m-d H:i:s"), $data['alasan']];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE pelunasan_id = " . $pelunasan_id;
        $action = query_update($this->conn2, 't_pelunasan', $field, $where);

        $fieldSave = ['pelunasandet_aktif'];
        $dataSave = ['N'];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE t_pelunasan_id = " . $pelunasan_id;
        $action = query_update($this->conn2, 't_pelunasan_detail', $field, $where);

        $fieldSave = ['t_pelunasandet_bayar'];
        foreach ($data['rows'] as $key => $valpenagihan) {
            $dataSave = ['t_pelunasandet_bayar-'.$valpenagihan['pelunasandet_bayar']];
            $field = '';
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
            $where = "WHERE penagihan_id = " . $valpenagihan['t_penagihan_id'];
            query_update($this->conn2, 't_penagihan', $field, $where);
        }

        if ($action) {
            $result['code'] = 200;
            // $result['id'] = $pelunasan_id;
        } else {
            $result['code'] = 202;
        }

        echo json_encode($result);
    }
    
    public function exportpdf($data)
    {
        require_once "../vendor/autoload.php";
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 148.5]]);
        $pelunasan_id = $data['id'];
        $datapelunasan = $this->model->getDataPelunasan2($pelunasan_id);
        $datapelunasandetail = $this->model->getDataPelunasanDetail($pelunasan_id);

        $content = '<html>';
        $content .= '<header>';
        $content .= '<title>DUTA KARYA';
        $content .= '</title>';
        $content .= '<style>';
        $content .= 'html, body {font-size: 12px; font-family : Helvetica;} 
                    .pengiriman {
                        margin-top : 10px; 
                        border : 1px solid;
                        width: 100%;
                        cellspacing : 0;
                        cellpadding : 0;
                        font-family:Helvetica,serif;
                        font-size:12px;
                        color:rgb(0,0,0);
                        font-weight:normal;
                        font-style:normal;
                        text-decoration: none;
                        border-collapse: collapse;
                    }
                    .assign {
                        width : 100%;
                    }
                    ';
        $content .= '</style>';
        $content .= '</header>';
        $content .= '<body>';
        $content .= '<table width="100%">';
        $content .= '<tr>';
        $content .= '<td style="width: 30%;font-size:13px;">PT. DUTAKARYA YASHA<br>JL. DEWI SARTIKA 312 CAWANG JAKARTA TIMUR 13650<br>TELP. (021) 22801922&nbsp;&nbsp;&nbsp;&nbsp;<br>CS&nbsp;:&nbsp;08111189888<br>NPWP&nbsp;:&nbsp;0 3 0 2 2 4 3 8 5 9 0 0 8 0 0</td>';
        $content .= '<td style="vertical-align:top;text-align:center;width: 40%;font-size:13px;">KUITANSI PEMBAYARAN</td>';
        $content .= '<td style="vertical-align:top;text-align:left;width: 10%;">';
        $content .= 'Nomor';
        $content .= '<br>Tanggal';
        $content .= '<br>Halaman';
        $content .= '</td>';
        $content .= '<td style="vertical-align:top;text-align:left;width: 2%;">';
        $content .= ':';
        $content .= '<br>:';
        $content .= '<br>:';
        $content .= '</td>';
        $content .= '<td style="vertical-align:top;text-align:right;width: 18%;">';
        $content .= $datapelunasan->pelunasan_no;
        $content .= '<br>' . date('d-m-Y', strtotime($datapelunasan->pelunasan_tgl));
        $content .= '<br>1';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '<hr>';
        $content .= '<div style="padding-left:50px;padding-right:50px;">';
        $content .= '<table width="50%">';
        $content .= '<tr>';
        $content .= '<td style="vertical-align:top;">';
        $content .= 'NO. PELANGGAN<br>';
        $content .= 'NAMA. PELANGGAN<br>';
        $content .= 'ALAMAT<br>';
        $content .= '</td>';
        $content .= '<td style="vertical-align:top;">';
        $content .= ': ' . $datapelunasan->rekanan_kode . '<br>';
        $content .= ': ' . $datapelunasan->rekanan_nama . '<br>';
        $rekanan_alamat = str_replace('\n', '<br />', $datapelunasan->rekanan_alamat);
        $content .= ': ' . $rekanan_alamat . '<br>';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '<table class="pengiriman">';
        $content .= '<tr>';
        $content .= '<td style="text-align:center; border:1px solid; padding: 5px;;">TANGGAL</td>';
        $content .= '<td style="text-align:center; border:1px solid; padding: 5px;;">NO. PENAGIHAN</td>';
        $content .= '<td style="text-align:center; border:1px solid; padding: 5px;;">DIBAYAR</td>';
        $content .= '</tr>';
        $total = 0;
        $ppn = 0;
        foreach ($datapelunasandetail as $key => $val) {
            $content .= '<tr>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 15%;">'.date('d-m-Y', strtotime($val['penagihan_tgl'])).'</td>';
            $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px;">'.$val['penagihan_no'].'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 30%;">'.number_format($val['pelunasandet_bayar']).'</td>';
            $content .= '</tr>';
            $total = $total + $val['pelunasandet_bayar'];
        }
        $content .= '<tr>';
        $tanggalindo = tgl_indo($datapelunasan->pelunasan_tgl);
        $terbilang = terbilang($total);
        $content .= '<td style="text-align:right; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 10px;" colspan="2">
                        Total :
                     </td>';
        $content .= '<td style="text-align:right; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 10px;">'.number_format($total).'</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="text-align:left; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 10px;" colspan="3">
                        Telah Terbayar :
                        <br>
                        <br>
                        <p><font style="font-size: 14px;"><b>'.ucfirst($terbilang).'</b></font></p>
                     </td>';
        $content .= '</tr>';

        
        // $content .= '<tr>
        //                 <td style="border-top:1px solid;border-right:1px solid;padding: 10px;" colspan="6">
        //                 <p>TERBILANG : </p>
        //                 <br>
        //                 <p><font style="font-size: 14px;"><b>'.ucfirst($terbilang).'</b></font></p>
        //                 </td>
        //             </tr>';
        $content .= '</table>';
        
        // $content .= '<div style="padding-left: 10px;padding-right: 10px; padding-top: 50px">';
        // $content .= '<table class="pengiriman">';
        // $content .= '<tr>';
        // $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px;" colspan="3">
        //                 Catatan :
        //             </td>';
        // $content .= '</tr>';
        // $content .= '</table>';
        $content .= '</div>';

        $content .= '</body>';
        $content .= '</html>';
        $mpdf->AddPage("P","","","","","5","5","5","5","","","","","","","","","","","");
        $mpdf->WriteHTML($content);
        $mpdf->Output();
    }
}

$pelunasan = new C_pelunasan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getpelunasan':
        $pelunasan->getPelunasan();
        break;
    case 'formtransaksi':
        $pelunasan->formTransaksi($_GET);
        break;
    case 'getpenagihan':
        $pelunasan->getPenagihan($_POST);
        break;
    case 'submit':
        $pelunasan->submit($_POST);
        break;
    case 'batal':
        $pelunasan->batal($_POST);
        break;
    case 'exportpdf':
        $pelunasan->exportpdf($_GET);
        break;
    default:
        templateAdmin($conn2, '../views/pelunasan/v_pelunasan.php', NULL, 'KEUANGAN', 'PELUNASAN');
    break;
}

