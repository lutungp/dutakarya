<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_stockopname.php';

class C_stockopname
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

        $this->model = new M_stockopname($conn, $conn2, $config);
    }

    public function StockOpname()
    {
        templateAdmin($this->conn2, '../views/stockopname/v_stockopname.php', NULL, 'TRANSAKSI', 'STOCK OPNAME');
    }

    public function getStockOpname()
    {
        $data = $this->model->getStockOpname();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $stockopname_id = $data['id'];
            $result['stockopname_id'] = $stockopname_id;
            $result['datastockopname'] = $this->model->getStockOpnameData($stockopname_id);
            $result['datastockopnamedetail'] = $this->model->getStockOpnameDataDetail($stockopname_id);
        }
        
        templateAdmin($this->conn2, '../views/stockopname/v_formstockopname.php', json_encode($result), 'TRANSAKSI', 'STOCK OPNAME');
    }

    public function getBarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function getQty($data)
    {
        $barang_id = $data['barang_id'];
        $tanggal = $data['tanggal'];
        $result = $this->model->getStokAkhir($barang_id, $tanggal);

        echo $result;
    }

    public function submit($data)
    {
        $stockopname_tgl = date('Y-m-d', strtotime($data['stockopname_tgl']));
        $stockopname_id = $data['stockopname_id'];
        $stockopname_no = $data['stockopname_no'];
        
        if ($stockopname_id > 0) {
            $fieldSave = ['stockopname_tgl', 'stockopname_updated_by', 'stockopname_updated_date', 'stockopname_revised'];
            $dataSave = [$stockopname_tgl, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'stockopname_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE stockopname_id = " . $data['stockopname_id'];
            query_update($this->conn2, 't_stockopname', $field, $where);
        } else {
            $stockopname_no = getPenomoran($this->conn2, 'KM', 't_stockopname', 'stockopname_id', 'stockopname_no', $stockopname_tgl);
            $fieldSave = ['stockopname_no', 'stockopname_tgl', 'stockopname_created_by', 'stockopname_created_date'];
            $dataSave = [$stockopname_no, $stockopname_tgl, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $stockopname_id = query_create($this->conn2, 't_stockopname', $fieldSave, $dataSave);
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $stockopnamedet_id = $val['stockopnamedet_id'];
                if ($stockopnamedet_id > 0) {
                    $fieldSave = ['t_stockopname_id', 'm_barang_id', 'm_satuan_id', 't_barangtrans_akhir', 'stockopnamedet_qty', 'stockopnamedet_updated_by', 'stockopnamedet_updated_date', 'stockopnamedet_revised'];
                    $dataSave = [$stockopname_id, $val['m_barang_id'], $val['m_satuan_id'], $val['t_barangtrans_akhir'], $val['stockopnamedet_qty'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'stockopnamedet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE stockopnamedet_id = " . $val['stockopnamedet_id'];
                    query_update($this->conn2, 't_stockopnamedet', $field, $where);
                } else {
                    $fieldSave = ['t_stockopname_id', 'm_barang_id', 'm_satuan_id', 'stockopnamedet_qty', 'stockopnamedet_created_by', 'stockopnamedet_created_date'];
                    $dataSave = [$stockopname_id, $val['m_barang_id'], $val['m_satuan_id'], $val['t_barangtrans_akhir'], $val['stockopnamedet_qty'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $stockopnamedet_id = query_create($this->conn2, 't_stockopnamedet', $fieldSave, $dataSave);
                }

                $qty    = $val['stockopnamedet_qty'];
                $barangtrans_awal = $this->model->getStokAkhir($val['m_barang_id'], $stockopname_tgl);

                $datastock = array(
                    't_trans_id'      => $stockopname_id,
                    't_transdet_id'   => $stockopnamedet_id,
                    'barangtrans_jenis' => 'STOCK OPNAME',
                    'barangtrans_no'  => $stockopname_no,
                    'barangtrans_tgl' => $stockopname_tgl,
                    'm_barang_id'  => $val['m_barang_id'],
                    'm_barangsatuan_id' => $val['m_satuan_id'],
                    'm_satuan_id' => $val['m_satuan_id'],
                    'barangtrans_jml' => $val['stockopnamedet_qty'],
                    'barangtrans_konv' => $val['satkonv_nilai'],
                    'barangtrans_awal' => $barangtrans_awal,
                    'barangtrans_masuk' => 0,
                    'barangtrans_keluar' => 0,
                    'barangtrans_akhir' => $qty,
                    'barangtrans_status' => 'ADJUSMENT'
                );

                if (strtotime($stockopname_tgl) < strtotime(date('Y-m-d'))) {
                    /* jika melakukan backdate */
                    // $fieldBackDate = ['barangtrans_awal', 'barangtrans_akhir'];

                    // $field = " barangtrans_awal=barangtrans_awal+(($barangtrans_masuk-$barangtrans_keluar)),  barangtrans_akhir=barangtrans_akhir+(($barangtrans_masuk-$barangtrans_keluar))";
                    // $where = "WHERE barangtrans_tgl >= '" . $stockopname_tgl . "' AND barangtrans_created_date < now() AND m_barang_id = " . $datastock['m_barang_id'];
                    // query_update($this->conn2, 't_barangtrans', $field, $where);
                }

                $fieldTransSave = ['barangtrans_no', 'barangtrans_tgl', 'barangtrans_jenis', 't_trans_id', 't_transdet_id', 'm_barang_id', 'm_barangsatuan_id', 'm_satuan_id', 
                            'barangtrans_jml', 'barangtrans_konv', 'barangtrans_awal', 'barangtrans_masuk', 'barangtrans_keluar', 'barangtrans_akhir', 'barangtrans_status', 'barangtrans_created_by', 'barangtrans_created_date'];
                $dataTransSave = [$datastock['barangtrans_no'], $datastock['barangtrans_tgl'], $datastock['barangtrans_jenis'], $datastock['t_trans_id'], $datastock['t_transdet_id'], $datastock['m_barang_id'], $datastock['m_barangsatuan_id'], $datastock['m_satuan_id'], 
                            $datastock['barangtrans_jml'], $datastock['barangtrans_konv'], $datastock['barangtrans_awal'], $datastock['barangtrans_masuk'], $datastock['barangtrans_keluar'], $datastock['barangtrans_akhir'], 'ADJUSMENT', $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                query_create($this->conn2, 't_barangtrans', $fieldTransSave, $dataTransSave);
            }
        }

        $return["code"] = "202";

        if ($stockopname_id > 0) {
            $return["code"] = "200";
            $return["id"] = $stockopname_id;
        }

        echo json_encode($return);
    }

}

$stockopname = new C_stockopname($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'getstockopname':
        $stockopname->getStockOpname();
        break;
    case 'formtransaksi':
        $stockopname->formTransaksi($_GET);
        break;
    case 'getbarang':
        $stockopname->getBarang();
        break;
    case 'getqty':
        $stockopname->getQty($_POST);
        break;
    case 'submit':
        $stockopname->submit($_POST);
        break;
    default:
        $stockopname->StockOpname();
    break;
}
