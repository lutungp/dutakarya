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
        $tanggal = date('Y-m-d');
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
        $jenis = isset($data['jenis']) ? $data['jenis'] : 'pelanggan';
        $result = $this->model->getRekanan($search, $jenis);
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
        $pengiriman_id = $data['pengiriman_id'] > 0 ? $data['pengiriman_id'] : 0;
        $pengiriman_no = $data['pengiriman_no'] > 0 ? $data['pengiriman_no'] : 0;
		$rit = $data['rit'] > 0 ? $data['rit'] : 1;
        if ($pengiriman_id > 0) {
            $fieldSave = ['pengiriman_tgl', 'm_pegdriver_id', 'm_peghelper_id', 'm_rekanan_id', 'rit', 'pengiriman_updated_by', 'pengiriman_updated_date', 'pengiriman_revised'];
            $dataSave = [$pengiriman_tgl, $data['m_pegdriver_id'], $data['m_peghelper_id'], $data['m_rekanan_id'], $rit, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'pengiriman_revised+1'];
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
            $fieldSave = ['pengiriman_no', 'pengiriman_tgl', 'm_pegdriver_id', 'm_peghelper_id', 'm_rekanan_id', 'rit', 'pengiriman_created_by', 'pengiriman_created_date'];
            $dataSave = [$pengiriman_no, $pengiriman_tgl, $data['m_pegdriver_id'], $data['m_peghelper_id'], $data['m_rekanan_id'], $rit, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
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
                    $fieldSave = ['t_pengiriman_id', 'm_bahanbakubrg_id', 'm_barang_id', 'm_satuan_id', 'pengirimandet_qty', 'pengirimandet_harga', 'pengirimandet_ppn', 'pengirimandet_subtotal', 'pengirimandet_potongan', 'pengirimandet_total',
                                  'pengirimandet_updated_by', 'pengirimandet_updated_date', 'pengirimandet_revised'];
                    $dataSave = [$pengiriman_id, $val['m_bahanbakubrg_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['pengirimandet_qty'], $val['pengirimandet_ppn'], $val['pengirimandet_harga'], $val['pengirimandet_subtotal'], $val['pengirimandet_potongan'], $val['pengirimandet_total'],
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
                    $fieldSave = ['t_pengiriman_id', 'm_bahanbakubrg_id', 'm_barang_id', 'm_satuan_id', 'pengirimandet_qty', 'pengirimandet_harga', 'pengirimandet_ppn', 'pengirimandet_subtotal', 'pengirimandet_potongan', 'pengirimandet_total',
                                    'pengirimandet_created_by', 'pengirimandet_created_date'];
                    $dataSave = [$pengiriman_id, $val['m_bahanbakubrg_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['pengirimandet_qty'], $val['pengirimandet_harga'], $val['pengirimandet_ppn'], $val['pengirimandet_subtotal'], $val['pengirimandet_potongan'], $val['pengirimandet_total'],
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

                    /* bahan produksi galon kosong dikembalikan */
                    $bahanproduksi = $this->model->getBahanBaku($val['m_barang_id'], 'produksi');
                    foreach ($bahanproduksi as $key => $valbb) {
                        $barangtrans_awal = $this->model->getStokAkhir($valbb['m_barang_id'], $data['pengiriman_tgl']);
                        $barangtrans_masuk  = 0;
                        $barangtrans_keluar = 0;
                        $barangtrans_jenis = '';
                        $barangtrans_status = '';
                        $barangtrans_akhir = 0;
                        if ($qty > $qtyold) {
                            $barangtrans_keluar = ($qty - $qtyold) * $valbb['bahanbrg_qty'];
                            $barangtrans_jenis = 'PENGIRIMAN BRG';
                            $barangtrans_status = 'MASUK';
                            $barangtrans_akhir = ($barangtrans_awal + $barangtrans_keluar) * $valbb['bahanbrg_qty'];
                        } else if ($qty < $qtyold) {
                            $barangtrans_masuk = ($qtyold - $qty) * $valbb['bahanbrg_qty'];
                            $barangtrans_jenis = 'PENGURANGAN PENGIRIMAN BRG';
                            $barangtrans_status = 'KELUAR';
                            $barangtrans_akhir = ($barangtrans_awal - $barangtrans_masuk) * $valbb['bahanbrg_qty'];
                        }
                        $datastock = array(
                            't_trans_id'      => $pengiriman_id,
                            't_transdet_id'   => $pengirimandet_id,
                            'barangtrans_jenis' => $barangtrans_jenis,
                            'barangtrans_no'  => $pengiriman_no,
                            'barangtrans_tgl' => $pengiriman_tgl,
                            'm_barang_id'  => $valbb['m_barang_id'],
                            'm_barangsatuan_id' => $valbb['m_satuan_id'],
                            'm_satuan_id' => $valbb['m_satuan_id'],
                            'barangtrans_jml' => $val['satkonv_nilai']*$val['pengirimandet_qty']*$valbb['bahanbrg_qty'],
                            'barangtrans_konv' => 1,
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
        }

        $return["code"] = "202";

        if ($pengiriman_id > 0) {
            $return["code"] = "200";
            $return["id"] = $pengiriman_id;
        }

        echo json_encode($return);
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
                'barangtrans_akhir' => $barangtrans_awal + $barangtrans_masuk,
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

             /* bahan produksi galon kosong dikembalikan */
             $bahanproduksi = $this->model->getBahanBaku($val['m_barang_id'], 'produksi');
             foreach ($bahanproduksi as $key => $valbb) {
                 $barangtrans_awal = $this->model->getStokAkhir($valbb['m_barang_id'], $pengiriman_tgl);
                 $barangtrans_keluar = ($val['satkonv_nilai'] * $val['pengirimandet_qty']) * $valbb['bahanbrg_qty'];
                 $datastock = array(
                     't_trans_id'      => $val['t_pengiriman_id'],
                     't_transdet_id'   =>  $val['pengirimandet_id'],
                     'barangtrans_jenis' => 'BTL PENGIRIMAN BRG',
                     'barangtrans_no'  => $pengiriman_no,
                     'barangtrans_tgl' => $pengiriman_tgl,
                     'm_barang_id'  => $valbb['m_barang_id'],
                     'm_barangsatuan_id' => $valbb['m_satuan_id'],
                     'm_satuan_id' => $valbb['m_satuan_id'],
                     'barangtrans_jml' => $val['satkonv_nilai']*$val['pengirimandet_qty'],
                     'barangtrans_konv' => 1,
                     'barangtrans_awal' => $barangtrans_awal,
                     'barangtrans_masuk' => 0,
                     'barangtrans_keluar' => $barangtrans_keluar,
                     'barangtrans_akhir' => $barangtrans_awal-$barangtrans_keluar,
                     'barangtrans_status' => 'KELUAR'
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
    }

    public function exportPdf($data)
    {
        require_once "../vendor/autoload.php";
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 148.5]]);
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
        $content .= ': ' . $datapengiriman->rekanan_kode . '<br>';
        $content .= ': ' . $datapengiriman->rekanan_nama . '<br>';
        $rekanan_alamat = str_replace('\n', '<br />', $datapengiriman->rekanan_alamat);
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
        $content .= ': ' . $datapengiriman->pengiriman_no . '<br>';
        $content .= ': ' . ucfirst($datapengiriman->user_nama) . '<br>';
        $content .= ':<br>';
        $content .= ': ' . ucfirst($datapengiriman->pegdriver_nama) . '<br>';
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
            $content .= '<td style="text-align:left; border-left:1px solid; border-right:1px solid; padding: 5px;">'.$val['m_barang_nama'].'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px;">'.number_format($val['pengirimandet_harga']).'</td>';
            $content .= '<td style="text-align:right; border-left:1px solid; border-right:1px solid; padding: 5px; width: 10%;">'.number_format($val['pengirimandet_qty']).'</td>';
            $content .= '<td style="text-align:center; border-left:1px solid; border-right:1px solid; padding: 5px; width: 8%;">'.$val['m_satuan_nama'].'</td>';
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
        $content .= '<td style="text-align: center;">( DIBUAT OLEH )<br><br><br><br><br><br>'.ucfirst($datapengiriman->user_nama).'</td>';
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

    public function getJadwal($tanggal)
    {
        $tanggalreal = $tanggal;
        $tanggal = strtotime(date('Y-m-d', strtotime($tanggal)));
        $day = date('N', $tanggal);
        $week = getWeeks($tanggalreal, $day) - 1;
        $month = intval(date('m', $tanggal));
        $year = intval(date('Y', $tanggal));
        $data = $this->model->getJadwal($day, $week, $month, $year, $tanggalreal);

        echo json_encode($data);
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
        $fieldSave = ['pengiriman_aktif', 'pengiriman_void_by', 'pengiriman_void_date', 'pengiriman_void_alasan'];
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
        $where = "WHERE pengiriman_id = " . $data['pengiriman_id'];
        $action = query_update($this->conn2, 't_pengiriman', $field, $where);
        /* nonaktif detail terlebih dahulu */
        if (isset($data['rows'])) {
            $this->nonaktifdetail($data['rows'], $data['pengiriman_no'], $data['pengiriman_tgl']);
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
    case 'getjadwal':
        $pengiriman->getJadwal($_POST['tanggal']);
        break;
    case 'gethargakontrak':
        $pengiriman->getHargaKontrak($_POST);
        break;
    case 'batal':
        $pengiriman->batal($_POST);
        break;
    case 'getpegdriver':
        $pengiriman->getPegDriver($_GET);
        break;
    case 'getpeghelper':
        $pengiriman->getPegHelper($_GET);
        break;
    case 'getbahanbaku':
        $pengiriman->getBahanBaku($_POST);
        break;
    default:
        $pengiriman->Pengiriman();
    break;
}
