<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_kontrakrekanan.php';

class C_kontrakrekanan
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_kontrakrekanan($conn, $conn2, $config);
    }

    public function getHargaKontrak()
    {
        $data = $this->model->getHargaKontrak();
        echo json_encode($data);
    }

    function formTransaksi($data){
        $result = NULL;
        $hargakontrak_id = isset($data['id']) ? $data['id'] : 0;
        if ($hargakontrak_id > 0) {
            $result['hargakontrak'] = $this->model->getKontrakData($hargakontrak_id);
            $result['hargakontrakdetail'] = $this->model->getKontrakDataDetail($hargakontrak_id);
        }
        templateAdmin($this->conn2, '../views/kontrak/v_formkontrakrekanan.php', json_encode($result), "KEUANGAN", "KONTRAK REKANAN");
    }

    public function submit($data)
    {
        $hargakontrak_id = $data['hargakontrak_id'];
        $hargakontrak_no = $data['hargakontrak_no'];
        $hargakontrak_tgl = $data['hargakontrak_tgl'];
        $m_rekanan_id = $data['m_rekanan_id'];
        
        if ($hargakontrak_id > 0) {
            $fieldSave = ['hargakontrak_tgl', 'm_rekanan_id', 'hargakontrak_updated_by', 'hargakontrak_updated_date', 'hargakontrak_revised'];
            $dataSave = [$hargakontrak_tgl, $m_rekanan_id, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'hargakontrak_revised+1'];
            $field = "";
            foreach ($fieldSave as $key => $value) {
                $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                if (!preg_match("/revised/i", $value)) {
                    $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                } else {
                    $field .= "$value = $dataSave[$key]" . $regex . " ";
                }
            }
            $where = "WHERE hargakontrak_id = " . $data['hargakontrak_id'];
            query_update($this->conn2, 't_hargakontrak', $field, $where);
        } else {
            $hargakontrak_no = getPenomoran($this->conn2, 'KT', 't_hargakontrak', 'hargakontrak_id', 'hargakontrak_no', $hargakontrak_tgl);
            $fieldSave = ['hargakontrak_no', 'm_rekanan_id' , 'hargakontrak_tgl', 'hargakontrak_created_by', 'hargakontrak_created_date'];
            $dataSave = [$hargakontrak_no, $m_rekanan_id, $hargakontrak_tgl, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
            $hargakontrak_id = query_create($this->conn2, 't_hargakontrak', $fieldSave, $dataSave);
        }

        if (isset($data['hapusdetail'])) {
            $this->nonaktifdetail($data['hapusdetail']);
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $val) {
                $hargakontrakdet_id = $val['hargakontrakdet_id'];
                $hargakontrakdet_sewa = $val['hargakontrakdet_sewa'] == 'true' ? 'Y' : 'N';
                $hargakontrakdet_brgrusak = $val['hargakontrakdet_brgrusak'] == 'true' ? 'Y' : 'N';
                $hargakontrakdet_ppn = $val['hargakontrakdet_ppn'] == 'true' ? 'Y' : 'N';
                if ($hargakontrakdet_id > 0) {
                    $fieldSave = ['t_hargakontrak_id', 'm_rekanan_id', 'm_barang_id', 'm_satuan_id', 'hargakontrakdet_tgl', 'hargakontrakdet_harga', 
                                  'hargakontrakdet_ppn', 'hargakontrakdet_sewa', 'hargakontrakdet_jmlsewa', 'hargakontrakdet_brgrusak', 'hargakontrakdet_updated_by', 'hargakontrakdet_updated_date', 'hargakontrakdet_revised'];
                    $dataSave = [$hargakontrak_id, $m_rekanan_id, $val['m_barang_id'], $val['m_satuan_id'], $hargakontrak_tgl, $val['hargakontrakdet_harga'], 
                                 $hargakontrakdet_ppn, $hargakontrakdet_sewa, $val['hargakontrakdet_jmlsewa'], $hargakontrakdet_brgrusak, $_SESSION["USER_ID"], date("Y-m-d H:i:s"), 'hargakontrakdet_revised+1'];
                    $field = "";
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE hargakontrakdet_id = " . $val['hargakontrakdet_id'];
                    query_update($this->conn2, 't_hargakontrak_detail', $field, $where);
                } else {
                    $fieldSave = ['t_hargakontrak_id', 'm_rekanan_id', 'm_barang_id', 'm_satuan_id', 'hargakontrakdet_tgl', 'hargakontrakdet_harga', 
                                    'hargakontrakdet_ppn', 'hargakontrakdet_sewa', 'hargakontrakdet_jmlsewa', 'hargakontrakdet_brgrusak', 'hargakontrakdet_created_by', 'hargakontrakdet_created_date'];
                    $dataSave = [$hargakontrak_id, $m_rekanan_id, $val['m_barang_id'], $val['m_satuan_id'], $hargakontrak_tgl, $val['hargakontrakdet_harga'], 
                                    $hargakontrakdet_ppn, $hargakontrakdet_sewa, $val['hargakontrakdet_jmlsewa'], $hargakontrakdet_brgrusak, $_SESSION["USER_ID"], date("Y-m-d H:i:s")];
                    $hargakontrakdet_id = query_create($this->conn2, 't_hargakontrak_detail', $fieldSave, $dataSave);
                }
            }
        }

        if ($hargakontrak_id>0) {
            echo '200';
        } else {
            echo '202';
        }
    }

    public function nonaktifdetail($data)
    {
        $hargakontrakdet_idArr = array();
        for ($i=0; $i < count($data); $i++) { 
            array_push($hargakontrakdet_idArr, $data[$i]['hargakontrakdet_id']);
        }
        $hargakontrakdet_id = implode(',', $hargakontrakdet_idArr);
        
        $fieldSave = ["hargakontrakdet_aktif"];
        $dataSave = ["N"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE hargakontrakdet_id IN (".$hargakontrakdet_id.")";
        $action = query_update($this->conn2, 't_hargakontrak_detail', $field, $where);
    }

    public function batal($data)
    {
        $hargakontrak_id = $data['hargakontrak_id'];
        $alasan = $data['alasan'];
        $fieldSave = ["hargakontrak_aktif", "hargakontrak_void_by", "hargakontrak_void_date", "hargakontrak_void_alasan"];
        $dataSave = ["N", $_SESSION["USER_ID"], date("Y-m-d H:i:s"), $alasan];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }

        $where = "WHERE hargakontrak_id = $hargakontrak_id";
        $action = query_update($this->conn2, 't_hargakontrak', $field, $where);

        $fieldSave = ["hargakontrakdet_aktif"];
        $dataSave = ["N"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE t_hargakontrak_id = $hargakontrak_id";
        $action = query_update($this->conn2, 't_hargakontrak_detail', $field, $where);

        if ($action) {
            echo '200';
        } else {
            echo '202';
        }
    }

}

$kontrakrekanan = new C_kontrakrekanan($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'formtransaksi':
        $kontrakrekanan->formTransaksi($_GET);
        break;
    case 'gethargakontrak':
        $kontrakrekanan->getHargaKontrak();
        break;
    case 'submit':
        $kontrakrekanan->submit($_POST);
        break;
    case 'batal':
        $kontrakrekanan->batal($_POST);
        break;
    default:
        templateAdmin($conn2, '../views/kontrak/v_kontrakrekanan.php', NULL, "KEUANGAN", "KONTRAK REKANAN");
    break;
}