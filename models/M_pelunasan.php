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
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'pelunasan_created_date' => $val['pelunasan_created_date'],
                'user_nama' => $val['user_nama'],
            );
        }

        return $result;
    }

    public function getPenagihan($m_rekanan_id, $pelunasan_tgl)
    {
        $sql = "SELECT
                    t_penagihan.penagihan_id,
                    t_penagihan.penagihan_no,
                    t_penagihan.penagihan_tgl,
                    t_penagihan.m_rekanan_id,
                    SUM(t_penagihan_detail.penagihandet_total) AS penagihandet_total,
                    t_penagihan.t_pelunasandet_bayar
                FROM t_penagihan
                INNER JOIN t_penagihan_detail ON t_penagihan_detail.t_penagihan_id = t_penagihan.penagihan_id
                WHERE t_penagihan_detail.penagihandet_aktif = 'Y' AND t_penagihan.penagihan_aktif = 'Y'
                GROUP BY t_penagihan.penagihan_id, t_penagihan.penagihan_no, t_penagihan.penagihan_tgl, t_penagihan.m_rekanan_id, t_penagihan.t_pelunasandet_bayar
                ORDER BY t_penagihan.penagihan_tgl ASC ";
        $qpenagihan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenagihan->fetch_array()) {

            if (($val['penagihandet_total']-$val['t_pelunasandet_bayar']) > 0) {
                $result[] = array(
                    'penagihan_id' => $val['penagihan_id'],
                    'penagihan_no' => $val['penagihan_no'],
                    'penagihan_tgl' => $val['penagihan_tgl'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'penagihandet_total' => $val['penagihandet_total']-$val['t_pelunasandet_bayar'],
                );
            }
        }

        return $result;
    }

}