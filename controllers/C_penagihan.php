<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_penagihan.php';

class C_penagihan
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

        $this->model = new M_penagihan($conn, $conn2, $config);
    }

    public function getPenagihan()
    {
        $data = $this->model->getPenagihan();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $penagihan_id = $data['id'];
            $result['penagihan_id'] = $penagihan_id;
            $result['datapenagihan'] = $this->model->getPenagihanData($penagihan_id);
            $result['datapenagihandetail'] = $this->model->getPenagihanDataDetail($penagihan_id);
        }
        
        templateAdmin($this->conn2, '../views/penagihan/v_formpenagihan.php', json_encode($result), 'KEUANGAN', 'PENAGIHAN');
    }

    public function getPengiriman($data)
    {
        $m_rekanan_id = $data['m_rekanan_id'];
        $penagihan_tgl = $data['penagihan_tgl'];
        $data = $this->model->getPengiriman($m_rekanan_id, $penagihan_tgl);

        echo json_encode($data);
    }

    public function getSewa($data)
    {
        $m_rekanan_id = $data['m_rekanan_id'];
        $penagihan_tgl = $data['penagihan_tgl'];
        $day = date('N', strtotime($penagihan_tgl));
        $week = getWeeks($penagihan_tgl, $day) - 1;
        $month = intval(date('m', strtotime($penagihan_tgl)));
        $year = intval(date('Y', strtotime($penagihan_tgl)));
        $data = $this->model->getSewa($m_rekanan_id, $month, $year, $penagihan_tgl);

        echo json_encode($data);
    }

    public function submit($data)
    {
        $penagihan_id = $data['penagihan_id'];
        $penagihan_no = $data['penagihan_no'];
        $penagihan_tgl = $data['penagihan_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];
        $action = false;
        if ($penagihan_id > 0) {
            $fieldSave = ['penagihan_tgl', 'm_rekanan_id', 'penagihan_updated_by', 'penagihan_updated_date', 'penagihan_revised'];
            $dataSave = [$penagihan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'penagihan_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE penagihan_id = " . $data['penagihan_id'];
            $action = query_update($this->conn2, 't_penagihan', $field, $where);
        } else {
            $penagihan_no = getPenomoran($this->conn2, 'INV', 't_penagihan', 'penagihan_id', 'penagihan_no', $penagihan_tgl);
            $fieldSave = ['penagihan_no', 'penagihan_tgl', 'm_rekanan_id', 'penagihan_created_by', 'penagihan_created_date'];
            $dataSave = [$penagihan_no, $penagihan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $penagihan_id = query_create($this->conn2, 't_penagihan', $fieldSave, $dataSave);
        }
        
        if (isset($data['rows'])) {
            $pengiriman_idArr = [];
            foreach ($data['rows'] as $key => $val) {
                $penagihandet_id = $val['penagihandet_id'];
                if ($penagihandet_id > 0) {
                    $fieldSave = ['t_penagihan_id', 't_pengiriman_id', 't_pengirimandet_id', 'penagihandet_harga', 'penagihandet_subtotal', 'penagihandet_ppn', 'penagihandet_potongan', 'penagihandet_total', 'penagihandet_updated_by', 'penagihandet_updated_date', 'penagihandet_revised'];
                    $dataSave = [$penagihan_id, $val['t_pengiriman_id'], $val['t_pengirimandet_id'], $val['penagihandet_harga'], $val['penagihandet_subtotal'], $val['penagihandet_ppn'], $val['penagihandet_potongan'], $val['penagihandet_total'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'penagihandet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE penagihandet_id = " . $val['penagihandet_id'];
                    query_update($this->conn2, 't_penagihan_detail', $field, $where);
                } else {
                    $fieldSave = ['t_penagihan_id', 't_pengiriman_id', 't_pengirimandet_id', 'penagihandet_harga', 'penagihandet_subtotal', 'penagihandet_ppn', 'penagihandet_potongan', 'penagihandet_total', 'penagihandet_created_by', 'penagihandet_created_date'];
                    $dataSave = [$penagihan_id, $val['t_pengiriman_id'], $val['t_pengirimandet_id'], $val['penagihandet_harga'], $val['penagihandet_subtotal'], $val['penagihandet_ppn'], $val['penagihandet_potongan'], $val['penagihandet_total'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $action = query_create($this->conn2, 't_penagihan_detail', $fieldSave, $dataSave);
                }


                /* update pengiriman status penagihan */
                array_push($pengiriman_idArr, $val['t_pengiriman_id']);
            }
            
            $this->updateStatusPenagihan($pengiriman_idArr, $penagihan_id, $penagihan_no, 'Y');
        }

        $return["code"] = "202";

        if ($action > 0) {
            $return["code"] = "200";
            $return["id"] = $penagihan_id;
        }

        echo json_encode($return);
    }

    public function updateStatusPenagihan($pengiriman_idArr, $penagihan_id, $penagihan_no, $tagih)
    {
        if ($tagih == 'Y') {
            $fieldSave = ['t_penagihan_id', 't_penagihan_no'];
            $dataSave = [$penagihan_id, $penagihan_no];
        } else {
            $fieldSave = ['t_penagihan_id', 't_penagihan_no'];
            $dataSave = [NULL, NULL];
        }

        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $pengiriman_id = implode(',', $pengiriman_idArr);
        $where = "WHERE pengiriman_id IN (" . $pengiriman_id . ")";
        query_update($this->conn2, 't_pengiriman', $field, $where);
    }

    public function batal($data)
    {
        $fieldSave = ['penagihan_aktif', 'penagihan_void_by', 'penagihan_void_date', 'penagihan_void_alasan'];
        $dataSave = ['N', $_SESSION['USER_ID'], date("Y-m-d H:i:s"), $data['alasan']];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }

        $where = "WHERE penagihan_id = " . $data['penagihan_id'];
        $action = query_update($this->conn2, 't_penagihan', $field, $where);
        
        $fieldSave = ['t_penagihan_id', 't_penagihan_no'];
        $dataSave = [NULL, NULL];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $pengiriman_idArr = implode(',', $data['pengiriman_idArr']);
        $where = "WHERE pengiriman_id IN (" . $pengiriman_idArr . ")";
        query_update($this->conn2, 't_pengiriman', $field, $where);

        if ($action > 0) {
            echo 200;
        } else {
            echo 202;
        }
    }

    public function exportpdf($data)
    {
        require_once "../vendor/autoload.php";
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 148.5]]);
        $penagihan_id = $data['id'];
        $datapenagihan = $this->model->getPenagihanData2($penagihan_id);
        $datapenagihandetail = $this->model->getPenagihanDataDetail($penagihan_id);
        
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
        $content .= '<td style="vertical-align:top;text-align:center;width: 40%;font-size:13px;">FAKTUR PENJUALAN/KUITANSI</td>';
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
        $content .= $datapenagihan->penagihan_no;
        $content .= '<br>' . date('d-m-Y', strtotime($datapenagihan->penagihan_tgl));
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
        $content .= ': ' . $datapenagihan->rekanan_kode . '<br>';
        $content .= ': ' . $datapenagihan->rekanan_nama . '<br>';
        $rekanan_alamat = str_replace('\n', '<br />', $datapengiriman->rekanan_alamat);
        $content .= ': ' . $rekanan_alamat . '<br>';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '<table class="pengiriman">';
        $content .= '<tr>';
        $content .= '<td style="text-align:center; border:1px solid;">TANGGAL</td>';
        $content .= '<td style="text-align:center; border:1px solid;">NO.<br>PENGIRIMAN</td>';
        $content .= '<td style="text-align:center; border:1px solid;">NAMA<br>BARANG</td>';
        $content .= '<td style="text-align:center; border:1px solid;">JUMLAH</td>';
        $content .= '<td style="text-align:center; border:1px solid;">HARGA SATUAN</td>';
        $content .= '<td style="text-align:center; border:1px solid;">TOTAL</td>';
        $content .= '<td style="text-align:center; border:1px solid;">KETERANGAN</td>';
        $content .= '</tr>';
        $total = 0;
        $ppn = 0;
        foreach ($datapenagihandetail as $key => $val) {
            $content .= '<tr>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 100px;">'.date('d-m-Y', strtotime($val['pengiriman_tgl'])).'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 120px;">'.$val['pengiriman_no'].'</td>';
            $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px; width: 220px;">'.$val['barang_nama'].'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;">'.($val['penagihandet_qtyreal'] - $val['t_returdet_qty']).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 100px;">'.number_format($val['pengirimandet_harga']).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 100px;">'.number_format($val['pengirimandet_harga']*($val['penagihandet_qtyreal'] - $val['t_returdet_qty'])).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;"></td>';
            $content .= '</tr>';
            $total = $total + ($val['pengirimandet_harga']*($val['penagihandet_qtyreal'] - $val['t_returdet_qty']));
            $ppn = $ppn + $val['penagihandet_ppn'];
        }
        $content .= '<tr>';
        $tanggalindo = tgl_indo($datapenagihan->penagihan_tgl);
        $content .= '<td style="text-align:left; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 10px;" colspan="3" rowspan="2">
                        Catatan :
                        <br>
                        <br>
                        Pembayaran transfer mohon untuk dikirimkan ke No. Rekening 006 311 8998 Bank BCA KCU SCBD - EQUITY TOWER JL. JEND SUDIRMAN KAV. 52 - 53 A/N PT. DUTAKARYA YASHA
                    </td>
                    <td style="text-align:left; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 5px;" colspan="2">
                        JUMLAH<br>
                        PPN
                    </td>
                    <td style="text-align:right; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 5px;">
                        '.number_format($total).'<br>'.number_format($ppn).'
                    </td>
                    <td style="text-align:center; border-top:1px solid; border-left:1px solid; border-right:1px solid; padding: 5px; vertical-align: top;" rowspan="3">
                    <p>' . $tanggalindo . '</p>
                    </td>';
        $content .= '</tr>';
        $content .= '<tr>
                        <td style="border-top:1px solid;border-right:1px solid;" colspan="2">TOTAL PEMBAYARAN</td>
                        <td style="border-top:1px solid;border-right:1px solid;text-align:right;">
                        '.number_format($total+$ppn).'
                        </td>
                    </tr>';

        $terbilang = terbilang($total);
        $content .= '<tr>
                        <td style="border-top:1px solid;border-right:1px solid;padding: 10px;" colspan="6">
                        <p>TERBILANG : </p>
                        <br>
                        <p><font style="font-size: 14px;"><b>'.ucfirst($terbilang).'</b></font></p>
                        </td>
                    </tr>';
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

$penagihan = new C_penagihan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'submit':
        $penagihan->submit($_POST);
        break;
    case 'formtransaksi':
        $penagihan->formTransaksi($_GET);
        break;
    case 'getpengiriman':
        $penagihan->getPengiriman($_POST);
        break;
    case 'getsewa':
        $penagihan->getSewa($_POST);
        break;
    case 'getpenagihan':
        $penagihan->getPenagihan();
        break;
    case 'exportpdf':
        $penagihan->exportpdf($_GET);
        break;
    case 'batal':
        $penagihan->batal($_POST);
        break;
    default:
        templateAdmin($conn2, '../views/penagihan/v_penagihan.php', NULL, 'KEUANGAN', 'PENAGIHAN');
    break;
}
