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
    default:
        templateAdmin($conn2, '../views/pelunasan/v_pelunasan.php', NULL, 'KEUANGAN', 'PELUNASAN');
    break;
}

