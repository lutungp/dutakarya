<?php

class M_penagihan
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPenagihan()
    {
        $sql = " SELECT 
                    penagihan_id,
                    penagihan_no,
                    penagihan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat
                FROM t_penagihan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                WHERE penagihan_aktif = 'Y' ORDER BY t_penagihan.penagihan_created_date DESC ";
        $qpenagihan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenagihan->fetch_array()) {
            $result[] = array(
                'penagihan_id' => $val['penagihan_id'],
                'penagihan_no' => $val['penagihan_no'],
                'penagihan_tgl' => $val['penagihan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama']
            );
        }

        return $result;
    }

    public function getPengiriman($m_rekanan_id, $penagihan_tgl)
    {
        $sql = "SELECT
                    t_pengiriman.pengiriman_id,
                    t_pengiriman.pengiriman_no,
                    t_pengiriman.pengiriman_tgl,
                    t_pengiriman_detail.pengirimandet_id,
                    t_pengiriman.m_rekanan_id,
                    t_pengiriman_detail.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    satuanutama.satuan_nama AS m_barangsatuan_nama,
                    t_pengiriman_detail.m_satuan_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    t_pengiriman_detail.pengirimandet_qty,
                    t_pengiriman_detail.pengirimandet_harga,
                    t_pengiriman_detail.pengirimandet_subtotal,
                    t_pengiriman_detail.pengirimandet_ppn,
                    t_pengiriman_detail.pengirimandet_potongan,
                    t_pengiriman_detail.pengirimandet_total,
                    t_pengiriman_detail.t_returdet_qty
                FROM t_pengiriman_detail
                JOIN t_pengiriman ON t_pengiriman.pengiriman_id = t_pengiriman_detail.t_pengiriman_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id
                JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                JOIN m_satuan satuanutama ON satuanutama.satuan_id = m_barang.m_satuan_id
                WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y' AND t_pengiriman.pengiriman_tgl <= '$penagihan_tgl'
				AND t_pengiriman_detail.m_bahanbakubrg_id = 0 AND t_pengiriman_detail.pengirimandet_total > 0
                AND t_pengiriman.m_rekanan_id = $m_rekanan_id AND (t_penagihan_no IS NULL OR t_penagihan_no = '') ORDER BY t_pengiriman.pengiriman_created_date ASC";
        $qkirim = $this->conn2->query($sql);
        $rkirim = array();
        while ($val = $qkirim->fetch_array()) {
            $rkirim[] = array(
                'penagihandet_id' => 0,
                't_penagihan_id' => 0,
                'pengiriman_id' => $val['pengiriman_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                'pengirimandet_id' => $val['pengirimandet_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_barangsatuan_nama' => $val['m_barangsatuan_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'pengirimandet_qty' => $val['pengirimandet_qty'],
                'pengirimandet_harga' => $val['pengirimandet_harga'],
                'pengirimandet_qtyreal' => $val['pengirimandet_qty'] * $val['satkonv_nilai'],
                'pengirimandet_subtotal' => $val['pengirimandet_subtotal'],
                'pengirimandet_ppn' => $val['pengirimandet_ppn'],
                'pengirimandet_potongan' => $val['pengirimandet_potongan'],
                'pengirimandet_total' => $val['pengirimandet_total'],
                't_returdet_qty' => $val['t_returdet_qty']
            );
        }

        return $rkirim;
    }

    public function getPenagihanData($penagihan_id)
    {
        $sql = " SELECT 
                    penagihan_id,
                    penagihan_no,
                    penagihan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama
                FROM t_penagihan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                WHERE penagihan_aktif = 'Y' 
                AND penagihan_id = $penagihan_id ";
        $qpenagihan = $this->conn2->query($sql);
        $row = $qpenagihan->fetch_object();

        $rpenagihan = new stdClass();
        if ($row) {
            $rpenagihan->penagihan_id = $row->penagihan_id;
            $rpenagihan->penagihan_no = $row->penagihan_no;
            $rpenagihan->penagihan_tgl = $row->penagihan_tgl;
            $rpenagihan->m_rekanan_id = $row->m_rekanan_id;
            $rpenagihan->rekanan_nama = $row->rekanan_nama;
        } else {
            $rpenagihan->penagihan_id = 0;
            $rpenagihan->penagihan_no = '';
            $rpenagihan->penagihan_tgl = '';
            $rpenagihan->m_rekanan_id = '';
            $rpenagihan->rekanan_nama = '';
        }
        

        return $rpenagihan;
    }

    public function getPenagihanData2($penagihan_id)
    {
        $sql = " SELECT 
                    penagihan_id,
                    penagihan_no,
                    penagihan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat
                FROM t_penagihan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                WHERE penagihan_aktif = 'Y' 
                AND penagihan_id = $penagihan_id ";
        $qpenagihan = $this->conn2->query($sql);
        $row = $qpenagihan->fetch_object();

        $rpenagihan = new stdClass();
        $rpenagihan->penagihan_id = $row->penagihan_id;
        $rpenagihan->penagihan_no = $row->penagihan_no;
        $rpenagihan->penagihan_tgl = $row->penagihan_tgl;
        $rpenagihan->m_rekanan_id = $row->m_rekanan_id;
        $rpenagihan->rekanan_kode = $row->rekanan_kode;
        $rpenagihan->rekanan_nama = $row->rekanan_nama;
        $rpenagihan->rekanan_alamat = str_replace("<br />", "\\n", $row->rekanan_alamat);

        return $rpenagihan;
    }

    public function getPenagihanDataDetail($penagihan_id)
    {
        $sql = "SELECT
                    t_penagihan_detail.penagihandet_id,
                    t_penagihan_detail.t_penagihan_id,
                    t_penagihan_detail.t_pengiriman_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusak.m_rekanan_id ELSE t_pengiriman.m_rekanan_id 
                    END AS m_rekanan_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusak.barangrusak_no ELSE t_pengiriman.pengiriman_no 
                    END AS pengiriman_no,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusak.barangrusak_tgl ELSE t_pengiriman.pengiriman_tgl 
                    END AS pengiriman_tgl,
                    t_penagihan_detail.t_pengirimandet_id,
                    m_barang.barang_id AS m_barang_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'sewa' THEN
                        CONCAT( 'Sewa ', m_barang.barang_nama )
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_penagihan_detail.penagihandet_ket
                        ELSE m_barang.barang_nama 
                    END AS barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    satuanutama.satuan_nama AS m_barangsatuan_nama,
                    t_pengiriman_detail.m_satuan_id,
                    COALESCE ( m_satuan_konversi.satkonv_nilai, 1 ) AS satkonv_nilai,
                    t_pengiriman_detail.pengirimandet_harga,
                    t_penagihan_detail.penagihandet_harga,
                    t_pengiriman_detail.pengirimandet_qty,
                    t_penagihan_detail.penagihandet_subtotal,
                    t_penagihan_detail.penagihandet_ppn,
                    t_penagihan_detail.penagihandet_potongan,
                    t_penagihan_detail.penagihandet_total,
                    t_pengiriman_detail.t_returdet_qty,
                    t_penagihan_detail.penagihandet_ket,
                    t_penagihan_detail.penagihandet_jenis 
                FROM
                    t_penagihan_detail
                    LEFT JOIN t_pengiriman ON t_pengiriman.pengiriman_id = t_penagihan_detail.t_pengiriman_id
                    LEFT JOIN t_pengiriman_detail ON t_pengiriman_detail.pengirimandet_id = t_penagihan_detail.t_pengirimandet_id
                    LEFT JOIN t_barangrusak ON t_barangrusak.barangrusak_id = t_penagihan_detail.t_pengiriman_id
                    LEFT JOIN t_barangrusakdet ON t_barangrusakdet.barangrusakdet_id = t_penagihan_detail.t_pengirimandet_id
                    INNER JOIN m_barang ON (m_barang.barang_id = t_pengiriman_detail.m_barang_id OR m_barang.barang_id = t_barangrusakdet.m_barang_id)
                    LEFT JOIN m_satuan_konversi ON ((
                            m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id 
                            AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id 
                            ) 
                    OR ( m_satuan_konversi.m_satuan_id = t_barangrusakdet.m_satuan_id AND m_satuan_konversi.m_barang_id = t_barangrusakdet.m_barang_id ))
                    LEFT JOIN m_satuan satuanutama ON satuanutama.satuan_id = m_barang.m_satuan_id 
                WHERE
                    t_penagihan_detail.penagihandet_aktif = 'Y' ";

        if ($penagihan_id > 0) {
            $sql .= " AND t_penagihan_detail.t_penagihan_id = $penagihan_id ";
        }
        
        $qpenagihan = $this->conn2->query($sql);
        $rpenagihan = array();
        while ($val = $qpenagihan->fetch_array()) {
            $rpenagihan[] = array(
                'penagihandet_id' => $val['penagihandet_id'],
                't_penagihan_id' => $val['t_penagihan_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                't_pengiriman_id' => $val['t_pengiriman_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                't_pengirimandet_id' => $val['t_pengirimandet_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_barangsatuan_nama' => $val['m_barangsatuan_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'pengirimandet_harga' => $val['pengirimandet_harga'],
                'penagihandet_harga' => $val['penagihandet_harga'],
                'penagihandet_qty' => $val['pengirimandet_qty'],
                'penagihandet_qtyreal' => $val['pengirimandet_qty'] * $val['satkonv_nilai'],
                'penagihandet_subtotal' => $val['penagihandet_subtotal'],
                'penagihandet_ppn' => $val['penagihandet_ppn'],
                'penagihandet_potongan' => $val['penagihandet_potongan'],
                'penagihandet_total' => $val['penagihandet_total'],
                't_returdet_qty' => $val['t_returdet_qty'],
                'penagihandet_jenis' => $val['penagihandet_jenis'],
                'penagihandet_ket' => $val['penagihandet_ket']
            );
        }

        return $rpenagihan;
    }

    public function getSewa($rekanan_id, $bulan, $tahun, $penagihan_tgl)
    {
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanIndo2 = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan = intval(date('m', strtotime($penagihan_tgl)));
        $tahun = intval(date('Y', strtotime($penagihan_tgl)));
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
                        COALESCE(penagihansewa.t_penagihan_tgl, '') AS t_penagihan_tgl
                    FROM
                        t_hargakontrak_detail
                    INNER JOIN t_hargakontrak ON t_hargakontrak.hargakontrak_id = t_hargakontrak_detail.t_hargakontrak_id
                    INNER JOIN m_barang ON m_barang.barang_id = t_hargakontrak_detail.m_barang_id
                    INNER JOIN m_satuan ON m_satuan.satuan_id = t_hargakontrak_detail.m_satuan_id
                    INNER JOIN (
                            SELECT 
                                    t_pengiriman.pengiriman_id,
                                    t_pengiriman_detail.pengirimandet_id,
                                    t_pengiriman.pengiriman_no,
                                    t_pengiriman.pengiriman_tgl,
                                    t_pengiriman_detail.m_barang_id,
                                    (t_pengiriman_detail.pengirimandet_qty - COALESCE(t_pengiriman_detail.t_returdet_qty, 0)) AS pengirimandet_qty
                            FROM t_pengiriman
                            INNER JOIN t_pengiriman_detail ON t_pengiriman_detail.t_pengiriman_id = t_pengiriman.pengiriman_id
                            WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y'
                            AND t_pengiriman.pengiriman_tgl <= '$penagihan_tgl'
                    ) AS pengiriman ON pengiriman.m_barang_id = t_hargakontrak_detail.m_barang_id
                    LEFT JOIN (
                        SELECT
                            t_penagihansewa.m_barang_id,
                            CONCAT( '[', group_concat( JSON_OBJECT( 'bulan', t_penagihansewa.bulan, 'tahun', t_penagihansewa.tahun)), ']' ) AS t_penagihan_tgl 
                        FROM t_penagihansewa
                        INNER JOIN t_penagihan ON t_penagihan.penagihan_id = t_penagihansewa.t_penagihan_id
                        WHERE t_penagihansewa.m_rekanan_id = $rekanan_id
                        AND t_penagihansewa.penagihansewa_aktif = 'Y'
                        AND t_penagihan.penagihan_aktif = 'Y'
                        GROUP BY t_penagihansewa.m_barang_id
                    ) AS penagihansewa ON penagihansewa.m_barang_id = t_hargakontrak_detail.m_barang_id
                    WHERE
                        t_hargakontrak_detail.hargakontrakdet_aktif = 'Y'
                    AND t_hargakontrak_detail.hargakontrakdet_tgl <= '$penagihan_tgl'
                    AND t_hargakontrak_detail.m_rekanan_id = $rekanan_id
                    AND t_hargakontrak.hargakontrak_aktif = 'Y'
                    AND t_hargakontrak_detail.hargakontrakdet_sewa = 'Y'
                    ORDER BY
                        t_hargakontrak_detail.hargakontrakdet_tgl ASC ";
        
        $qsewa = $this->conn2->query($sqlsewa);
        $sewa = array();
        while ($val = $qsewa->fetch_array()) {
            $kontrakmulai = $val['hargakontrakdet_tgl'];
            $barangkirim = $val['pengiriman_tgl'];
            $sudahtagih = $val['t_penagihan_tgl'] <> '' ? json_decode($val['t_penagihan_tgl']) : [];

            $year1 = date('Y', strtotime($barangkirim)); 
            $year2 = date('Y', strtotime($penagihan_tgl)); 
            $month1 = intval(date('m', strtotime($barangkirim))); 
            $month2 = intval(date('m', strtotime($penagihan_tgl))); 
            $tahunbln = ($year2 - $year1 + 1) * 12;
            $x = 0;
            $y = 0;
            $z = $year1;
            $mulai = 0;
            $tagih = 0;

            for ($i=1; $i <= $tahunbln; $i++) {
                if ($x == 12) {
                    $z++;
                    $x = 0;
                    $y = $y+12;
                }
                $x++;
                $bulanditagih = $bulanIndo2[$i-$y];
                if (($i-$y) == $month1 && $year1 == $z) {
                    $mulai = 1;
                }
                $b = $i-$y;
                
                $selisihkirim = (($year2 - $z) * 12) + ($month2 - ($i-$y));
                if ($mulai == 1 && $selisihkirim >= 0) {
                    $hh = array_filter($sudahtagih, function ($element) use ($b, $z){
                        return ($element->bulan == $b && $element->tahun == $z);
                    });
                    
                    if(count($hh) == 0) {
                        $sewa[] = array(
                            'm_rekanan_id' => $val['m_rekanan_id'],
                            'm_barang_id' => $val['m_barang_id'],
                            'barang_nama' => 'Sewa ' . $val['barang_nama'] . ' ' . $val['jmlsewa'] . ' ' . $val['satuan_nama'] . '</br>' . $bulanditagih . ' ' . $z,
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
                            'bulan' => $i,
                            'tahun' => $z
                        ); 
                    }
                }
            }
        }

        return $sewa;
    }

    public function getPenagihanDataDetail2($penagihan_id)
    {
        $sql = "SELECT
                    t_penagihan_detail.penagihandet_id,
                    t_penagihan_detail.t_penagihan_id,
                    t_penagihan_detail.t_pengiriman_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusak.barangrusak_no ELSE t_pengiriman.pengiriman_no 
                    END AS pengiriman_no,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusak.barangrusak_tgl ELSE t_pengiriman.pengiriman_tgl 
                    END AS pengiriman_tgl,
                    t_penagihan_detail.t_pengirimandet_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'sewa' THEN
                        t_pengiriman_detail.m_barang_id ELSE t_barangrusakdet.m_barang_id 
                    END AS m_barang_id,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'sewa' THEN
                        CONCAT( 'Sewa ', m_barang.barang_nama ) 
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_penagihan_detail.penagihandet_ket ELSE m_barang.barang_nama 
                    END AS barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    satuanutama.satuan_nama AS m_barangsatuan_nama,
                    t_pengiriman_detail.m_satuan_id,
                    COALESCE ( m_satuan_konversi.satkonv_nilai, 1 ) AS satkonv_nilai,-- t_pengiriman_detail.pengirimandet_harga,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusakdet.barangrusakdet_harga ELSE t_pengiriman_detail.pengirimandet_harga 
                    END AS pengirimandet_harga,
                CASE
                        
                        WHEN t_penagihan_detail.penagihandet_jenis = 'ganti rugi' THEN
                        t_barangrusakdet.barangrusakdet_qty ELSE t_pengiriman_detail.pengirimandet_qty 
                    END AS pengirimandet_qty,
                    t_penagihan_detail.penagihandet_subtotal,
                    t_penagihan_detail.penagihandet_ppn,
                    t_penagihan_detail.penagihandet_potongan,
                    t_penagihan_detail.penagihandet_total,
                    COALESCE ( t_pengiriman_detail.t_returdet_qty, 0 ) AS t_returdet_qty,
                    t_penagihan_detail.penagihandet_jenis,
                    penagihan.bulan,
                    penagihan.tahun 
                FROM
                    t_penagihan_detail
                    LEFT JOIN t_pengiriman ON t_pengiriman.pengiriman_id = t_penagihan_detail.t_pengiriman_id
                    LEFT JOIN t_pengiriman_detail ON t_pengiriman_detail.pengirimandet_id = t_penagihan_detail.t_pengirimandet_id
                    LEFT JOIN t_barangrusak ON t_barangrusak.barangrusak_id = t_penagihan_detail.t_pengiriman_id
                    LEFT JOIN t_barangrusakdet ON t_barangrusakdet.barangrusakdet_id = t_penagihan_detail.t_pengirimandet_id
                    LEFT JOIN m_barang ON ( m_barang.barang_id = t_pengiriman_detail.m_barang_id OR m_barang.barang_id = t_barangrusakdet.m_barang_id )
                    LEFT JOIN m_satuan_konversi ON ( m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id )
                    LEFT JOIN m_satuan satuanutama ON satuanutama.satuan_id = m_barang.m_satuan_id
                    LEFT JOIN ( SELECT t_penagihansewa.t_penagihan_id, t_penagihansewa.t_penagihandet_id, bulan, tahun FROM t_penagihansewa WHERE t_penagihansewa.penagihansewa_aktif = 'Y' ) AS penagihan ON penagihan.t_penagihandet_id = t_penagihan_detail.penagihandet_id 
                WHERE
                    t_penagihan_detail.penagihandet_aktif = 'Y' ";

        if ($penagihan_id > 0) {
            $sql .= " AND t_penagihan_detail.t_penagihan_id = $penagihan_id ";
        }
        
        $qpenagihan = $this->conn2->query($sql);
        $rpenagihan = array();
        while ($val = $qpenagihan->fetch_array()) {
            $rpenagihan[] = array(
                'penagihandet_id' => $val['penagihandet_id'],
                't_penagihan_id' => $val['t_penagihan_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                't_pengiriman_id' => $val['t_pengiriman_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                't_pengirimandet_id' => $val['t_pengirimandet_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_barangsatuan_nama' => $val['m_barangsatuan_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'pengirimandet_harga' => $val['pengirimandet_harga'],
                'penagihandet_qty' => $val['pengirimandet_qty'],
                'penagihandet_qtyreal' => $val['pengirimandet_qty'] * $val['satkonv_nilai'],
                'penagihandet_subtotal' => $val['penagihandet_subtotal'],
                'penagihandet_ppn' => $val['penagihandet_ppn'],
                'penagihandet_potongan' => $val['penagihandet_potongan'],
                'penagihandet_total' => $val['penagihandet_total'],
                't_returdet_qty' => $val['t_returdet_qty'],
                'penagihandet_jenis' => $val['penagihandet_jenis'],
                'bulan' => $val['bulan'],
                'tahun' => $val['tahun'],
            );
        }

        return $rpenagihan;
    }

    public function getGantiRugi($m_rekanan_id, $penagihan_tgl)
    {
        $sql = "SELECT 
                    t_barangrusak.barangrusak_id,
                    t_barangrusak.barangrusak_no,
                    t_barangrusak.barangrusak_tgl,
                    t_barangrusakdet.barangrusakdet_id,
                    t_barangrusakdet.m_barang_id,
                    m_barang.barang_nama,
                    t_barangrusakdet.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_barangrusakdet.barangrusakdet_qty,
                    t_barangrusakdet.barangrusakdet_harga,
                    t_barangrusakdet.barangrusakdet_subtotal,
                    t_barangrusakdet.barangrusakdet_alasan
                FROM t_barangrusakdet
                INNER JOIN t_barangrusak ON t_barangrusak.barangrusak_id = t_barangrusakdet.t_barangrusak_id
                INNER JOIN m_barang ON m_barang.barang_id = t_barangrusakdet.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_barangrusakdet.m_satuan_id
                WHERE t_barangrusakdet.barangrusakdet_aktif = 'Y'
                AND t_barangrusak.barangrusak_aktif = 'Y'
                AND (t_barangrusak.t_penagihan_id = 0 OR t_barangrusak.t_penagihan_id = null)
                AND t_barangrusak.m_rekanan_id = $m_rekanan_id
                AND t_barangrusak.barangrusak_tgl >= '$penagihan_tgl' ";

         $qpenagihan = $this->conn2->query($sql);
         $rpenagihan = array();
         while ($val = $qpenagihan->fetch_array()) {
             $rpenagihan[] = array(
                'penagihandet_id' => 0,
                't_penagihan_id' => 0,
                'm_rekanan_id' => $m_rekanan_id,
                't_pengiriman_id' => $val['barangrusak_id'],
                'pengiriman_no' => $val['barangrusak_no'],
                'pengiriman_tgl' => $val['barangrusak_tgl'],
                't_pengirimandet_id' => $val['barangrusakdet_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => 'Ganti Rugi ' . $val['barang_nama'] . ' ' . $val['barangrusakdet_qty'] . ' ' . $val['satuan_nama'] . '<br> Ket : ' . $val['barangrusakdet_alasan'],
                'm_barangsatuan_id' => $val['m_satuan_id'],
                'm_barangsatuan_nama' => $val['satuan_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satkonv_nilai' => 1,
                'penagihandet_harga' => $val['barangrusakdet_harga'],
                'penagihandet_qty' => $val['barangrusakdet_qty'],
                'penagihandet_qtyreal' => $val['barangrusakdet_qty'],
                'penagihandet_subtotal' => $val['barangrusakdet_subtotal'],
                'penagihandet_ppn' => 0,
                'penagihandet_potongan' => 0,
                'penagihandet_total' => $val['barangrusakdet_subtotal'],
                't_returdet_qty' => 0,
                'penagihandet_jenis' => 'ganti rugi',
                'bulan' => 0,
                'tahun' => 0,
            );
        }
        return $rpenagihan;
    }
}