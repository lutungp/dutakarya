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

    public function getJadwal($m_rekanan_id, $bulan, $tahun)
    {
        $sql = "SELECT
                    jadwal_id,
                    m_rekanan_id,
                    hari,
                    bulan,
                    tahun,
                    minggu1,
                    minggu2,
                    minggu3,
                    minggu4,
                    minggu5
                FROM t_jadwal WHERE m_rekanan_id = $m_rekanan_id AND bulan = $bulan AND tahun = $tahun";
        $qkirim = $this->conn2->query($sql);
        $rkirim = array();
        while ($val = $qkirim->fetch_array()) {
            $rkirim[] = array(
                'jadwal_id' => $val['jadwal_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'hari' => $val['hari'],
                'bulan' => $val['bulan'],
                'tahun' => $val['tahun'],
                'minggu1' => $val['minggu1'],
                'minggu2' => $val['minggu2'],
                'minggu3' => $val['minggu3'],
                'minggu4' => $val['minggu4'],
                'minggu5' => $val['minggu5'],
            );
        }

        return $rkirim;
    }
}