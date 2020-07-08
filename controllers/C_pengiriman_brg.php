<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_pengiriman_brg.php';

class C_pengiriman_brg
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

        $this->model = new M_pengiriman_brg($conn, $conn2, $config);
    }

    public function Pengiriman()
    {
        templateAdmin($this->conn2, '../views/pengirimanbrg/v_pengiriman_brg.php', NULL, 'TRANSAKSI', 'PENGIRIMAN BARANG');
    }

    public function getPengiriman()
    {
        $data = $this->model->getPengiriman();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $pengiriman_id = $data['id'];
            $result['pengiriman_id'] = $pengiriman_id;
            $result['datapengiriman'] = $this->model->getPengirimanData($pengiriman_id);
            $result['datapengirimandetail'] = $this->model->getPengirimanDataDetail($pengiriman_id);
        }

        templateAdmin($this->conn2, '../views/pengirimanbrg/v_formpengiriman_brg.php', json_encode($result), 'TRANSAKSI', 'PENGIRIMAN BARANG');
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $result = $this->model->getRekanan($search);
        echo json_encode($result);
    }

    public function getBarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function simpanPengiriman($data)
    {
        $pengiriman_tgl = date('Y-m-d', strtotime($data['pengiriman_tgl']));
        $pengiriman_id = $data['pengiriman_id'];
        $pengiriman_no = $data['pengiriman_no'];

        if ($pengiriman_id > 0) {
            $fieldSave = ['pengiriman_tgl', 'm_rekanan_id', 'pengiriman_updated_by', 'pengiriman_updated_date', 'pengiriman_revised'];
            $dataSave = [$pengiriman_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'pengiriman_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE pengiriman_id = " . $data['pengiriman_id'];
            query_update($this->conn2, 't_pengiriman', $field, $where);
        } else {
            $pengiriman_no = getPenomoran($this->conn2, 'KM', 't_pengiriman', 'pengiriman_id', 'pengiriman_no', $pengiriman_tgl);
            $fieldSave = ['pengiriman_no', 'pengiriman_tgl', 'm_rekanan_id', 'pengiriman_created_by', 'pengiriman_created_date'];
            $dataSave = [$pengiriman_no, $pengiriman_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $pengiriman_id = query_create($this->conn2, 't_pengiriman', $fieldSave, $dataSave);
        }
        /* nonaktif detail terlebih dahulu */
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail'], $pengiriman_no, $pengiriman_tgl);
        }
        
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $pengirimandet_id = $val['pengirimandet_id'];
                if ($pengirimandet_id > 0) {
                    $fieldSave = ['t_pengiriman_id', 'm_barang_id', 'm_satuan_id', 'pengirimandet_qty', 'pengirimandet_harga', 'pengirimandet_subtotal', 'pengirimandet_potongan', 'pengirimandet_total',
                                  'pengirimandet_updated_by', 'pengirimandet_updated_date', 'pengirimandet_revised'];
                    $dataSave = [$pengiriman_id, $val['m_barang_id'], $val['m_satuan_id'], $val['pengirimandet_qty'], $val['pengirimandet_harga'], $val['pengirimandet_subtotal'], $val['pengirimandet_potongan'], $val['pengirimandet_total'],
                                    $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'pengirimandet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE pengirimandet_id = " . $val['pengirimandet_id'];
                    query_update($this->conn2, 't_pengiriman_detail', $field, $where);
                } else {
                    $fieldSave = ['t_pengiriman_id', 'm_barang_id', 'm_satuan_id', 'pengirimandet_qty', 'pengirimandet_harga', 'pengirimandet_subtotal', 'pengirimandet_potongan', 'pengirimandet_total',
                                    'pengirimandet_created_by', 'pengirimandet_created_date'];
                    $dataSave = [$pengiriman_id, $val['m_barang_id'], $val['m_satuan_id'], $val['pengirimandet_qty'], $val['pengirimandet_harga'], $val['pengirimandet_subtotal'], $val['pengirimandet_potongan'], $val['pengirimandet_total'],
                                $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $pengirimandet_id = query_create($this->conn2, 't_pengiriman_detail', $fieldSave, $dataSave);
                }

                $qtyold = $val['pengirimandet_qtyold'] * $val['satkonv_nilai'];
                $qty    = $val['pengirimandet_qty'] * $val['satkonv_nilai'];
                if($qty <> $qtyold) {
                    $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $data['pengiriman_tgl']);
                    $barangtrans_masuk  = 0;
                    $barangtrans_keluar = 0;
                    $barangtrans_jenis = '';
                    $barangtrans_status = '';
                    $barangtrans_akhir = 0;
                    if ($qty > $qtyold) {
                        $barangtrans_keluar = $qty - $qtyold;
                        $barangtrans_jenis = 'PENGIRIMAN BRG';
                        $barangtrans_status = 'KELUAR';
                        $barangtrans_akhir = $barangtrans_awal - $barangtrans_keluar;
                    } else if ($qty < $qtyold) {
                        $barangtrans_masuk = $qtyold - $qty;
                        $barangtrans_jenis = 'PENGURANGAN PENGIRIMAN BRG';
                        $barangtrans_status = 'MASUK';
                        $barangtrans_akhir = $barangtrans_awal + $barangtrans_masuk;
                    }

                    $datastock = array(
                        't_trans_id'      => $pengiriman_id,
                        't_transdet_id'   => $pengirimandet_id,
                        'barangtrans_jenis' => $barangtrans_jenis,
                        'barangtrans_no'  => $pengiriman_no,
                        'barangtrans_tgl' => $pengiriman_tgl,
                        'm_barang_id'  => $val['m_barang_id'],
                        'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                        'm_satuan_id' => $val['m_satuan_id'],
                        'barangtrans_jml' => $val['pengirimandet_qty'],
                        'barangtrans_konv' => $val['satkonv_nilai'],
                        'barangtrans_awal' => $barangtrans_awal,
                        'barangtrans_masuk' => $barangtrans_masuk,
                        'barangtrans_keluar' => $barangtrans_keluar,
                        'barangtrans_akhir' => $barangtrans_akhir,
                        'barangtrans_status' => $barangtrans_status
                    );

                    if (strtotime($pengiriman_tgl) < strtotime(date('Y-m-d'))) {
                        /* jika melakukan backdate */
                        $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                        $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                        $where = "WHERE barangtrans_tgl >= '" . $pengiriman_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                        query_update($this->conn2, 't_barangtrans', $field, $where);
                    }

                    $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                                'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_keluar', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
                    $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                                $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_keluar'], $datastock['barangtrans_akhir'], $barangtrans_status, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);
                }
            }
        }
        
        if ($pengiriman_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function nonaktifdetail($data, $pengiriman_no, $pengiriman_tgl)
    {
        $pengirimandet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($pengirimandet_idArr, $data[$i]['pengirimandet_id']);
        }
        $pengirimandet_id = implode(',', $pengirimandet_idArr);
        $fieldSave = ["pengirimandet_aktif","pengirimandet_void_by", "pengirimandet_void_date"];
        $dataSave = ["N", $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE pengirimandet_id IN (".$pengirimandet_id.")";
        $action = query_update($this->conn2, 't_pengiriman_detail', $field, $where);
        $detail = $this->model->getPengirimanDataDetail2($pengirimandet_id);
        
        foreach ($detail as $key => $val) {
            $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $pengiriman_tgl);
            $barangtrans_masuk = $val['satkonv_nilai'] * $val['pengirimandet_qty'];
            $datastock = array(
                't_trans_id'      => $val['t_pengiriman_id'],
                't_transdet_id'   => $val['pengirimandet_id'],
                'barangtrans_jenis' => 'BTL PENGIRIMAN BRG',
                'barangtrans_no'  => $pengiriman_no,
                'barangtrans_tgl' => $pengiriman_tgl,
                'm_barang_id'  => $val['m_barang_id'],
                'm_barangsatuan_id' => $val['satuanutama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'barangtrans_jml' => $val['pengirimandet_qty'],
                'barangtrans_konv' => $val['satkonv_nilai'],
                'barangtrans_awal' => $barangtrans_awal,
                'barangtrans_masuk' => $barangtrans_masuk,
                'barangtrans_keluar' => 0,
                'barangtrans_akhir' => $barangtrans_awal + $barangtrans_keluar,
                'barangtrans_status' => 'MASUK'
            );
            if (strtotime($pengiriman_tgl) < strtotime(date('Y-m-d'))) {
                /* jika melakukan backdate */
                $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                $field = " barangtrans_awal=barangtrans_awal+$barangtrans_keluar,  barangtrans_akhir=barangtrans_akhir+$barangtrans_keluar ";
                $where = "WHERE barangtrans_tgl >= '" . $pengiriman_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                query_update($this->conn2, 't_barangtrans', $field, $where);
            }
            $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                         'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
            $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                         $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_akhir'], $datastock['barangtrans_status'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);
        }
    }

    public function exportPdf($data)
    {
        require_once "../vendor/autoload.php";
        $mpdf = new \Mpdf\Mpdf();
        $pengiriman_id = $data['id'];
        $datapengiriman = $this->model->getPengirimanData($pengiriman_id);
        $datapengirimandetail = $this->model->getPengirimanDataDetail($pengiriman_id);

        $content = '<html>';
        $content .= '<header>';
        $content .= '<style>';
        $content .= 'html, body {font-size: 12px; font-family : Helvetica;} 
                    .pengiriman {
                        margin-top : 10px; 
                        border : 1px solid;
                        width: 100%;
                        cellspacing : 0;
                        cellpadding : 0;
                        font-family:Helvetica,serif;
                        font-size:11px;
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
        $content .= '<td style="width:50%;">PT. DUTA KARYA<br>JL. DEWI SARTIKA 312 CAWANG JAKARTA<br>TIMUR 13650<br>TELP. (021) 22801922&nbsp;&nbsp;&nbsp;&nbsp;CS 08111189888<br>NPWP&nbsp;&nbsp;&nbsp;&nbsp;0 3 0 2 2 4 3 8 5 9 0 0 8 0 0</td>';
        $content .= '<td style="vertical-align:top;text-align:center;width:50%;">DELIVERY ORDER</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '<hr>';
        $content .= '<div style="padding-left:50px;padding-right:50px;">';
        $content .= '<table width="100%">';
        $content .= '<tr>';
        $content .= '<td style="vertical-align:top;">';
        $content .= 'NO. KONTRAK<br>';
        $content .= 'NO. PELANGGAN<br>';
        $content .= 'NAMA. PELANGGAN<br>';
        $content .= 'ALAMAT<br>';
        $content .= '</td>';
        $content .= '<td style="vertical-align:top;">';
        $content .= ':<br>';
        $content .= ': ' . $datapengiriman->rekanan_kode . '<br>';
        $content .= ': ' . $datapengiriman->rekanan_nama . '<br>';
        $content .= ': ' . $datapengiriman->rekanan_alamat . '<br>';
        $content .= '</td>';
        $content .= '<td style="vertical-align:top;">';
        $content .= 'NO. DO<br>';
        $content .= 'DIBUAT<br>';
        $content .= 'DIKIRIM<br>';
        $content .= 'DRIVER<br>';
        $content .= 'JADWAL<br>';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<td style="vertical-align:top;">';
        $content .= ': ' . $datapengiriman->pengiriman_no . '<br>';
        $content .= ':<br>';
        $content .= ':<br>';
        $content .= ':<br>';
        $content .= ':<br>';
        $content .= '</td>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '<table class="pengiriman">';
        $content .= '<tr>';
        $content .= '<td style="text-align:center; border:1px solid;">PRODUK</td>';
        $content .= '<td style="text-align:center; border:1px solid;">HARGA</td>';
        $content .= '<td style="text-align:center; border:1px solid;">DIKIRIM</td>';
        $content .= '<td style="text-align:center; border:1px solid;">SATUAN</td>';
        $content .= '<td style="text-align:center; border:1px solid;">KEMBALI</td>';
        $content .= '<td style="text-align:center; border:1px solid;">DITERIMA</td>';
        $content .= '<td style="text-align:center; border:1px solid;">TOTAL</td>';
        $content .= '</tr>';
        foreach ($datapengirimandetail as $key => $val) {
            $content .= '<tr>';
            $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px;">'.$val['barang_nama'].'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;">'.number_format($val['pengirimandet_harga']).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 10%;">'.number_format($val['pengirimandet_qty']).'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;">'.$val['satuan_nama'].'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;">'.number_format($val['pengirimandet_total']).'</td>';
            $content .= '</tr>';
        }

        $kosong = 10;
        if (count($datapengirimandetail) < 10) {
            for ($i=0; $i < ($kosong - count($datapengirimandetail)); $i++) { 
                $content .= '<tr>';
                $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px;"></td>';
                $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;"></td>';
                $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 10%;"></td>';
                $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
                $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
                $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
                $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;"></td>';
                $content .= '</tr>';
            }
        }
        $content .= '</table>';
        
        $content .= '<div style="padding-left: 10px;padding-right: 10px; padding-top: 50px">';
        $content .= '<table class="assign">';
        $content .= '<tr>';
        $content .= '<td style="text-align: center;">( DIBUAT OLEH )<br><br><br><br><br><br>.............................</td>';
        $content .= '<td style="text-align: center;">( DIKIRIM OLEH )<br><br><br><br><br><br>.............................</td>';
        $content .= '<td style="text-align: center;">( DITERIMA OLEH )<br><br><br><br><br><br>.............................</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '</div>';

        $content .= '</body>';
        $content .= '</html>';
        $mpdf->AddPage("L","","","","","5","5","5","5","","","","","","","","","","","","B5");
        $mpdf->WriteHTML($content);
        $mpdf->Output('pengiriman.pdf', 'I');
    }

}

$pengiriman = new C_pengiriman_brg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getpengiriman':
        $pengiriman->getPengiriman($_POST);
        break;
    case 'submit':
        $pengiriman->simpanPengiriman($_POST);
        break;
    case 'formtransaksi':
        $pengiriman->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $pengiriman->getRekanan($_GET);
        break;
    case 'getbarang':
        $pengiriman->getBarang();
        break;
    case 'exportpdf':
        $pengiriman->exportPdf($_GET);
        break;
    default:
        $pengiriman->Pengiriman();
    break;
}
