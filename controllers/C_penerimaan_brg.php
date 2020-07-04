<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_penerimaan_brg.php';

class C_penerimaan_brg
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

        $this->model = new M_penerimaan_brg($conn, $conn2, $config);
    }

    public function Penerimaan()
    {
        templateAdmin($this->conn2, '../views/penerimaanbrg/v_penerimaan_brg.php', NULL, 'TRANSAKSI', 'PENERIMAAN BARANG');
    }

    public function getPenerimaan()
    {
        $data = $this->model->getPenerimaan();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $penerimaan_id = $data['id'];
            $result['penerimaan_id'] = $penerimaan_id;
            $result['datapenerimaan'] = $this->model->getPenerimaanData($penerimaan_id);
            $result['datapenerimaandetail'] = $this->model->getPenerimaanDataDetail($penerimaan_id);
        }

        templateAdmin($this->conn2, '../views/penerimaanbrg/v_formpenerimaan_brg.php', json_encode($result), 'TRANSAKSI', 'PENERIMAAN BARANG');
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

    public function simpanPenerimaan($data)
    {
        $penerimaan_tgl = date('Y-m-d', strtotime($data['penerimaan_tgl']));
        $penerimaan_id = $data['penerimaan_id'];
        $penerimaan_no = $data['penerimaan_no'];
        if ($penerimaan_id > 0) {
            $fieldSave = ['penerimaan_tgl', 'm_rekanan_id', 'penerimaan_updated_by', 'penerimaan_updated_date', 'penerimaan_revised'];
            $dataSave = [$penerimaan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'penerimaan_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE penerimaan_id = " . $data['penerimaan_id'];
            query_update($this->conn2, 't_penerimaan', $field, $where);
        } else {
            $penerimaan_no = getPenomoran($this->conn2, 'TM', 't_penerimaan', 'penerimaan_id', 'penerimaan_no', $penerimaan_tgl);
            $fieldSave = ['penerimaan_no', 'penerimaan_tgl', 'm_rekanan_id', 'penerimaan_created_by', 'penerimaan_created_date'];
            $dataSave = [$penerimaan_no, $penerimaan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $penerimaan_id = query_create($this->conn2, 't_penerimaan', $fieldSave, $dataSave);
        }
        /* nonaktif detail terlebih dahulu */
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail'], $penerimaan_no, $penerimaan_tgl);
        }
        
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $penerimaandet_id = $val['penerimaandet_id'];
                if ($penerimaandet_id > 0) {
                    $fieldSave = ['t_penerimaan_id', 'm_barang_id', 'm_satuan_id', 'penerimaandet_qty', 'penerimaandet_updated_by', 'penerimaandet_updated_date', 'penerimaandet_revised'];
                    $dataSave = [$penerimaan_id, $val['m_barang_id'], $val['m_satuan_id'], $val['penerimaandet_qty'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'penerimaandet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE penerimaandet_id = " . $val['penerimaandet_id'];
                    query_update($this->conn2, 't_penerimaan_detail', $field, $where);
                } else {
                    $fieldSave = ['t_penerimaan_id', 'm_barang_id', 'm_satuan_id', 'penerimaandet_qty', 'penerimaandet_created_by', 'penerimaandet_created_date'];
                    $dataSave = [$penerimaan_id, $val['m_barang_id'], $val['m_satuan_id'], $val['penerimaandet_qty'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $penerimaandet_id = query_create($this->conn2, 't_penerimaan_detail', $fieldSave, $dataSave);
                }

                $qtyold = $val['penerimaandet_qtyold'] * $val['satkonv_nilai'];
                $qty    = $val['penerimaandet_qty'] * $val['satkonv_nilai'];
                if($qty <> $qtyold) {
                    $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $penerimaan_tgl);
                    $barangtrans_masuk  = 0;
                    $barangtrans_keluar = 0;
                    $barangtrans_jenis = '';
                    $barangtrans_status = '';
                    $barangtrans_akhir = 0;
                    if ($qty > $qtyold) {
                        $barangtrans_masuk = $qty - $qtyold;
                        $barangtrans_jenis = 'PENERIMAAN BRG';
                        $barangtrans_status = 'MASUK';
                        $barangtrans_akhir = $barangtrans_awal + $barangtrans_masuk;
                    } else if ($qty < $qtyold) {
                        $barangtrans_keluar = $qtyold - $qty;
                        $barangtrans_jenis = 'PENGURANGAN PENERIMAAN BRG';
                        $barangtrans_status = 'KELUAR';
                        $barangtrans_akhir = $barangtrans_awal - $barangtrans_keluar;
                    }

                    $datastock = array(
                        't_trans_id'      => $penerimaan_id,
                        't_transdet_id'   => $penerimaandet_id,
                        'barangtrans_jenis' => $barangtrans_jenis,
                        'barangtrans_no'  => $penerimaan_no,
                        'barangtrans_tgl' => $penerimaan_tgl,
                        'm_barang_id'  => $val['m_barang_id'],
                        'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                        'm_satuan_id' => $val['m_satuan_id'],
                        'barangtrans_jml' => $val['penerimaandet_qty'],
                        'barangtrans_konv' => $val['satkonv_nilai'],
                        'barangtrans_awal' => $barangtrans_awal,
                        'barangtrans_masuk' => $barangtrans_masuk,
                        'barangtrans_keluar' => $barangtrans_keluar,
                        'barangtrans_akhir' => $barangtrans_akhir,
                        'barangtrans_status' => $barangtrans_status
                    );

                    if (strtotime($penerimaan_tgl) < strtotime(date('Y-m-d'))) {
                        /* jika melakukan backdate */
                        $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                        $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                        $where = "WHERE barangtrans_tgl >= '" . $penerimaan_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
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

        if ($penerimaan_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function nonaktifdetail($data, $penerimaan_no, $penerimaan_tgl)
    {
        $penerimaandet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($penerimaandet_idArr, $data[$i]['penerimaandet_id']);
        }
        $penerimaandet_id = implode(',', $penerimaandet_idArr);
        
        $fieldSave = ["penerimaandet_aktif","penerimaandet_void_by", "penerimaandet_void_date"];
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
        $where = "WHERE penerimaandet_id IN (".$penerimaandet_id.")";
        $action = query_update($this->conn2, 't_penerimaan_detail', $field, $where);
        $detail = $this->model->getPenerimaanDataDetail2($penerimaandet_id);
        
        foreach ($detail as $key => $val) {
            $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $penerimaan_tgl);
            $barangtrans_keluar = $val['satkonv_nilai'] * $val['penerimaandet_qty'];
            $datastock = array(
                't_trans_id'      => $val['t_penerimaan_id'],
                't_transdet_id'   => $val['penerimaandet_id'],
                'barangtrans_jenis' => 'BTL PENERIMAAN BRG',
                'barangtrans_no'  => $penerimaan_no,
                'barangtrans_tgl' => $penerimaan_tgl,
                'm_barang_id'  => $val['m_barang_id'],
                'm_barangsatuan_id' => $val['satuanutama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'barangtrans_jml' => $val['penerimaandet_qty'],
                'barangtrans_konv' => $val['satkonv_nilai'],
                'barangtrans_awal' => $barangtrans_awal,
                'barangtrans_masuk' => 0,
                'barangtrans_keluar' => $barangtrans_keluar,
                'barangtrans_akhir' => $barangtrans_awal - $barangtrans_keluar,
                'barangtrans_status' => 'KELUAR'
            );
            
            if (strtotime($penerimaan_tgl) < strtotime(date('Y-m-d'))) {
                /* jika melakukan backdate */
                $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                $field = " barangtrans_awal=barangtrans_awal-$barangtrans_keluar,  barangtrans_akhir=barangtrans_akhir-$barangtrans_keluar ";
                $where = "WHERE barangtrans_tgl >= '" . $penerimaan_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                query_update($this->conn2, 't_barangtrans', $field, $where);
            }
            $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                         'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
            $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                         $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_akhir'], $datastock['barangtrans_status'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);
        }
    }

    public function batalPenerimaan($data)
    {
        $fieldSave = ['penerimaan_aktif', 'penerimaan_void_alasan', 'penerimaan_void_by', 'penerimaan_void_date'];
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
        $where = "WHERE penerimaan_id = " . $data['penerimaan_id'];
        $action = query_update($this->conn2, 't_penerimaan', $field, $where);
        $this->nonaktifdetail($data['rows'], $data['penerimaan_no'], $data['penerimaan_tgl']);
        
        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }
}

$penerimaan = new C_penerimaan_brg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getpenerimaan':
        $penerimaan->getPenerimaan($_POST);
        break;
    case 'submit':
        $penerimaan->simpanPenerimaan($_POST);
        break;
    case 'formtransaksi':
        $penerimaan->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $penerimaan->getRekanan($_GET);
        break;
    case 'batal':
        $penerimaan->batalPenerimaan($_POST);
        break;
    case 'getbarang':
        $penerimaan->getBarang();
        break;
    default:
        $penerimaan->Penerimaan();
    break;
}
