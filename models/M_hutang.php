<?php

class M_hutang
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getHutang()
    {
        $sql = "SELECT 
                    t_hutang_lunas.hutang_id,
                    t_hutang_lunas.hutang_tgl,
                    t_hutang_lunas.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_user.user_nama,
                    t_hutang_lunas.hutang_created_date
                FROM t_hutang_lunas
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hutang_lunas.m_rekanan_id
                INNER JOIN m_user ON m_user.user_id = t_hutang_lunas.hutang_created_by
                WHERE t_hutang_lunas.hutang_aktif = 'Y'";

        $qhutang = $this->conn2->query($sql);
        $result = array();
        while ($val = $qhutang->fetch_array()) {
            $result[] = array(
                'hutang_id' => $val['hutang_id'],
                'hutang_no' => $val['hutang_no'],
                'hutang_tgl' => $val['hutang_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'hutang_created_date' => $val['hutang_created_date'],
                'user_nama' => $val['user_nama'],
            );
        }

        return $result;
    }

    public function getRekanan($search, $jenis = 'pabrik')
    {
        $sql = "SELECT
                m_rekanan.rekanan_id AS id,
                m_rekanan.rekanan_kode,
                CONCAT('[', m_rekanan.rekanan_kode, '] ', m_rekanan.rekanan_nama) AS text
            FROM m_rekanan WHERE rekanan_aktif = 'Y'";
        if ($jenis <> 'all') {
            $sql .= " AND rekanan_jenis = '$jenis '";
        }
        if ($search <> '') {
            $sql .= " AND m_rekanan.rekanan_nama LIKE '%".$search."%' ";
        }

        $qrekanan = $this->conn2->query($sql);
        $rekanan = [];
        while ($result = $qrekanan->fetch_array(MYSQLI_ASSOC)) {
            array_push($rekanan, $result);
        }
        return $rekanan;
    }
}