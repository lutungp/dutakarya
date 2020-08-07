<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_maklon.php';

class C_maklon
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

        $this->model = new M_maklon($conn, $conn2, $config);
    }

    public function Maklon()
    {
        templateAdmin($this->conn2, '../views/maklon/v_maklon.php', NULL, 'TRANSAKSI', 'MAKLON');
    }

    public function getMaklon()
    {
        $data = $this->model->getMaklon();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        $tanggal = date('Y-m-d');
        if (isset($data['id']) > 0) {
            $maklon_id = $data['id'];
            $result['maklon_id'] = $maklon_id;
            $result['datamaklon'] = $this->model->getmaklonData($maklon_id);
            $result['datamaklondetail'] = $this->model->getmaklonDataDetail($maklon_id);
        }
        
        templateAdmin($this->conn2, '../views/maklon/v_formmaklon.php', json_encode($result), 'TRANSAKSI', 'MAKLON');
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $jenis = isset($data['jenis']) ? $data['jenis'] : 'pelanggan';
        $result = $this->model->getRekanan($search, $jenis);
        echo json_encode($result);
    }

    public function getBarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function simpanmaklon($data)
    {
        $maklon_tgl = date('Y-m-d', strtotime($data['maklon_tgl']));
        $maklon_id = $data['maklon_id'];
        $maklon_no = $data['maklon_no'];
        if ($maklon_id > 0) {
            $fieldSave = ['maklon_tgl', 'm_rekanan_id', 'maklon_updated_by', 'maklon_updated_date', 'maklon_revised'];
            $dataSave = [$maklon_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'maklon_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE maklon_id = " . $data['maklon_id'];
            query_update($this->conn2, 't_maklon', $field, $where);
        } else {
            $maklon_no = getPenomoran($this->conn2, 'MK', 't_maklon', 'maklon_id', 'maklon_no', $maklon_tgl);
            $fieldSave = ['maklon_no', 'maklon_tgl', 'm_rekanan_id', 'maklon_created_by', 'maklon_created_date'];
            $dataSave = [$maklon_no, $maklon_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $maklon_id = query_create($this->conn2, 't_maklon', $fieldSave, $dataSave);
        }
        /* nonaktif detail terlebih dahulu */
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail'], $maklon_no, $maklon_tgl);
        }
        
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $maklondet_id = $val['maklondet_id'];
                if ($maklondet_id > 0) {
                    $fieldSave = ['t_maklon_id', 'm_bahanbakubrg_id', 'm_barang_id', 'm_satuan_id', 'maklondet_qty', 'maklondet_harga', 'maklondet_ppn', 'maklondet_subtotal', 'maklondet_total',
                                  'maklondet_updated_by', 'maklondet_updated_date', 'maklondet_revised'];
                    $dataSave = [$maklon_id, $val['m_bahanbakubrg_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['maklondet_qty'], $val['maklondet_ppn'], $val['maklondet_harga'], $val['maklondet_subtotal'], $val['maklondet_total'],
                                    $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'maklondet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE maklondet_id = " . $val['maklondet_id'];
                    query_update($this->conn2, 't_maklondet', $field, $where);
                } else {
                    $fieldSave = ['t_maklon_id', 'm_bahanbakubrg_id', 'm_barang_id', 'm_satuan_id', 'maklondet_qty', 'maklondet_harga', 'maklondet_ppn', 'maklondet_subtotal', 'maklondet_total',
                                    'maklondet_created_by', 'maklondet_created_date'];
                    $dataSave = [$maklon_id, $val['m_bahanbakubrg_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['maklondet_qty'], $val['maklondet_harga'], $val['maklondet_ppn'], $val['maklondet_subtotal'], $val['maklondet_total'],
                                $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $maklondet_id = query_create($this->conn2, 't_maklondet', $fieldSave, $dataSave);
                }

                $qtyold = $val['maklondet_qtyold'] * $val['satkonv_nilai'];
                $qty    = $val['maklondet_qty'] * $val['satkonv_nilai'];
                if($qty <> $qtyold) {
                    $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $data['maklon_tgl']);
                    $barangtrans_masuk  = 0;
                    $barangtrans_keluar = 0;
                    $barangtrans_jenis = '';
                    $barangtrans_status = '';
                    $barangtrans_akhir = 0;
                    if ($qty > $qtyold) {
                        $barangtrans_keluar = $qty - $qtyold;
                        $barangtrans_jenis = 'MAKLON BRG';
                        $barangtrans_status = 'KELUAR';
                        $barangtrans_akhir = $barangtrans_awal - $barangtrans_keluar;
                    } else if ($qty < $qtyold) {
                        $barangtrans_masuk = $qtyold - $qty;
                        $barangtrans_jenis = 'PENGURANGAN MAKLON BRG';
                        $barangtrans_status = 'MASUK';
                        $barangtrans_akhir = $barangtrans_awal + $barangtrans_masuk;
                    }

                    $datastock = array(
                        't_trans_id'      => $maklon_id,
                        't_transdet_id'   => $maklondet_id,
                        'barangtrans_jenis' => $barangtrans_jenis,
                        'barangtrans_no'  => $maklon_no,
                        'barangtrans_tgl' => $maklon_tgl,
                        'm_barang_id'  => $val['m_barang_id'],
                        'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                        'm_satuan_id' => $val['m_satuan_id'],
                        'barangtrans_jml' => $val['maklondet_qty'],
                        'barangtrans_konv' => $val['satkonv_nilai'],
                        'barangtrans_awal' => $barangtrans_awal,
                        'barangtrans_masuk' => $barangtrans_masuk,
                        'barangtrans_keluar' => $barangtrans_keluar,
                        'barangtrans_akhir' => $barangtrans_akhir,
                        'barangtrans_status' => $barangtrans_status
                    );

                    if (strtotime($maklon_tgl) < strtotime(date('Y-m-d'))) {
                        /* jika melakukan backdate */
                        $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                        $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                        $where = "WHERE barangtrans_tgl >= '" . $maklon_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
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
        
        $return["code"] = "202";

        if ($maklon_id > 0) {
            $return["code"] = "200";
            $return["id"] = $maklon_id;
        }

        echo json_encode($return);
    }

    public function nonaktifdetail($data, $maklon_no, $maklon_tgl)
    {
        $maklondet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($maklondet_idArr, $data[$i]['maklondet_id']);
        }
        $maklondet_id = implode(',', $maklondet_idArr);
        $fieldSave = ["maklondet_aktif","maklondet_void_by", "maklondet_void_date"];
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
        $where = "WHERE maklondet_id IN (".$maklondet_id.")";
        $action = query_update($this->conn2, 't_maklondet', $field, $where);
        $detail = $this->model->getmaklonDataDetail2($maklondet_id);
        
        foreach ($detail as $key => $val) {
            $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $maklon_tgl);
            $barangtrans_masuk = $val['satkonv_nilai'] * $val['maklondet_qty'];
            $datastock = array(
                't_trans_id'      => $val['t_maklon_id'],
                't_transdet_id'   => $val['maklondet_id'],
                'barangtrans_jenis' => 'BTL MAKLON BRG',
                'barangtrans_no'  => $maklon_no,
                'barangtrans_tgl' => $maklon_tgl,
                'm_barang_id'  => $val['m_barang_id'],
                'm_barangsatuan_id' => $val['satuanutama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'barangtrans_jml' => $val['maklondet_qty'],
                'barangtrans_konv' => $val['satkonv_nilai'],
                'barangtrans_awal' => $barangtrans_awal,
                'barangtrans_masuk' => $barangtrans_masuk,
                'barangtrans_keluar' => 0,
                'barangtrans_akhir' => $barangtrans_awal + $barangtrans_masuk,
                'barangtrans_status' => 'MASUK'
            );
            if (strtotime($maklon_tgl) < strtotime(date('Y-m-d'))) {
                /* jika melakukan backdate */
                $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                $field = " barangtrans_awal=barangtrans_awal+$barangtrans_keluar,  barangtrans_akhir=barangtrans_akhir+$barangtrans_keluar ";
                $where = "WHERE barangtrans_tgl >= '" . $maklon_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
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
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 148.5]]);
        $maklon_id = $data['id'];
        $datamaklon = $this->model->getmaklonData($maklon_id);
        $datamaklondetail = $this->model->getmaklonDataDetail($maklon_id);
        
        $content = '<html>';
        $content .= '<header>';
        $content .= '<style>';
        $content .= 'html, body {font-size: 12px; font-family : Helvetica;} 
                    .maklon {
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
        $content .= '<td style="width:50%;font-size:13px;">PT. DUTAKARYA YASHA<br>JL. DEWI SARTIKA 312 CAWANG<br> JAKARTA TIMUR 13650<br>TELP. (021) 22801922&nbsp;&nbsp;&nbsp;&nbsp;<br>CS&nbsp;:&nbsp;08111189888<br>NPWP&nbsp;:&nbsp;0 3 0 2 2 4 3 8 5 9 0 0 8 0 0</td>';
        $content .= '<td style="vertical-align:top;text-align:center;width:50%;font-size:13px;">DELIVERY ORDER</td>';
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
        $content .= ': ' . $datamaklon->rekanan_kode . '<br>';
        $content .= ': ' . $datamaklon->rekanan_nama . '<br>';
        $rekanan_alamat = str_replace('\n', '<br />', $datamaklon->rekanan_alamat);
        $content .= ': ' .  $rekanan_alamat . '<br>';
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
        $content .= ': ' . $datamaklon->maklon_no . '<br>';
        $content .= ': ' . ucfirst($datamaklon->user_nama) . '<br>';
        $content .= ':<br>';
        $content .= ': ' . ucfirst($datamaklon->pegdriver_nama) . '<br>';
        $content .= ':<br>';
        $content .= '</td>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '<table class="maklon">';
        $content .= '<tr>';
        $content .= '<td style="text-align:center; border:1px solid;">PRODUK</td>';
        $content .= '<td style="text-align:center; border:1px solid;">HARGA</td>';
        $content .= '<td style="text-align:center; border:1px solid;">DIKIRIM</td>';
        $content .= '<td style="text-align:center; border:1px solid;">SATUAN</td>';
        $content .= '<td style="text-align:center; border:1px solid;">KEMBALI</td>';
        $content .= '<td style="text-align:center; border:1px solid;">DITERIMA</td>';
        $content .= '<td style="text-align:center; border:1px solid;">TOTAL</td>';
        $content .= '</tr>';
        foreach ($datamaklondetail as $key => $val) {
            $content .= '<tr>';
            $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px;">'.$val['m_barang_nama'].'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;">'.number_format($val['maklondet_harga']).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 10%;">'.number_format($val['maklondet_qty']).'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;">'.$val['m_satuan_nama'].'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;"></td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;">'.number_format($val['maklondet_total']).'</td>';
            $content .= '</tr>';
        }

        $kosong = 10;
        if (count($datamaklondetail) < 10) {
            for ($i=0; $i < ($kosong - count($datamaklondetail)); $i++) { 
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
        $content .= '<td style="text-align: center;">( DIBUAT OLEH )<br><br><br><br><br><br>'.ucfirst($datamaklon->user_nama).'</td>';
        $content .= '<td style="text-align: center;">( DIKIRIM OLEH )<br><br><br><br><br><br>.............................</td>';
        $content .= '<td style="text-align: center;">( DITERIMA OLEH )<br><br><br><br><br><br>.............................</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '</div>';

        $content .= '</body>';
        $content .= '</html>';
        $mpdf->AddPage("P","","","","","5","5","5","5","","","","","","","","","","","");
        $mpdf->WriteHTML($content);
        $mpdf->Output();
    }

    public function getHargaKontrak($data)
    {
        $barang_id = $data['barang_id'];
        $tanggal = $data['tanggal'];
        $m_rekanan_id = $data['m_rekanan_id'];
        $data = $this->model->getHargaKontrak($barang_id, $tanggal, $m_rekanan_id);

        echo json_encode($data);
    }

    public function batal($data)
    {
        $fieldSave = ['maklon_aktif', 'maklon_void_by', 'maklon_void_date', 'maklon_void_alasan'];
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
        $where = "WHERE maklon_id = " . $data['maklon_id'];
        $action = query_update($this->conn2, 't_maklon', $field, $where);
        /* nonaktif detail terlebih dahulu */
        if (isset($data['rows'])) {
            $this->nonaktifdetail($data['rows'], $data['maklon_no'], $data['maklon_tgl']);
        }

        if ($action > 0) {
            echo 200;
        } else {
            echo 202;
        }
        
    }

    public function getPegDriver($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $tanggal = isset($data['tanggal']) ? $data['tanggal'] : '';
        $pegawai = $this->model->getPegawai($search, 'driver', $tanggal);
        echo json_encode($pegawai);
    }

    public function getPegHelper($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $tanggal = isset($data['tanggal']) ? $data['tanggal'] : '';
        $pegawai = $this->model->getPegawai($search, 'helper', $tanggal);
        echo json_encode($pegawai);
    }

    public function getBahanBaku($data)
    {
        $data = $this->model->getBahanBaku($data['barang_id']);
        echo json_encode($data);
    }
}

$maklon = new C_maklon($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getmaklon':
        $maklon->getMaklon($_POST);
        break;
    case 'submit':
        $maklon->simpanmaklon($_POST);
        break;
    case 'formtransaksi':
        $maklon->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $maklon->getRekanan($_GET);
        break;
    case 'getbarang':
        $maklon->getBarang();
        break;
    case 'exportpdf':
        $maklon->exportPdf($_GET);
        break;
    case 'gethargakontrak':
        $maklon->getHargaKontrak($_POST);
        break;
    case 'batal':
        $maklon->batal($_POST);
        break;
    case 'getpegdriver':
        $maklon->getPegDriver($_GET);
        break;
    case 'getpeghelper':
        $maklon->getPegHelper($_GET);
        break;
    case 'getbahanbaku':
        $maklon->getBahanBaku($_POST);
        break;
    default:
        $maklon->Maklon();
    break;
}
