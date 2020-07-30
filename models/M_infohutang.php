<?php

class M_infohutang
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
            FROM m_rekanan WHERE rekanan_aktif = 'Y' AND m_rekanan.rekanan_jenis = 'pabrik'";

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

    public function getHutangLunas($tanggal, $rekananArr)
    {
        $sql = "SELECT
                    t_hutang_lunas.hutang_id,
                    t_hutang_lunas.hutang_no,
                    t_hutang_lunas.hutang_tgl,
                    t_hutang_lunas.m_rekanan_id,
                    CONCAT('<b>[', m_rekanan.rekanan_kode, ']</b> ', m_rekanan.rekanan_nama) AS rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    SUM(t_hutang_lunasdet.hutangdet_tagihan) AS hutangdet_tagihan,
                    SUM(t_hutang_lunasdet.hutangdet_bayar) AS hutangdet_bayar
                FROM t_hutang_lunas
                INNER JOIN t_hutang_lunasdet ON t_hutang_lunasdet.t_hutang_id = t_hutang_lunas.hutang_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hutang_lunas.m_rekanan_id
                WHERE t_hutang_lunas.hutang_aktif = 'Y' AND t_hutang_lunasdet.hutangdet_aktif = 'Y'";
        if (is_array($tanggal)) {
            $sql .= " AND t_hutang_lunas.hutang_tgl >= '" . date('Y-m-d', strtotime($tanggal[0])) . "' AND t_hutang_lunas.hutang_tgl <= '" . date('Y-m-d', strtotime($tanggal[1])) . "' ";
        } else {
            $sql .= " AND t_hutang_lunas.hutang_tgl = NOW()";
        }
        $sql .= " GROUP BY 
                    t_hutang_lunas.hutang_id,
                    t_hutang_lunas.hutang_no,
                    t_hutang_lunas.hutang_tgl,
                    t_hutang_lunas.m_rekanan_id,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat";
        
        $qhutang = $this->conn2->query($sql);
        $rhutang = array();
        while ($val = $qhutang->fetch_array()) {
            $rhutang[] = array(
                'hutang_id' => $val['hutang_id'],
                'hutang_no' => $val['hutang_no'],
                'hutang_tgl' => $val['hutang_tgl'],
                'rekanan_nama' => $val['rekanan_nama'],
                'rekanan_alamat' => $val['rekanan_alamat'],
                'hutangdet_tagihan' => $val['hutangdet_tagihan'],
                'hutangdet_bayar' => $val['hutangdet_bayar'],
            );
        }

        return $rhutang;
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

    public function getmaklon($tanggal, $rekananArr, $barangArr, $tagih)
    {
        $sql = "SELECT
                    COALESCE(t_maklon.maklon_no, '') AS maklon_no,
                    COALESCE(t_maklon.maklon_tgl, '') AS maklon_tgl,
                    CONCAT('<b>[', m_rekanan.rekanan_kode, ']</b> ', m_rekanan.rekanan_nama) AS rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    m_barang.barang_nama,
                    m_satuan.satuan_nama,
                    t_maklondet.maklondet_qty,
                    COALESCE(t_hutang_lunas.hutang_no, '') AS hutang_no,
                    COALESCE(t_hutang_lunas.hutang_tgl, '') AS hutang_tgl,
                    t_maklondet.maklondet_total,
                    t_maklondet.t_hutanglunasdet_bayar
                FROM t_maklon
                INNER JOIN t_maklondet ON t_maklondet.t_maklon_id = t_maklon.maklon_id
                INNER JOIN m_barang ON m_barang.barang_id = t_maklondet.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_maklondet.m_satuan_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_maklon.m_rekanan_id
                LEFT JOIN t_hutang_lunas ON t_hutang_lunas.hutang_id = t_maklondet.t_hutanglunas_id
                WHERE t_maklon.maklon_aktif = 'Y' AND t_maklondet.maklondet_aktif = 'Y' AND (t_maklondet.m_bahanbakubrg_id = 0 OR t_maklondet.m_bahanbakubrg_id IS NULL)";
        if ($rekananArr <> '') {
            $sql .= " AND m_rekanan.rekanan_id IN (".$rekananArr.") ";
        }
        if ($barangArr <> '') {
            $sql .= " AND m_barang.barang_id IN (".$barangArr.") ";
        }
        if (is_array($tanggal)) {
            $sql .= " AND t_maklon.maklon_tgl >= '" . date('Y-m-d', strtotime($tanggal[0])) . "' AND t_maklon.maklon_tgl <= '" . date('Y-m-d', strtotime($tanggal[1])) . "' ";
        } else {
            $sql .= " AND t_maklon.maklon_tgl = NOW()";
        }
        if ($tagih == 'Y') {
            $sql .= " AND (t_hutang_lunas.hutang_id > 0 AND t_hutang_lunas.hutang_aktif = 'Y')";
        }
        if ($tagih == 'N') {
            $sql .= " AND (t_hutang_lunas.hutang_id < 1 OR t_hutang_lunas.hutang_id IS NULL) ";
        }
        
        $sql .=  "ORDER BY t_maklon.maklon_tgl ASC ";
        
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        if ($qbarang) {
            while ($val = $qbarang->fetch_array()) {
                $rbarang[] = array(
                    'maklon_no' => $val['maklon_no'],
                    'maklon_tgl' => $val['maklon_tgl'],
                    'rekanan_nama' => $val['rekanan_nama'],
                    'rekanan_alamat' => $val['rekanan_alamat'],
                    'barang_nama' => $val['barang_nama'],
                    'satuan_nama' => $val['satuan_nama'],
                    'maklondet_qty' => $val['maklondet_qty'],
                    'hutang_no' => $val['hutang_no'],
                    'hutang_tgl' => $val['hutang_tgl'],
                    'maklondet_total' => $val['maklondet_total'],
                    't_hutanglunasdet_bayar' => $val['t_hutanglunasdet_bayar']
                );
            }
        }
        return $rbarang;
    }

}