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
            FROM m_rekanan WHERE rekanan_aktif = 'Y' AND m_rekanan.rekanan_jenis = 'pelanggan'
            ORDER BY m_rekanan.rekanan_nama";

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
                    CONCAT('<b>[', m_rekanan.rekanan_kode, ']</b> ', m_rekanan.rekanan_nama) AS rekanan_nama,
                    m_rekanan.rekanan_alamat,
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
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_alamat";
        
        $qpenagihan = $this->conn2->query($sql);
        $rpenagihan = array();
        while ($val = $qpenagihan->fetch_array()) {
            $rpenagihan[] = array(
                'penagihan_id' => $val['penagihan_id'],
                'penagihan_no' => $val['penagihan_no'],
                'penagihan_tgl' => $val['penagihan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'rekanan_alamat' => $val['rekanan_alamat'],
                'penagihandet_ppn' => $val['penagihandet_ppn'],
                'penagihandet_subtotal' => $val['penagihandet_subtotal'],
                'penagihandet_potongan' => $val['penagihandet_potongan'],
                'penagihandet_total' => $val['penagihandet_total'],
                't_pelunasandet_bayar' => $val['t_pelunasandet_bayar']
            );
        }

        return $rpenagihan;
    }

    public function getPengiriman($tanggal, $rekananArr, $barangArr, $tagih)
    {
        $sql = "SELECT 
                    t_pengiriman.pengiriman_id,
                    COALESCE(t_pengiriman.pengiriman_no, '') AS pengiriman_no,
                    COALESCE(t_pengiriman.pengiriman_tgl, '') AS pengiriman_tgl,
                    t_pengiriman.m_rekanan_id,
                    CONCAT('<b>[', m_rekanan.rekanan_kode, ']</b> ', m_rekanan.rekanan_nama) AS rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    t_pengiriman_detail.m_barang_id,
                    m_barang.barang_nama,
                    m_satuan.satuan_nama,
                    t_pengiriman_detail.pengirimandet_qty,
                    COALESCE(t_penagihan.penagihan_no, '') AS penagihan_no,
                    COALESCE(t_penagihan.penagihan_tgl, '') AS penagihan_tgl
                FROM t_pengiriman
                INNER JOIN t_pengiriman_detail ON t_pengiriman_detail.t_pengiriman_id = t_pengiriman.pengiriman_id
                INNER JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_pengiriman_detail.m_satuan_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_pengiriman.m_rekanan_id
                LEFT JOIN t_penagihan ON t_penagihan.penagihan_id = t_pengiriman.t_penagihan_id
                WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y'
				AND t_pengiriman_detail.m_bahanbakubrg_id = 0";
        if ($rekananArr <> '') {
            $sql .= " AND m_rekanan.rekanan_id IN (".$rekananArr.") ";
        }
        if ($barangArr <> '') {
            $sql .= " AND m_barang.barang_id IN (".$barangArr.") ";
        }
        if (is_array($tanggal)) {
            $sql .= " AND t_pengiriman.pengiriman_tgl >= '" . date('Y-m-d', strtotime($tanggal[0])) . "' AND t_pengiriman.pengiriman_tgl <= '" . date('Y-m-d', strtotime($tanggal[1])) . "' ";
        } else {
            $sql .= " AND t_pengiriman.pengiriman_tgl = NOW()";
        }
        if ($tagih == 'Y') {
            $sql .= " AND t_penagihan.penagihan_id > 0 ";
        }
        if ($tagih == 'N') {
            $sql .= " AND (t_penagihan.penagihan_id < 1 OR t_penagihan.penagihan_id IS NULL) ";
        }
        
        $sql .=  "ORDER BY t_pengiriman.pengiriman_tgl ASC ";
        
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        if ($qbarang) {
            while ($val = $qbarang->fetch_array()) {
                $rbarang[] = array(
                    'pengiriman_id' => $val['pengiriman_id'],
                    'pengiriman_no' => $val['pengiriman_no'],
                    'pengiriman_tgl' => $val['pengiriman_tgl'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'rekanan_nama' => $val['rekanan_nama'],
                    'rekanan_alamat' => $val['rekanan_alamat'],
                    'm_barang_id' => $val['m_barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'satuan_nama' => $val['satuan_nama'],
                    'pengirimandet_qty' => $val['pengirimandet_qty'],
                    'penagihan_no' => $val['penagihan_no'],
                    'penagihan_tgl' => $val['penagihan_tgl']
                );
            }
        }
        return $rbarang;
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

    public function getSewa($tahun, $rekanan, $barang, $tagih)
    {
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanIndo2 = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $sqlsewa = "SELECT
                        t_hargakontrak_detail.hargakontrakdet_tgl,
                        t_hargakontrak_detail.m_rekanan_id,
                        t_hargakontrak_detail.m_barang_id,
                        m_barang.barang_nama,
                        t_hargakontrak_detail.m_satuan_id,
                        m_satuan.satuan_nama,
                        t_hargakontrak_detail.hargakontrakdet_harga,
                        t_hargakontrak_detail.hargakontrakdet_ppn,
                        t_hargakontrak_detail.hargakontrakdet_sewa,
                        pengiriman.pengiriman_id,
                        pengiriman.pengirimandet_id,
                        pengiriman.pengiriman_no,
                        pengiriman.pengiriman_tgl,
                        pengiriman.pengirimandet_qty AS jmlsewa,
                        COALESCE ( penagihansewa.t_penagihan_tgl, '' ) AS t_penagihan_tgl,
                        m_rekanan.rekanan_nama,
                        m_rekanan.rekanan_alamat
                    FROM
                        t_hargakontrak_detail
                        INNER JOIN t_hargakontrak ON t_hargakontrak.hargakontrak_id = t_hargakontrak_detail.t_hargakontrak_id
                        INNER JOIN m_barang ON m_barang.barang_id = t_hargakontrak_detail.m_barang_id
                        INNER JOIN m_satuan ON m_satuan.satuan_id = t_hargakontrak_detail.m_satuan_id
                        INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hargakontrak_detail.m_rekanan_id
                        INNER JOIN (
                        SELECT
                            t_pengiriman.pengiriman_id,
                            t_pengiriman_detail.pengirimandet_id,
                            t_pengiriman.pengiriman_no,
                            t_pengiriman.pengiriman_tgl,
                            t_pengiriman_detail.m_barang_id,
                            (
                            t_pengiriman_detail.pengirimandet_qty - COALESCE ( t_pengiriman_detail.t_returdet_qty, 0 )) AS pengirimandet_qty 
                        FROM
                            t_pengiriman
                            INNER JOIN t_pengiriman_detail ON t_pengiriman_detail.t_pengiriman_id = t_pengiriman.pengiriman_id 
                        WHERE
                            t_pengiriman.pengiriman_aktif = 'Y' 
                            AND t_pengiriman_detail.pengirimandet_aktif = 'Y' 
                            AND t_pengiriman_detail.m_barang_id = $barang
                            AND t_pengiriman.m_rekanan_id = $rekanan
                            AND YEAR(t_pengiriman.pengiriman_tgl) = $tahun
                        ) AS pengiriman ON pengiriman.m_barang_id = t_hargakontrak_detail.m_barang_id
                        LEFT JOIN (
                            SELECT
                                t_penagihansewa.m_barang_id,
                                CONCAT(
                                    '[',
                                    group_concat(
                                    JSON_OBJECT( 'nopenagihan', t_penagihan.penagihan_no, 'bulan', t_penagihansewa.bulan, 'tahun', t_penagihansewa.tahun, 'nobayar', t_penagihansewa.t_pelunasan_no )),
                                    ']' 
                                ) AS t_penagihan_tgl 
                            FROM
                                t_penagihansewa
                                INNER JOIN t_penagihan ON t_penagihan.penagihan_id = t_penagihansewa.t_penagihan_id 
                            WHERE t_penagihansewa.penagihansewa_aktif = 'Y'
                                AND t_penagihansewa.m_rekanan_id = $rekanan
                                AND t_penagihansewa.m_barang_id = $barang
                                AND t_penagihan.penagihan_aktif = 'Y' 
                                AND t_penagihansewa.tahun = '2020'
                            GROUP BY
                                t_penagihansewa.m_barang_id
                        ) AS penagihansewa ON penagihansewa.m_barang_id = t_hargakontrak_detail.m_barang_id 
                    WHERE
                        t_hargakontrak_detail.hargakontrakdet_aktif = 'Y' 
                        AND t_hargakontrak_detail.m_barang_id = $barang
                        AND t_hargakontrak_detail.m_rekanan_id = $rekanan
                        AND t_hargakontrak.hargakontrak_aktif = 'Y' 
                        AND t_hargakontrak_detail.hargakontrakdet_sewa = 'Y' 
                    ORDER BY
                        t_hargakontrak_detail.hargakontrakdet_tgl ASC";
        
        $qsewa = $this->conn2->query($sqlsewa);
        $sewa = array();
        while ($val = $qsewa->fetch_array()) {
            $kontrakmulai = $val['hargakontrakdet_tgl'];
            $barangkirim = $val['pengiriman_tgl'];
            $sudahtagih = $val['t_penagihan_tgl'] <> '' ? json_decode($val['t_penagihan_tgl']) : [];

            $tahunkirim = date('Y', strtotime($barangkirim));
            $bulankirim = intval(date('m', strtotime($barangkirim)));
            $x = 0;
            $y = 0;
            $z = $tahunkirim;
            $mulai = '';
            $tagih = '';
            $januari = '';
            $februari = '';
            $maret = '';
            $april = '';
            $mei = '';
            $juni = '';
            $juli = '';
            $agustus = '';
            $september = '';
            $oktober = '';
            $november = '';
            $desember = '';
            
            foreach ($sudahtagih as $keytagih => $valuetagih) {
                switch ($valuetagih->bulan) {
                    case 1:
                        $januari = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 2:
                        $februari = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 3:
                        $maret = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 4:
                        $april = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 5:
                        $mei = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 6:
                        $juni = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 7:
                        $juli = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 8:
                        $agustus = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 9:
                        $september = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 10:
                        $oktober = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 11:
                        $november = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                    case 12:
                        $desember = 'Penagihan : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nopenagihan . '</b><br> Pembayaran : &nbsp;&nbsp;&nbsp;<b>' . $valuetagih->nobayar . '</b>';
                        break;
                }
            }
            $sewa[] = array(
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'rekanan_alamat' => $val['rekanan_alamat'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'hargakontrakdet_harga' => $val['hargakontrakdet_harga'],
                'hargakontrakdet_ppn' => $val['hargakontrakdet_ppn'],
                'hargakontrakdet_sewa' => $val['hargakontrakdet_sewa'],
                'pengiriman_id' => $val['pengiriman_id'],
                'pengirimandet_id' => $val['pengirimandet_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                'jmlsewa' => $val['jmlsewa'],
                'januari' => $januari,
                'februari' => $februari,
                'maret' => $maret,
                'april' => $april,
                'mei' => $mei,
                'juni' => $juni,
                'juli' => $juli,
                'agustus' => $agustus,
                'september' => $september,
                'oktober' => $oktober,
                'november' => $november,
                'desember' => $desember,
            );
        }

        return $sewa;
    }

}