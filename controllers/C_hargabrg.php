<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_hargabrg.php';

class C_hargabrg
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_hargabrg($conn, $conn2, $config);
    }

    public function getBarangHna()
    {
        $data = $this->model->getBarangHna();
        echo json_encode($data);
    }

    public function formTransaksi($data)
    {
        $result = NULL;
        if (isset($data['id']) > 0) {
            $baranghna_id = $data['id'];
            $result['baranghna_id'] = $baranghna_id;
            $baranghna = $this->model->getBaranghnaData($baranghna_id);
            $result['databaranghna'] = $baranghna;
            $result['databaranghnadetail'] = $this->model->getBaranghnaDataDetail($baranghna_id, $baranghna->baranghna_tglawal);
        }
        
        templateAdmin($this->conn2, '../views/baranghna/v_formhargabrg.php', json_encode($result), "MASTER", "SETUP HARGA BRG");
    }

    public function getbarang()
    {
        $result = $this->model->getBarangSatkonv();
        echo json_encode($result);
    }

    public function submit($data)
    {
        $baranghna_tglawal = date('Y-m-d', strtotime($data['baranghna_tglawal']));
        $baranghna_id = $data['baranghna_id'];
        $baranghna_no = $data['baranghna_no'];
        if ($baranghna_id > 0) {
            $fieldSave = ['baranghna_tglawal', 'baranghna_updated_by', 'baranghna_updated_date', 'baranghna_revised'];
            $dataSave = [$baranghna_tglawal, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'baranghna_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE baranghna_id = " . $data['baranghna_id'];
            query_update($this->conn2, 't_baranghna', $field, $where);
        } else {
            $baranghna_no = getPenomoran($this->conn2, 'HNA', 't_baranghna', 'baranghna_id', 'baranghna_no', $baranghna_tglawal);
            $fieldSave = ['baranghna_no', 'baranghna_tglawal', 'baranghna_created_by', 'baranghna_created_date'];
            $dataSave = [$baranghna_no, $baranghna_tglawal, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $baranghna_id = query_create($this->conn2, 't_baranghna', $fieldSave, $dataSave);
        }

        
        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail']);
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $baranghnadet_id = $val['baranghnadet_id'];
                if ($baranghnadet_id > 0) {
                    $fieldSave = ['t_baranghna_id', 'm_barang_id', 'baranghnadet_tglawal', 'baranghnadet_harga', 'baranghnadet_updated_by', 'baranghnadet_updated_date', 'baranghnadet_revised'];
                    $dataSave = [$baranghna_id, $val['m_barang_id'], $baranghna_tglawal, $val['baranghnadet_harga'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'baranghnadet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE baranghnadet_id = " . $val['baranghnadet_id'];
                    query_update($this->conn2, 't_baranghna_detail', $field, $where);
                } else {
                    $fieldSave = ['t_baranghna_id', 'm_barang_id', 'baranghnadet_tglawal', 'baranghnadet_harga', 'baranghnadet_created_by', 'baranghnadet_created_date'];
                    $dataSave = [$baranghna_id, $val['m_barang_id'], $baranghna_tglawal, $val['baranghnadet_harga'],  $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $baranghnadet_id = query_create($this->conn2, 't_baranghna_detail', $fieldSave, $dataSave);
                }
            }
        }

        if ($baranghna_id > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function nonaktifdetail($data, $baranghna_no, $baranghna_tglawal)
    {
        $baranghnadet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($baranghnadet_idArr, $data[$i]['baranghnadet_id']);
        }
        $baranghnadet_id = implode(',', $baranghnadet_idArr);
        
        $fieldSave = ["baranghnadet_aktif","baranghnadet_void_by", "baranghnadet_void_date"];
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
        $where = "WHERE baranghnadet_id IN (".$baranghnadet_id.")";
        $action = query_update($this->conn2, 't_baranghna_detail', $field, $where);
    }

    public function getLastHNA($data)
    {
        $barang_id = $data['barang_id'];
        $baranghna_tglawal = $data['baranghna_tglawal'];
        $data = $this->model->getLastHNA($barang_id, $baranghna_tglawal);
        echo json_encode($data);
    }

}

$hargabrg = new C_hargabrg($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getbaranghna':
        $hargabrg->getBarangHna();
        break;
    
    case 'getbarang':
        $hargabrg->getBarang();
        break;

    case 'formtransaksi':
        $hargabrg->formTransaksi($_GET);
        break;
    
    case 'submit':
        $hargabrg->submit($_POST);
        break;

    case 'getlasthna':
        $hargabrg->getLastHNA($_POST);
        break;

    default:
        templateAdmin($conn2, '../views/baranghna/v_hargabrg.php', NULL, "MASTER", "SETUP HARGA BRG");
    break;
}