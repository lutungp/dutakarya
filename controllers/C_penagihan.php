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

    public function formTransaksi()
    {
        $result = NULL;
        templateAdmin($this->conn2, '../views/penagihan/v_formpenagihan.php', json_encode($result), 'KEUANGAN', 'PENAGIHAN');
    }

    public function getPengiriman($data)
    {
        $m_rekanan_id = $data['m_rekanan_id'];
        $penagihan_tgl = $data['penagihan_tgl'];
        $data = $this->model->getPengiriman($m_rekanan_id, $penagihan_tgl);

        echo json_encode($data);
    }

    public function submit($data)
    {
        $penagihan_id = $data['penagihan_id'];
        $penagihan_no = $data['penagihan_no'];
        $penagihan_tgl = $data['penagihan_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];

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
            query_update($this->conn2, 't_penagihan', $field, $where);
        } else {
            $penagihan_no = getPenomoran($this->conn2, 'TG', 't_penagihan', 'penagihan_id', 'penagihan_no', $penagihan_tgl);
            $fieldSave = ['penagihan_no', 'penagihan_tgl', 'm_rekanan_id', 'penagihan_created_by', 'penagihan_created_date'];
            $dataSave = [$penagihan_no, $penagihan_tgl, $data['m_rekanan_id'], $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $penagihan_id = query_create($this->conn2, 't_penagihan', $fieldSave, $dataSave);
        }
        $action = false;
        if (isset($data['rows'])) {
            $pengiriman_idArr = [];
            foreach ($data['rows'] as $key => $val) {
                $penagihandet_id = $val['penagihandet_id'];
                if ($penagihandet_id > 0) {
                    $fieldSave = ['t_penagihan_id', 't_pengiriman_id', 't_pengirimandet_id', 'penagihandet_subtotal', 'penagihandet_potongan', 'penagihandet_total', 'penagihandet_updated_by', 'penagihandet_updated_date', 'penagihandet_revised'];
                    $dataSave = [$penagihan_id, $val['t_pengiriman_id'], $val['t_pengirimandet_id'], $val['penagihandet_subtotal'], $val['penagihandet_potongan'], $val['penagihandet_total'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'penagihandet_revised+1'];
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
                    $fieldSave = ['t_penagihan_id', 't_pengiriman_id', 't_pengirimandet_id', 'penagihandet_subtotal', 'penagihandet_potongan', 'penagihandet_total', 'penagihandet_created_by', 'penagihandet_created_date'];
                    $dataSave = [$penagihan_id, $val['t_pengiriman_id'], $val['t_pengirimandet_id'], $val['penagihandet_subtotal'], $val['penagihandet_potongan'], $val['penagihandet_total'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
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

    public function exportpdf($data)
    {
        # code...
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
        $penagihan->formTransaksi();
        break;
    case 'getpengiriman':
        $penagihan->getPengiriman($_POST);
        break;
    case 'getpenagihan':
        $penagihan->getPenagihan();
        break;
    case 'exportpdf':
        $penagihan->exportpdf($_GET);
        break;
    default:
        templateAdmin($conn2, '../views/penagihan/v_penagihan.php', NULL, 'KEUANGAN', 'PENAGIHAN');
    break;
}
