<?php

class M_infotagihan
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    
    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getRekanan()
    {
        $sql = "SELECT
                m_rekanan.rekanan_id,
                m_rekanan.rekanan_kode,
                m_rekanan.rekanan_nama
            FROM m_rekanan WHERE rekanan_aktif = 'Y'";

        $qrekanan = $this->conn2->query($sql);
        $rekanan = [];
        while ($val = $qrekanan->fetch_array(MYSQLI_ASSOC)) {
            $rekanan[] = array(
                'rekanan_id' => $val['rekanan_id'],
                'rekanan_kode' => $val['rekanan_kode'],
                'rekanan_nama' => $val['rekanan_nama'],
            );
        }
        return $rekanan;
    }

    public function getPenagihan($tanggal, $rekananArr)
    {
        $sql = "SELECT
                    t_penagihan.penagihan_id,
                    t_penagihan.penagihan_no,
                    t_penagihan.penagihan_tgl,
                    t_penagihan.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    SUM(t_penagihan_detail.penagihandet_ppn) AS penagihandet_ppn,
                    SUM(t_penagihan_detail.penagihandet_subtotal) AS penagihandet_subtotal,
                    SUM(t_penagihan_detail.penagihandet_potongan) AS penagihandet_potongan,
                    SUM(t_penagihan_detail.penagihandet_total) AS penagihandet_total,
                    t_penagihan.t_pelunasandet_bayar
                FROM t_penagihan
                INNER JOIN t_penagihan_detail ON t_penagihan_detail.t_penagihan_id = t_penagihan.penagihan_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                WHERE t_penagihan.penagihan_aktif = 'Y' AND t_penagihan_detail.penagihandet_aktif = 'Y'";
        if (is_array($tanggal)) {
            $sql .= " AND t_penagihan.penagihan_tgl >= '" . date('Y-m-d', strtotime($tanggal[0])) . "' AND t_penagihan.penagihan_tgl <= '" . date('Y-m-d', strtotime($tanggal[1])) . "' ";
        } else {
            $sql .= " AND t_penagihan.penagihan_tgl = NOW()";
        }
        $sql .= " GROUP BY 
                    t_penagihan.penagihan_id,
                    t_penagihan.penagihan_no,
                    t_penagihan.penagihan_tgl,
                    t_penagihan.m_rekanan_id,
                    m_rekanan.rekanan_nama";

        $qpenagihan = $this->conn2->query($sql);
        $rpenagihan = array();
        while ($val = $qpenagihan->fetch_array()) {
            $rpenagihan[] = array(
                'penagihan_id' => $val['penagihan_id'],
                'penagihan_no' => $val['penagihan_no'],
                'penagihan_tgl' => $val['penagihan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'penagihandet_ppn' => $val['penagihandet_ppn'],
                'penagihandet_subtotal' => $val['penagihandet_subtotal'],
                'penagihandet_potongan' => $val['penagihandet_potongan'],
                'penagihandet_total' => $val['penagihandet_total'],
                't_pelunasandet_bayar' => $val['t_pelunasandet_bayar']
            );
        }

        return $rpenagihan;
    }
}