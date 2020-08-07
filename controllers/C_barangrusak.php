<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_barangrusak.php';

class C_barangrusak
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

        $this->model = new M_barangrusak($conn, $conn2, $config);
    }

    public function getBarangRusak()
    {
        $result = $this->model->getBarangRusak();
        echo json_encode($result);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $barangrusak_id = $data['id'];
            $result['barangrusak_id'] = $barangrusak_id;
            $result['databarangrusak'] = $this->model->getBarangRusakData($barangrusak_id);
            $result['databarangrusakdetail'] = $this->model->getBarangRusakDataDetail($barangrusak_id);
        }
        
        templateAdmin($this->conn2, '../views/barangrusak/v_formbarangrusak.php', json_encode($result), 'TRANSAKSI', 'BARANG RUSAK');
    }

    public function getBarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function getHargaKontrak($data)
    {
        $barang_id = $data['barang_id'];
        $tanggal = $data['tanggal'];
        $m_rekanan_id = $data['m_rekanan_id'];
        $data = $this->model->getHargaKontrak($barang_id, $tanggal, $m_rekanan_id);

        echo json_encode($data);
    }

    public function submit($data)
    {
        $barangrusak_id = $data['barangrusak_id'];
        $barangrusak_no = $data['barangrusak_no'];
        $barangrusak_tgl = $data['barangrusak_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];
        if ($barangrusak_id > 0) {
            $fieldSave = ['barangrusak_tgl', 'm_rekanan_id', 'barangrusak_updated_by', 'barangrusak_updated_date', 'barangrusak_revised'];
            $dataSave = [$barangrusak_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'barangrusak_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE barangrusak_id = " . $data['barangrusak_id'];
            query_update($this->conn2, 't_barangrusak', $field, $where);
        } else {
            $barangrusak_no = getPenomoran($this->conn2, 'BR', 't_barangrusak', 'barangrusak_id', 'barangrusak_no', $barangrusak_tgl);
            $fieldSave = ['barangrusak_no', 'barangrusak_tgl', 'm_rekanan_id', 'barangrusak_created_by', 'barangrusak_created_date'];
            $dataSave = [$barangrusak_no, $barangrusak_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $barangrusak_id = query_create($this->conn2, 't_barangrusak', $fieldSave, $dataSave);
        }
        /* nonaktif detail terlebih dahulu */
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail'], $barangrusak_no, $barangrusak_tgl);
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $barangrusakdet_id = $val['barangrusakdet_id'];
                if ($barangrusakdet_id > 0) {
                    $fieldSave = ['t_barangrusak_id', 'm_barang_id', 'm_satuan_id', 'barangrusakdet_qty', 'barangrusakdet_harga', 'barangrusakdet_subtotal', 'barangrusakdet_alasan',
                                  'barangrusakdet_updated_by', 'barangrusakdet_updated_date', 'barangrusakdet_revised'];
                    $dataSave = [$barangrusak_id, $val['m_barang_id'], $val['m_satuan_id'], $val['barangrusakdet_qty'], $val['barangrusakdet_harga'], $val['barangrusakdet_subtotal'], $val['barangrusakdet_alasan'],
                                 $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'barangrusakdet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE barangrusakdet_id = " . $val['barangrusakdet_id'];
                    query_update($this->conn2, 't_barangrusakdet', $field, $where);
                } else {
                    $fieldSave = ['t_barangrusak_id', 'm_barang_id', 'm_satuan_id', 'barangrusakdet_qty', 'barangrusakdet_harga', 'barangrusakdet_subtotal', 'barangrusakdet_alasan',
                                  'barangrusakdet_created_by', 'barangrusakdet_created_date'];
                    $dataSave = [$barangrusak_id, $val['m_barang_id'], $val['m_satuan_id'], $val['barangrusakdet_qty'], $val['barangrusakdet_harga'], $val['barangrusakdet_subtotal'], $val['barangrusakdet_alasan'], 
                                 $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $barangrusakdet_id = query_create($this->conn2, 't_barangrusakdet', $fieldSave, $dataSave);
                }

                $qtyold = $val['barangrusakdet_qtyold'];
                $qty    = $val['barangrusakdet_qty'];
                if($qty <> $qtyold) {
                    $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $barangrusak_tgl);
                    $barangtrans_masuk  = 0;
                    $barangtrans_keluar = 0;
                    $barangtrans_jenis = '';
                    $barangtrans_status = '';
                    $barangtrans_akhir = 0;
                    if ($qty > $qtyold) {
                        $barangtrans_masuk = $qty - $qtyold;
                        $barangtrans_jenis = 'BARANG RUSAK';
                        $barangtrans_status = 'MASUK';
                        $barangtrans_akhir = $barangtrans_awal + $barangtrans_masuk;
                    } else if ($qty < $qtyold) {
                        $barangtrans_keluar = $qtyold - $qty;
                        $barangtrans_jenis = 'PENGURANGAN BARANG RUSAK';
                        $barangtrans_status = 'KELUAR';
                        $barangtrans_akhir = $barangtrans_awal - $barangtrans_keluar;
                    }

                    $datastock = array(
                        't_trans_id'      => $barangrusak_id,
                        't_transdet_id'   => $barangrusakdet_id,
                        'barangtrans_jenis' => $barangtrans_jenis,
                        'barangtrans_no'  => $barangrusak_no,
                        'barangtrans_tgl' => $barangrusak_tgl,
                        'm_barang_id'  => $val['m_barang_id'],
                        'm_barangsatuan_id' => $val['m_satuan_id'],
                        'm_satuan_id' => $val['m_satuan_id'],
                        'barangtrans_jml' => $val['barangrusakdet_qty'],
                        'barangtrans_konv' => 1,
                        'barangtrans_awal' => $barangtrans_awal,
                        'barangtrans_masuk' => $barangtrans_masuk,
                        'barangtrans_keluar' => $barangtrans_keluar,
                        'barangtrans_akhir' => $barangtrans_akhir,
                        'barangtrans_status' => $barangtrans_status
                    );

                    if (strtotime($barangrusak_tgl) < strtotime(date('Y-m-d'))) {
                        /* jika melakukan backdate */
                        $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                        $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                        $where = "WHERE barangtrans_tgl >= '" . $barangrusak_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                        query_update($this->conn2, 't_barangtrans', $field, $where);
                    }

                    $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                                'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_keluar', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
                    $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                                $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_keluar'], $datastock['barangtrans_akhir'], $barangtrans_status, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);

                    /* bahan produksi galon tidak rusak dikurangi */
                    $bahanproduksi = $this->model->getBahanBaku($val['m_barang_id'], 'rusak');
                    foreach ($bahanproduksi as $key => $valbb) {
                        $barangtrans_awal = $this->model->getStokAkhir($valbb['m_barang_id'], $data['barangrusak_tgl']);
                        $barangtrans_masuk  = 0;
                        $barangtrans_keluar = 0;
                        $barangtrans_jenis = '';
                        $barangtrans_status = '';
                        $barangtrans_akhir = 0;
                        if ($qty < $qtyold) {
                            $barangtrans_keluar = ($qty - $qtyold) * $valbb['bahanbrg_qty'];
                            $barangtrans_jenis = 'BATAL RUSAK';
                            $barangtrans_status = 'MASUK';
                            $barangtrans_akhir = ($barangtrans_awal + $barangtrans_keluar) * $valbb['bahanbrg_qty'];
                        } else if ($qty > $qtyold) {
                            $barangtrans_masuk = ($qtyold - $qty) * $valbb['bahanbrg_qty'];
                            $barangtrans_jenis = 'BARANG RUSAK';
                            $barangtrans_status = 'KELUAR';
                            $barangtrans_akhir = ($barangtrans_awal - $barangtrans_masuk) * $valbb['bahanbrg_qty'];
                        }
                        $datastock = array(
                            't_trans_id'      => $barangrusak_id,
                            't_transdet_id'   => $barangrusakdet_id,
                            'barangtrans_jenis' => $barangtrans_jenis,
                            'barangtrans_no'  => $barangrusak_no,
                            'barangtrans_tgl' => $barangrusak_tgl,
                            'm_barang_id'  => $valbb['m_barang_id'],
                            'm_barangsatuan_id' => $valbb['m_satuan_id'],
                            'm_satuan_id' => $valbb['m_satuan_id'],
                            'barangtrans_jml' => $val['barangrusakdet_qty']*$valbb['bahanbrg_qty'],
                            'barangtrans_konv' => 1,
                            'barangtrans_awal' => $barangtrans_awal,
                            'barangtrans_masuk' => $barangtrans_masuk,
                            'barangtrans_keluar' => $barangtrans_keluar,
                            'barangtrans_akhir' => $barangtrans_akhir,
                            'barangtrans_status' => $barangtrans_status
                        );
                        if (strtotime($barangrusak_tgl) < strtotime(date('Y-m-d'))) {
                            /* jika melakukan backdate */
                            $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                            $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                            $where = "WHERE barangtrans_tgl >= '" . $barangrusak_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
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

        if ($barangrusak_id > 0) {
            $return["code"] = "200";
            $return["id"] = $barangrusak_id;
        }

        echo json_encode($return);
    }

    public function nonaktifdetail($data, $barangrusak_no, $barangrusak_tgl)
    {
        # code...
    }

}

$barangrusak = new C_barangrusak($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'submit':
        $barangrusak->submit($_POST);
        break;
    case 'getbarang':
        $barangrusak->getBarang();
        break;
    case 'getbarangrusak':
        $barangrusak->getBarangRusak();
        break;
    case 'gethargakontrak':
        $barangrusak->getHargaKontrak($_POST);
        break;
    case 'formtransaksi':
        $barangrusak->formTransaksi($_GET);
        break;
    default:
        templateAdmin($conn2, '../views/barangrusak/v_barangrusak.php', NULL, 'TRANSAKSI', 'BARANG RUSAK');
        break;
}