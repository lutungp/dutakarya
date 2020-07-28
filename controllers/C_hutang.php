<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_hutang.php';

class C_hutang
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

        $this->model = new M_hutang($conn, $conn2, $config);
    }

    public function getHutang()
    {
        $result = $this->model->getHutang();

        echo json_encode($result);
    }

    public function getRekanan($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $jenis = isset($data['jenis']) ? $data['jenis'] : 'pabrik';
        $result = $this->model->getRekanan($search, $jenis);
        echo json_encode($result);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        $hutang_id = isset($data['id']) ? $data['id'] : 0;
        if ($hutang_id > 0) {
            $result['datahutang'] = $this->model->getDataHutang($hutang_id);
            $result['datahutangdetail'] = $this->model->getDataHutangDetail($hutang_id);
        }
        
        templateAdmin($this->conn2, '../views/hutang/v_formhutang.php', json_encode($result), 'KEUANGAN', 'PELUNASAN HUTANG');
    }

    public function getMaklondet($data)
    {
        $rekanan_id = $data["m_rekanan_id"];
        $tanggal = $data["hutang_tgl"];
        $result = $this->model->getMaklondet($rekanan_id, $tanggal);

        echo json_encode($result);
    }

    public function simpanHutang($data)
    {
        $hutang_id = $data['hutang_id'];
        $hutang_no = $data['hutang_no'];
        $hutang_tgl = $data['hutang_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];
        $action = false;
        if ($hutang_id > 0) {
            $fieldSave = ['hutang_tgl', 'm_rekanan_id', 'hutang_updated_by', 'hutang_updated_date', 'hutang_revised'];
            $dataSave = [$hutang_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'hutang_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE hutang_id = " . $data['hutang_id'];
            $action = query_update($this->conn2, 't_hutang_lunas', $field, $where);
        } else {
            $hutang_no = getPenomoran($this->conn2, 'HL', 't_hutang_lunas', 'hutang_id', 'hutang_no', $hutang_tgl);
            $fieldSave = ['hutang_no', 'hutang_tgl', 'm_rekanan_id', 'hutang_created_by', 'hutang_created_date'];
            $dataSave = [$hutang_no, $hutang_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $hutang_id = query_create($this->conn2, 't_hutang_lunas', $fieldSave, $dataSave);
            $action = true;
        }

        if (isset($data['rows'])) {
            $hutangArr = [];
            foreach ($data['rows'] as $key => $val) {
                $hutangdet_id = $val['hutangdet_id'];
                if ($hutangdet_id > 0) {
                    $fieldSave = ['t_hutang_id', 't_maklon_id', 't_maklondet_id', 'm_barang_id', 'm_satuan_id', 't_maklondet_qty', 't_maklondet_subtotal', 't_maklondet_ppn',
                                 'hutangdet_tagihan', 'hutangdet_bayar', 'hutangdet_updated_by', 'hutangdet_updated_date', 'hutangdet_revised'];
                    $dataSave = [$hutang_id, $val['t_maklon_id'], $val['t_maklondet_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['t_maklondet_qty'], $val['t_maklondet_subtotal'], $val['t_maklondet_ppn'],
                                 $val['hutangdet_tagihan'], $val['hutangdet_bayar'], $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'hutangdet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE hutangdet_id = " . $val['hutangdet_id'];
                    $action = query_update($this->conn2, 't_hutang_lunasdet', $field, $where);
                } else {
                    $fieldSave = ['t_hutang_id', 't_maklon_id', 't_maklondet_id', 'm_barang_id', 'm_satuan_id', 't_maklondet_qty', 't_maklondet_subtotal', 't_maklondet_ppn',
                                 'hutangdet_tagihan', 'hutangdet_bayar', 'hutangdet_created_by', 'hutangdet_created_date'];
                    $dataSave = [$hutang_id, $val['t_maklon_id'], $val['t_maklondet_id'], $val['m_barang_id'], $val['m_satuan_id'], $val['t_maklondet_qty'], $val['t_maklondet_subtotal'], $val['t_maklondet_ppn'],
                                 $val['hutangdet_tagihan'], $val['hutangdet_bayar'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];

                    $hutangdet_id = query_create($this->conn2, 't_hutang_lunasdet', $fieldSave, $dataSave);
                }

                /* update maklon status maklon */
                $hutangdet_bayar = $val['hutangdet_bayar'] - $val['hutangdet_bayarold'];
                $hutangArr[] = array(
                    'hutang_id' => $hutang_id,
                    'hutang_no' => $hutang_no,
                    'hutangdet_id' => $hutangdet_id,
                    't_maklon_id' => $val['t_maklon_id'],
                    't_maklondet_id' => $val['t_maklondet_id'],
                    'operator' => $val['hutangdet_bayarold'] < $val['hutangdet_bayar'] ? '+' : '-',
                    'hutangdet_bayar' => $hutangdet_bayar > 0 ? $hutangdet_bayar : (-1) * $hutangdet_bayar
                );
            }
            $this->updateStatusMaklon($hutangArr);
        }

        if ($action) {
            $result['code'] = 200;
            $result['id'] = $hutang_id;
        } else {
            $result['code'] = 202;
        }

        echo json_encode($result);
    }

    public function updateStatusMaklon($hutangArr)
    {
        $fieldSave = ['t_hutanglunas_id', 't_hutanglunas_no', 't_hutanglunasdet_id'];
        foreach ($hutangArr as $key => $value) {
            $dataSave = [$value['hutang_id'], $value['hutang_no'], $value['hutangdet_id']];
            $field = '';
            foreach ($fieldSave as $keySave => $valSave) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                $field .= "$valSave = '$dataSave[$keySave]'" . $regex . " ";
            }

            $field .= "t_hutanglunasdet_bayar=" . 't_hutanglunasdet_bayar'.$value['operator'].$value['hutangdet_bayar'];
            $where = "WHERE maklondet_id = " . $value['t_maklondet_id'];
            query_update($this->conn2, 't_maklondet', $field, $where);
        }
    }

    public function batal($data)
    {
        $hutang_id = $data['hutang_id'];
        $fieldSave = ['hutang_aktif', 'hutang_void_by', 'hutang_void_date', 'hutang_void_alasan'];
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
        $where = "WHERE hutang_id = " . $hutang_id;
        $action = query_update($this->conn2, 't_hutang_lunas', $field, $where);

        $fieldSave = ['t_hutanglunasdet_bayar'];
        foreach ($data['rows'] as $key => $valpenagihan) {
            $dataSave = ['t_hutanglunasdet_bayar-'.$valpenagihan['hutangdet_bayar']];
            $field = '';
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
            $where = "WHERE maklondet_id = " . $valpenagihan['t_maklondet_id'];
            query_update($this->conn2, 't_maklondet', $field, $where);
        }

        $fieldSave = ['hutangdet_aktif'];
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
        $where = "WHERE t_hutang_id = " . $hutang_id;
        $action = query_update($this->conn2, 't_hutang_lunasdet', $field, $where);
        $result['code'] = 200;
        echo json_encode($result);
    }
}

$hutang = new C_hutang($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'gethutang':
        $hutang->getHutang();
        break;
    case 'submit':
        $hutang->simpanHutang($_POST);
        break;
    case 'formtransaksi':
        $hutang->formTransaksi($_GET);
        break;
    case 'getrekanan':
        $hutang->getRekanan($_GET);
        break;
    case 'getmaklondet':
        $hutang->getMaklondet($_POST);
        break;
    case 'batal':
        $hutang->batal($_POST);
        break;
    default:
        templateAdmin($conn2, '../views/hutang/v_hutang.php', NULL, 'KEUANGAN', 'PELUNASAN HUTANG');
    break;
}
