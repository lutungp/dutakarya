<?php

class M_jadwalkirim
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getJadwal($m_rekanan_id, $bulan, $tahun, $m_barang_id)
    {
        $sql = "SELECT
                    jadwal_id,
                    m_rekanan_id,
                    m_pegdriver_id,
                    driver.pegawai_nama AS m_pegdriver_nama,
                    m_peghelper_id,
                    helper.pegawai_nama AS m_peghelper_nama,
                    hari,
                    bulan,
                    tahun,
                    minggu1,
                    minggu2,
                    minggu3,
                    minggu4,
                    minggu5,
                    m_barang_id
                FROM t_jadwal
                LEFT JOIN m_pegawai AS driver ON driver.pegawai_id = t_jadwal.m_pegdriver_id
                LEFT JOIN m_pegawai AS helper ON helper.pegawai_id = t_jadwal.m_peghelper_id
                WHERE m_rekanan_id = $m_rekanan_id AND bulan = $bulan AND tahun = $tahun AND m_barang_id = $m_barang_id";
        $qkirim = $this->conn2->query($sql);
        $rkirim = array();

        if ($qkirim) {
            while ($val = $qkirim->fetch_array()) {
                $rkirim[] = array(
                    'jadwal_id' => $val['jadwal_id'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'm_pegdriver_id' => $val['m_pegdriver_id'],
                    'm_pegdriver_nama' => $val['m_pegdriver_nama'],
                    'm_peghelper_id' => $val['m_peghelper_id'],
                    'm_peghelper_nama' => $val['m_peghelper_nama'],
                    'hari' => $val['hari'],
                    'bulan' => $val['bulan'],
                    'tahun' => $val['tahun'],
                    'minggu1' => $val['minggu1'],
                    'minggu2' => $val['minggu2'],
                    'minggu3' => $val['minggu3'],
                    'minggu4' => $val['minggu4'],
                    'minggu5' => $val['minggu5']
                );
            }
        }
        
        if (count($rkirim) < 7 && count($rkirim) > 1) {
            $hariexist = array();
            foreach ($rkirim as $key => $value) {
                array_push($hariexist, $value['hari']);
            }

            for ($i=1; $i < 8; $i++) { 
                if (!in_array($i, $hariexist)) {
                    $rkirim[] = array(
                        'jadwal_id' => 0,
                        'm_rekanan_id' => $m_rekanan_id,
                        'm_pegdriver_id' => 0,
                        'm_pegdriver_nama' => '',
                        'm_peghelper_id' => 0,
                        'm_peghelper_nama' => '',
                        'hari' => $i,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'minggu1' => 0,
                        'minggu2' => 0,
                        'minggu3' => 0,
                        'minggu4' => 0,
                        'minggu5' => 0
                    );
                }
            }
        }
        usort($rkirim, fn($a, $b) => strcmp($a['hari'], $b['hari']));
        return  $rkirim;
    }

    public function getBarang($search)
    {
        $sql = "SELECT m_barang.barang_id AS id, m_barang.barang_nama AS text FROM m_barang";
        if ($search <> '') {
            $sql .= " WHERE m_barang.barang_nama LIKE '%".$search."%' ";
        }
        $qbarang = $this->conn2->query($sql);
        $barang = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($barang, $result);
        }
        return $barang;
    }

    public function getPegawai()
    {
        $sql = "SELECT
                m_pegawai.pegawai_id,
                m_pegawai.pegawai_nama,
                m_pegawai.pegawai_bagian
            FROM m_pegawai
            WHERE pegawai_aktif = 'Y'";
        
        $qpegawai = $this->conn2->query($sql);
        $pegawai = [];
        while ($result = $qpegawai->fetch_array(MYSQLI_ASSOC)) {
            $pegawai[] = array(
                'pegawai_id' => $result['pegawai_id'],
                'pegawai_nama' => $result['pegawai_nama'],
                'pegawai_bagian' => $result['pegawai_bagian'],
            );
        }
        return $pegawai;
    }
}