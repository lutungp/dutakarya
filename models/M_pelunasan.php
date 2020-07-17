<?php

class M_pelunasan
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPelunasan()
    {
        $sql = "SELECT
                    t_pelunasan.pelunasan_id,
                    t_pelunasan.pelunasan_no,
                    t_pelunasan.pelunasan_tgl,
                    t_pelunasan.t_penagihan_id,
                    t_penagihan.penagihan_no,
                    t_pelunasan.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    t_pelunasan.pelunasan_created_date,
                    m_user.user_nama 
                FROM
                    t_pelunasan
                    INNER JOIN t_penagihan ON t_penagihan.penagihan_id = t_pelunasan.t_penagihan_id
                    INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                    INNER JOIN m_user ON m_user.user_id = t_pelunasan.pelunasan_created_by 
                WHERE t_pelunasan.pelunasan_aktif = 'Y' ";

        $qpenagihan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenagihan->fetch_array()) {
            $result[] = array(
                'pelunasan_id' => $val['pelunasan_id'],
                'pelunasan_no' => $val['pelunasan_no'],
                'pelunasan_tgl' => $val['pelunasan_tgl'],
                't_penagihan_id' => $val['t_penagihan_id'],
                'penagihan_no' => $val['penagihan_no'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'pelunasan_created_date' => $val['pelunasan_created_date'],
                'user_nama' => $val['user_nama'],
            );
        }

        return $result;
    }

}