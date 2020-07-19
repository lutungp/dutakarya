<?php

class M_infopenerimaan
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

    public function getBarang()
    {
        $sql = "SELECT barang_id, barang_nama FROM m_barang WHERE barang_aktif = 'Y'";
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        while ($val = $qbarang->fetch_array()) {
            $rbarang[] = array(
                'barang_id' => $val['barang_id'],
                'barang_nama' => $val['barang_nama']
            );
        }

        return $rbarang;
    }

    public function getPenerimaan($tanggal, $rekananArr, $barangArr)
    {
        $sql = "SELECT 
                    penerimaan_id,
                    penerimaan_no,
                    penerimaan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    t_penerimaan_detail.m_barang_id,
                    m_barang.barang_nama,
                    m_satuan.satuan_nama,
                    t_penerimaan_detail.penerimaandet_qty
                FROM t_penerimaan
                INNER JOIN t_penerimaan_detail ON t_penerimaan_detail.t_penerimaan_id = t_penerimaan.penerimaan_id
                INNER JOIN m_barang ON m_barang.barang_id = t_penerimaan_detail.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_penerimaan_detail.m_satuan_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_penerimaan.m_rekanan_id
                WHERE t_penerimaan.penerimaan_aktif = 'Y' AND t_penerimaan_detail.penerimaandet_aktif = 'Y'";
        if ($rekananArr <> '') {
            $sql .= " AND m_rekanan.rekanan_id IN (".$rekananArr.") ";
        }
        if ($barangArr <> '') {
            $sql .= " AND m_barang.barang_id IN (".$barangArr.") ";
        }
        if (is_array($tanggal)) {
            $sql .= " AND t_penerimaan.penerimaan_tgl >= '" . date('Y-m-d', strtotime($tanggal[0])) . "' AND t_penerimaan.penerimaan_tgl <= '" . date('Y-m-d', strtotime($tanggal[1])) . "' ";
        } else {
            $sql .= " AND t_penerimaan.penerimaan_tgl = NOW()";
        }

        $sql .=  "ORDER BY t_penerimaan.penerimaan_tgl ASC ";

        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        if ($qbarang) {
            while ($val = $qbarang->fetch_array()) {
                $rbarang[] = array(
                    'penerimaan_id' => $val['penerimaan_id'],
                    'penerimaan_no' => $val['penerimaan_no'],
                    'penerimaan_tgl' => $val['penerimaan_tgl'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'rekanan_nama' => $val['rekanan_nama'],
                    'm_barang_id' => $val['m_barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'satuan_nama' => $val['satuan_nama'],
                    'penerimaandet_qty' => $val['penerimaandet_qty'],
                );
            }
        }
        return $rbarang;
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

}