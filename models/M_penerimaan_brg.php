<?php

class M_penerimaan_brg
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPenerimaan()
    {
        $sql = " SELECT 
                    penerimaan_id,
                    penerimaan_no,
                    penerimaan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    penerimaan_catatan
                FROM t_penerimaan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penerimaan.m_rekanan_id
                WHERE penerimaan_aktif = 'Y'";
        $qpenerimaan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenerimaan->fetch_array()) {
            $result[] = array(
                'penerimaan_id' => $val['penerimaan_id'],
                'penerimaan_no' => $val['penerimaan_no'],
                'penerimaan_tgl' => $val['penerimaan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'penerimaan_catatan' => $val['penerimaan_catatan'],
            );
        }

        return $result;
    }

}