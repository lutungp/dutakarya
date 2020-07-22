<?php

class M_infopengiriman
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

    public function getPengiriman($tanggal, $rekananArr, $barangArr)
    {
        $sql = "SELECT 
                pengiriman_id,
                pengiriman_no,
                pengiriman_tgl,
                m_rekanan_id,
                m_rekanan.rekanan_nama,
                t_pengiriman_detail.m_barang_id,
                m_barang.barang_nama,
                m_satuan.satuan_nama,
                t_pengiriman_detail.pengirimandet_qty
            FROM t_pengiriman
            INNER JOIN t_pengiriman_detail ON t_pengiriman_detail.t_pengiriman_id = t_pengiriman.pengiriman_id
            INNER JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
            INNER JOIN m_satuan ON m_satuan.satuan_id = t_pengiriman_detail.m_satuan_id
            INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_pengiriman.m_rekanan_id
            WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y'";
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

        $sql .=  "ORDER BY t_pengiriman.pengiriman_tgl ASC ";

        $qpengiriman = $this->conn2->query($sql);
        $rpengiriman = array();
        if ($qpengiriman) {
            while ($val = $qpengiriman->fetch_array()) {
                $rpengiriman[] = array(
                    'pengiriman_id' => $val['pengiriman_id'],
                    'pengiriman_no' => $val['pengiriman_no'],
                    'pengiriman_tgl' => $val['pengiriman_tgl'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'rekanan_nama' => $val['rekanan_nama'],
                    'm_barang_id' => $val['m_barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'satuan_nama' => $val['satuan_nama'],
                    'pengirimandet_qty' => $val['pengirimandet_qty'],
                );
            }
        }
        return $rpengiriman;
    }

    public function getRekanan($rekananArr = '')
    {
        $sql = "SELECT
                m_rekanan.rekanan_id,
                m_rekanan.rekanan_kode,
                m_rekanan.rekanan_nama,
                rekanan_alamat
            FROM m_rekanan WHERE rekanan_aktif = 'Y'";
        
        if ($rekananArr <> '') {
            $sql .= " AND m_rekanan.rekanan_id IN (".$rekananArr.")";
        }

        $qrekanan = $this->conn2->query($sql);
        $rekanan = [];
        while ($val = $qrekanan->fetch_array(MYSQLI_ASSOC)) {
            $rekanan[] = array(
                'rekanan_id' => $val['rekanan_id'],
                'rekanan_kode' => $val['rekanan_kode'],
                'rekanan_nama' => $val['rekanan_nama'],
                'rekanan_alamat' => str_replace("<br />", "\\n", $val['rekanan_alamat'])
            );
        }
        return $rekanan;
    }

    public function getJadwalKirim($rekananArr, $barang_id, $hari, $bulan, $tahun, $rit)
    {
        $day['1'] = 2;
        $day['2'] = 3;
        $day['3'] = 4;
        $day['4'] = 5;
        $day['5'] = 6;
        $day['6'] = 7;
        $day['7'] = 1;
        $sql = "SELECT 
                    t_pengiriman.pengiriman_id,
                    t_pengiriman.pengiriman_no,
                    t_pengiriman.pengiriman_tgl,
                    t_pengiriman.m_rekanan_id,
                    t_pengiriman_detail.m_barang_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    m_barang.barang_nama,
                    m_satuan.satuan_nama,
                    t_pengiriman_detail.pengirimandet_qty,
                    t_pengiriman_detail.t_returdet_qty
                FROM t_pengiriman
                INNER JOIN t_pengiriman_detail ON t_pengiriman_detail.t_pengiriman_id = t_pengiriman.pengiriman_id
                INNER JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_pengiriman_detail.m_satuan_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id
                WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y' 
                AND t_pengiriman_detail.m_barang_id = $barang_id
                AND MONTH(t_pengiriman.pengiriman_tgl) = $bulan AND YEAR(t_pengiriman.pengiriman_tgl) = $tahun
                AND DAYOFWEEK(t_pengiriman.pengiriman_tgl) = $day[$hari]";
        
        if ($rekananArr <> '') {
            $sql .= " AND t_pengiriman.m_rekanan_id IN (".$rekananArr.")";
        }

        if ($rit > 0) {
            $sql .= " AND t_pengiriman.rit = $rit ";
        }

        $qpengiriman = $this->conn2->query($sql);
        $rpengiriman = array();
        if ($qpengiriman) {
            while ($val = $qpengiriman->fetch_array()) {
                $tanggal = strtotime(date('Y-m-d', strtotime($val['pengiriman_tgl'])));
                
                $day = date('N', $tanggal);
                $month = intval(date('m', $tanggal));
                $year = intval(date('Y', $tanggal));
                $week = getWeeks($val['pengiriman_tgl'], $day) - 1;
                $rpengiriman[] = array(
                    'pengiriman_id' => $val['pengiriman_id'],
                    'pengiriman_no' => $val['pengiriman_no'],
                    'pengiriman_tgl' => $val['pengiriman_tgl'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'm_barang_id' => $val['m_barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'satuan_nama' => $val['satuan_nama'],
                    'pengirimandet_qty' => $val['pengirimandet_qty'] * $val['satkonv_nilai'],
                    't_returdet_qty' => $val['t_returdet_qty'],
                    'hari' => $day,
                    'minggu' => $week,
                    'bulan' => $month,
                    'tahun' => $year,
                );
            }
        }

        return $rpengiriman;
    }

    public function getJadwal($rekananArr, $barang_id, $hari, $bulan, $tahun, $rit)
    {
        $sql = "SELECT 
                    jadwal_id,
                    m_rekanan_id,
                    hari,
                    bulan,
                    tahun,
                    minggu1,
                    minggu2,
                    minggu3,
                    minggu4,
                    minggu5,
                    m_barang_id
                FROM t_jadwal 
                WHERE t_jadwal.m_barang_id= $barang_id AND bulan = $bulan AND tahun = $tahun AND hari = $hari";
        if ($rekananArr <> '') {
            $sql .= " AND t_jadwal.m_rekanan_id IN (".$rekananArr.")";
        }
        if ($rit > 0) {
            $sql .= " AND t_jadwal.rit = $rit";
        }
        $qjadwal = $this->conn2->query($sql);
        $rjadwal = array();
        while ($val = $qjadwal->fetch_array()) {
            $rjadwal[] = array(
                'jadwal_id' => $val['jadwal_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'hari' => $val['hari'],
                'bulan' => $val['bulan'],
                'tahun' => $val['tahun'],
                'minggu1' => $val['minggu1'],
                'minggu2' => $val['minggu2'],
                'minggu3' => $val['minggu3'],
                'minggu4' => $val['minggu4'],
                'minggu5' => $val['minggu5'],
                'm_barang_id' => $val['m_barang_id'],
            );
        }

        return $rjadwal;
    }

}