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
                    t_hutang_lunas.hutang_no,
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

    public function getMaklondet($rekanan_id, $tanggal)
    {
        $sql = "SELECT
                    t_maklondet.maklondet_id,
                    t_maklondet.t_maklon_id,
                    t_maklon.maklon_no,
                    t_maklon.maklon_tgl,
                    t_maklondet.m_barang_id,
                    m_barang.barang_nama,
                    t_maklondet.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_maklondet.maklondet_qty,
                    t_maklondet.maklondet_harga,
                    t_maklondet.maklondet_subtotal,
                    t_maklondet.maklondet_ppn,
                    t_maklondet.maklondet_total-t_maklondet.t_hutanglunasdet_bayar AS maklondet_total
                FROM t_maklondet
                INNER JOIN t_maklon ON t_maklon.maklon_id = t_maklondet.t_maklon_id
                INNER JOIN m_barang ON m_barang.barang_id = t_maklondet.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_maklondet.m_satuan_id
                WHERE t_maklondet.maklondet_aktif = 'Y'
                AND t_maklon.m_rekanan_id = $rekanan_id AND t_maklon.maklon_tgl <= '$tanggal' 
                AND t_maklondet.maklondet_total > COALESCE(t_maklondet.t_hutanglunasdet_bayar, 0)
                AND t_maklondet.m_bahanbakubrg_id = 0";
        
        $qmaklon = $this->conn2->query($sql);
        $result = array();
        while ($val = $qmaklon->fetch_array()) {
            $result[] = array(
                'hutangdet_id' => 0,
                't_hutang_id'  => 0,
                't_maklon_id'  => $val['t_maklon_id'],
                'maklon_no'    => $val['maklon_no'],
                'maklon_noa' => $val['maklon_no'],
                'maklon_tgl'   => $val['maklon_tgl'],
                't_maklondet_id' => $val['maklondet_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'barang_namaa' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                't_maklondet_qty' => $val['maklondet_qty'],
                't_maklondet_subtotal' => $val['maklondet_subtotal'],
                't_maklondet_ppn' => $val['maklondet_ppn'],
                'hutangdet_tagihan' => $val['maklondet_total'],
                'hutangdet_bayarold' => 0,
                'hutangdet_bayar' => 0,
            );
        }

        return $result;
    }

    public function getDataHutang($hutang_id)
    {
        $sql = "SELECT
                    t_hutang_lunas.hutang_id,
                    t_hutang_lunas.hutang_no,
                    t_hutang_lunas.hutang_tgl,
                    t_hutang_lunas.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    t_hutang_lunas.hutang_created_date,
                    m_user.user_nama 
                FROM
                    t_hutang_lunas
                    INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hutang_lunas.m_rekanan_id
                    INNER JOIN m_user ON m_user.user_id = t_hutang_lunas.hutang_created_by 
                WHERE t_hutang_lunas.hutang_id = $hutang_id ";
        
        $qhutang = $this->conn2->query($sql);
        $rhutang = $qhutang->fetch_object();

        return $rhutang;
    }

    public function getDataHutang2($hutang_id)
    {
        $sql = "SELECT
                    t_hutang_lunas.hutang_id,
                    t_hutang_lunas.hutang_no,
                    t_hutang_lunas.hutang_tgl,
                    t_hutang_lunas.m_rekanan_id,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    t_hutang_lunas.hutang_created_date,
                    m_user.user_nama 
                FROM
                    t_hutang_lunas
                    INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hutang_lunas.m_rekanan_id
                    INNER JOIN m_user ON m_user.user_id = t_hutang_lunas.hutang_created_by 
                WHERE t_hutang_lunas.hutang_id = $hutang_id ";
        
        $qhutang = $this->conn2->query($sql);
        $rhutang = $qhutang->fetch_object();

        return $rhutang;
    }

    public function getDataHutangDetail($hutang_id)
    {
        $sql = "SELECT 
                    t_hutang_lunasdet.hutangdet_id,
                    t_hutang_lunasdet.t_hutang_id,
                    t_hutang_lunasdet.t_maklon_id,
                    t_maklon.maklon_no,
                    t_maklon.maklon_tgl,
                    t_hutang_lunasdet.t_maklondet_id,
                    t_hutang_lunasdet.m_barang_id,
                    m_barang.barang_nama,
                    t_hutang_lunasdet.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_hutang_lunasdet.t_maklondet_qty,
                    t_hutang_lunasdet.t_maklondet_subtotal,
                    t_hutang_lunasdet.t_maklondet_ppn,
                    t_hutang_lunasdet.hutangdet_tagihan,
                    t_hutang_lunasdet.hutangdet_bayar,
                    t_maklon.m_rekanan_id
                FROM t_hutang_lunasdet
                INNER JOIN t_maklon ON t_maklon.maklon_id = t_hutang_lunasdet.t_maklon_id
                INNER JOIN t_maklondet ON t_maklondet.maklondet_id = t_hutang_lunasdet.t_maklondet_id
                INNER JOIN m_barang ON m_barang.barang_id = t_maklondet.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_maklondet.m_satuan_id
                WHERE t_hutang_lunasdet.hutangdet_aktif = 'Y'
                AND t_hutang_lunasdet.t_hutang_id = $hutang_id ";
        
        $qhutang = $this->conn2->query($sql);
        $result = array();
        while ($val = $qhutang->fetch_array()) {
            $result[] = array(
                'hutangdet_id' => $val['hutangdet_id'],
                't_hutang_id'  => $val['t_hutang_id'],
                't_maklon_id'  => $val['t_maklon_id'],
                'maklon_no'    => $val['maklon_no'],
                'maklon_noa' => $val['maklon_no'],
                'maklon_tgl'   => $val['maklon_tgl'],
                't_maklondet_id' => $val['t_maklondet_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'barang_namaa' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                't_maklondet_qty' => $val['t_maklondet_qty'],
                't_maklondet_subtotal' => $val['t_maklondet_subtotal'],
                't_maklondet_ppn' => $val['t_maklondet_ppn'],
                'hutangdet_tagihan' => $val['t_maklondet_subtotal']+$val['t_maklondet_ppn'],
                'hutangdet_bayarold' => $val['hutangdet_bayar'],
                'hutangdet_bayar' => $val['hutangdet_bayar']
            );
        }

        return $result;
    }
}