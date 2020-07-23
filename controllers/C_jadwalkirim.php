<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_jadwalkirim.php';

class C_jadwalkirim
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_jadwalkirim($conn, $conn2, $config);
    }

    public function getBulan()
    {
        $bulan[0] = array(
            'value' => 1,
            'text' => 'Januari',
        );
        $bulan[1] = array(
            'value' => 2,
            'text' => 'Februari',
        );
        $bulan[2] = array(
            'value' => 3,
            'text' => 'Maret',
        );
        $bulan[3] = array(
            'value' => 4,
            'text' => 'April',
        );
        $bulan[4] = array(
            'value' => 5,
            'text' => 'Mei',
        );
        $bulan[5] = array(
            'value' => 6,
            'text' => 'Juni',
        );
        $bulan[6] = array(
            'value' => 7,
            'text' => 'Juli',
        );
        $bulan[7] = array(
            'value' => 8,
            'text' => 'Agustus',
        );
        $bulan[8] = array(
            'value' => 9,
            'text' => 'September',
        );
        $bulan[9] = array(
            'value' => 10,
            'text' => 'Oktober',
        );
        $bulan[10] = array(
            'value' => 11,
            'text' => 'November',
        );
        $bulan[11] = array(
            'value' => 12,
            'text' => 'Desember',
        );

        return $bulan;
    }

    public function getTahun()
    {
        $years = [];
        $yearstart = 2020;
        $yearnow = intval(date('Y'));
        array_push($years, 2020);
        for ($i=0; $i < ($yearnow-$yearstart); $i++) { 
            array_push($years, ($yearstart + $i));
        }

        return $years;
    }

    public function submit($data)
    {
        foreach ($data['rows'] as $key => $value) {
            $m_pegdriver_id = isset($value['m_pegdriver_id']) ? $value['m_pegdriver_id'] : 0;
            $m_peghelper_id = isset($value['m_peghelper_id']) ? $value['m_peghelper_id'] : 0;
            if ($value['jadwal_id'] > 0) {
                $fieldSave = ['m_pegdriver_id', 'm_peghelper_id', 'hari', 'bulan', 'tahun', 'rit', 'minggu1', 'minggu2', 'minggu3', 'minggu4', 'minggu5', 'jadwal_updated_by', 'jadwal_updated_date', 'jadwal_revised'];
                $dataSave = [$m_pegdriver_id, $m_peghelper_id, $value['hari'], $value['bulan'], $value['tahun'], $value['rit'], $value['minggu1'], $value['minggu2'], $value['minggu3'], $value['minggu4'], $value['minggu5'], $_SESSION["USER_ID"], date('Y-m-d H:i:s'), 'jadwal_revised+1'];
                
                $field = '';
                foreach ($fieldSave as $key => $val) {
                    $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                    if (!preg_match("/revised/i", $val)) {
                        $field .= "$val = '$dataSave[$key]'" . $regex . " ";
                    } else {
                        $field .= "$val = $dataSave[$key]" . $regex . " ";
                    }
                }
                $where = "WHERE jadwal_id = " . $value['jadwal_id'];
                $action = query_update($this->conn2, 't_jadwal', $field, $where);
            } else {
                $fieldSave = ['m_pegdriver_id', 'm_peghelper_id', 'm_rekanan_id', 'm_barang_id', 'hari', 'bulan', 'tahun', 'rit', 'minggu1', 'minggu2', 'minggu3', 'minggu4', 'minggu5', 'jadwal_created_by', 'jadwal_created_date'];
                $dataSave = [$m_pegdriver_id, $m_peghelper_id, $value['m_rekanan_id'], $value['m_barang_id'], $value['hari'], $value['bulan'], $value['tahun'], $value['rit'], $value['minggu1'], $value['minggu2'], $value['minggu3'], $value['minggu4'], $value['minggu5'], $_SESSION["USER_ID"], date('Y-m-d H:i:s')];
                $barang_id = query_create($this->conn2, 't_jadwal', $fieldSave, $dataSave);
            }
            
        }

        echo "200";
    }

    public function getJadwal($data)
    {
        $result = $this->model->getJadwal($data['m_rekanan_id'], $data['bulan'], $data['tahun'], $data['m_barang_id'], $data['rit']);
        echo json_encode($result);
    }

    public function getBarang($data)
    {
        $search = isset($data['searchTerm']) ? $data['searchTerm'] : '';
        $result = $this->model->getBarang($search);
        echo json_encode($result);
    }

    public function getPegawai()
    {
        $pegawai = $this->model->getPegawai();
        echo json_encode($pegawai);
    }
}

$jadwal = new C_jadwalkirim($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'submit':
        $jadwal->submit($_POST);
        break;
    
    case 'getjadwal':
        $jadwal->getJadwal($_POST);
        break;
    
    case 'getbarang':
        $jadwal->getBarang($_GET);
        break;

    case 'getpegawai':
        $jadwal->getPegawai();
        break;

    default:
        $data['tahun'] = $jadwal->getTahun();
        $data['bulan'] = $jadwal->getBulan();
        templateAdmin($conn2, '../views/v_jadwalkirim.php', json_encode($data), "TRANSAKSI", "JADWAL KIRIM");
    break;
}