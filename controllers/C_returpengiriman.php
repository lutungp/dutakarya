<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_returpengiriman.php';

class C_returpengiriman
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

        $this->model = new M_returpengiriman($conn, $conn2, $config);
    }

    public function Retur()
    {
        templateAdmin($this->conn2, '../views/returpengiriman/v_returpengiriman.php', NULL, 'TRANSAKSI', 'RETUR PENGIRIMAN');
    }

    public function getRetur()
    {
        $data = $this->model->getRetur();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $retur_id = $data['id'];
            $result['retur_id'] = $retur_id;
            $result['dataretur'] = $this->model->getReturData($retur_id);
            $result['datareturdetail'] = $this->model->getReturDataDetail($retur_id);
        }

        templateAdmin($this->conn2, '../views/returpengiriman/v_formreturpengiriman.php', json_encode($result), 'TRANSAKSI', 'RETUR PENGIRIMAN');
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $result = $this->model->getRekanan($search);
        echo json_encode($result);
    }

    public function getSatKonv()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function simpanRetur($data)
    {
        $retur_tgl = date('Y-m-d', strtotime($data['retur_tgl']));
        $retur_id = $data['retur_id'];
        $retur_no = $data['retur_no'];
        if ($retur_id > 0) {
            $fieldSave = ['retur_tgl', 't_pengiriman_id', 'm_rekanan_id', 'retur_updated_by', 'retur_updated_date', 'retur_revised'];
            $dataSave = [$retur_tgl, $data['t_pengiriman_id'], $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'retur_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE retur_id = " . $data['retur_id'];
            query_update($this->conn2, 't_retur', $field, $where);
        } else {
            $retur_no = getPenomoran($this->conn2, 'RT', 't_retur', 'retur_id', 'retur_no', $retur_tgl);
            $fieldSave = ['retur_no', 't_pengiriman_id', 'retur_tgl', 'm_rekanan_id', 'retur_created_by', 'retur_created_date'];
            $dataSave = [$retur_no, $data['t_pengiriman_id'], $retur_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $retur_id = query_create($this->conn2, 't_retur', $fieldSave, $dataSave);
        }
        
        /* nonaktif detail terlebih dahulu */
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail'], $retur_no, $retur_tgl);
        }
        
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $returdet_id = $val['returdet_id'];
                if ($returdet_id > 0) {
                    $fieldSave = ['t_retur_id', 't_pengirimandet_id', 'm_barang_id', 'm_satuan_id', 'returdet_qty', 'returdet_updated_by', 'returdet_updated_date', 'returdet_revised'];
                    $dataSave = [$retur_id, $val['t_pengirimandet_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['returdet_qty'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'returdet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE returdet_id = " . $val['returdet_id'];
                    query_update($this->conn2, 't_returdet', $field, $where);
                } else {
                    $fieldSave = ['t_retur_id', 't_pengirimandet_id', 'm_barang_id', 'm_satuan_id', 'returdet_qty', 'returdet_created_by', 'returdet_created_date'];
                    $dataSave = [$retur_id, $val['t_pengirimandet_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['returdet_qty'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $returdet_id = query_create($this->conn2, 't_returdet', $fieldSave, $dataSave);
                }

                $qtyold = $val['returdet_qtyold'] * $val['satkonv_nilai'];
                $qty    = $val['returdet_qty'] * $val['satkonv_nilai'];

                if($qty <> $qtyold) {
                    $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $retur_tgl);
                    $barangtrans_masuk  = 0;
                    $barangtrans_keluar = 0;
                    $barangtrans_jenis = '';
                    $barangtrans_status = '';
                    $barangtrans_akhir = 0;
                    
                    $fieldSaveKirim = ['t_returdet_qty']; /* memberi flag pada pengiriman barang */
                    if ($qty > $qtyold) {
                        $barangtrans_masuk = $qty - $qtyold;
                        $barangtrans_jenis = 'RETUR BRG';
                        $barangtrans_status = 'MASUK';
                        $barangtrans_akhir = $barangtrans_awal + $barangtrans_masuk;
                        $dataSaveKirim = [ "t_returdet_qty+".$barangtrans_masuk ];
                    } else if ($qty < $qtyold) {
                        $barangtrans_keluar = $qtyold - $qty;
                        $barangtrans_jenis = 'PENGURANGAN RETUR BRG';
                        $barangtrans_status = 'KELUAR';
                        $barangtrans_akhir = $barangtrans_awal - $barangtrans_keluar;
                        $dataSaveKirim = [ "t_returdet_qty-".$barangtrans_keluar ];
                    }
                    
                    $fieldkirim = "";
                    foreach ($fieldSaveKirim as $key => $value) {
                        $regex = (integer)$key < count($fieldSaveKirim)-1 ? "," : "";
                        $fieldkirim .= "$value = $dataSaveKirim[$key]" . $regex . " ";
                    }
                    
                    $wherekirim = "WHERE pengirimandet_id = " . $val['t_pengirimandet_id'];
                    query_update($this->conn2, 't_pengiriman_detail', $fieldkirim, $wherekirim);

                    $datastock = array(
                        't_trans_id'      => $retur_id,
                        't_transdet_id'   => $returdet_id,
                        'barangtrans_jenis' => $barangtrans_jenis,
                        'barangtrans_no'  => $retur_no,
                        'barangtrans_tgl' => $retur_tgl,
                        'm_barang_id'  => $val['m_barang_id'],
                        'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                        'm_satuan_id' => $val['m_satuan_id'],
                        'barangtrans_jml' => $val['returdet_qty'],
                        'barangtrans_konv' => $val['satkonv_nilai'],
                        'barangtrans_awal' => $barangtrans_awal,
                        'barangtrans_masuk' => $barangtrans_masuk,
                        'barangtrans_keluar' => $barangtrans_keluar,
                        'barangtrans_akhir' => $barangtrans_akhir,
                        'barangtrans_status' => $barangtrans_status
                    );

                    if (strtotime($retur_tgl) < strtotime(date('Y-m-d'))) {
                        /* jika melakukan backdate */
                        $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                        $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                        $where = "WHERE barangtrans_tgl >= '" . $retur_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
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

        if ($retur_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function nonaktifdetail($data, $retur_no, $retur_tgl)
    {
        $returdet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($returdet_idArr, $data[$i]['returdet_id']);
        }
        $returdet_id = implode(',', $returdet_idArr);
        
        $fieldSave = ["returdet_aktif","returdet_void_by", "returdet_void_date"];
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
        $where = "WHERE returdet_id IN (".$returdet_id.")";
        $action = query_update($this->conn2, 't_returdet', $field, $where);
        $detail = $this->model->getReturDataDetail2($returdet_id);
        
        foreach ($detail as $key => $val) {
            $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $retur_tgl);
            $barangtrans_keluar = $val['satkonv_nilai'] * $val['returdet_qty'];
            $datastock = array(
                't_trans_id'      => $val['t_retur_id'],
                't_transdet_id'   => $val['returdet_id'],
                'barangtrans_jenis' => 'BTL RETUR BRG',
                'barangtrans_no'  => $retur_no,
                'barangtrans_tgl' => $retur_tgl,
                'm_barang_id'  => $val['m_barang_id'],
                'm_barangsatuan_id' => $val['satuanutama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'barangtrans_jml' => $val['returdet_qty'],
                'barangtrans_konv' => $val['satkonv_nilai'],
                'barangtrans_awal' => $barangtrans_awal,
                'barangtrans_masuk' => 0,
                'barangtrans_keluar' => $barangtrans_keluar,
                'barangtrans_akhir' => $barangtrans_awal - $barangtrans_keluar,
                'barangtrans_status' => 'KELUAR'
            );
            
            if (strtotime($retur_tgl) < strtotime(date('Y-m-d'))) {
                /* jika melakukan backdate */
                $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                $field = " barangtrans_awal=barangtrans_awal-$barangtrans_keluar,  barangtrans_akhir=barangtrans_akhir-$barangtrans_keluar ";
                $where = "WHERE barangtrans_tgl >= '" . $retur_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                query_update($this->conn2, 't_barangtrans', $field, $where);
            }

            $fieldSaveKirim = ['t_returdet_qty']; /* memberi flag pada pengiriman barang */
            $dataSaveKirim = [ "t_returdet_qty-".$barangtrans_keluar ];
            
            $fieldkirim = "";
            foreach ($fieldSaveKirim as $key => $value) {
                $regex = (integer)$key < count($fieldSaveKirim)-1 ? "," : "";
                $fieldkirim .= "$value = $dataSaveKirim[$key]" . $regex . " ";
            }
            
            $wherekirim = "WHERE pengirimandet_id = " . $val['t_pengirimandet_id'];
            query_update($this->conn2, 't_pengiriman_detail', $fieldkirim, $wherekirim);

            $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                         'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
            $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                         $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_akhir'], $datastock['barangtrans_status'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);
        }
    }

    public function batalRetur($data)
    {
        $fieldSave = ['retur_aktif', 'retur_void_alasan', 'retur_void_by', 'retur_void_date'];
        $dataSave = ['N', $data['alasan'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE retur_id = " . $data['retur_id'];
        $action = query_update($this->conn2, 't_retur', $field, $where);
        $this->nonaktifdetail($data['rows'], $data['retur_no'], $data['retur_tgl']);
        
        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function getPengiriman($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $result = $this->model->getPengiriman($search);
        echo json_encode($result);
    }

    public function getPengirimanDet($data)
    {
        $result['pengiriman'] = $this->model->getPengirimanData($data['pengiriman_id']);
        $result['pengirimandet'] = $this->model->getPengirimanDataDetail($data['pengiriman_id']);

        echo json_encode($result);
    }
}

$retur = new C_returpengiriman($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getretur':
        $retur->getRetur($_POST);
        break;
    case 'submit':
        $retur->simpanRetur($_POST);
        break;
    case 'formtransaksi':
        $retur->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $retur->getRekanan($_GET);
        break;
    case 'batal':
        $retur->batalRetur($_POST);
        break;
    case 'getsatkonv':
        $retur->getSatKonv();
        break;
    case 'getpengiriman':
        $retur->getPengiriman($_GET);
        break;
    case 'getpengirimandet':
        $retur->getPengirimanDet($_POST);
        break;
    default:
        $retur->retur();
    break;
}
