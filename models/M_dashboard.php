<?php

class M_dashboard
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

    public function getJadwal($day, $week, $month, $year, $tanggal, $rit, $barangArr, $rekananArr)
    {
        $sql = "SELECT
                    t_jadwal.jadwal_id,
                    t_jadwal.m_rekanan_id,
                    t_jadwal.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    t_jadwal.m_pegdriver_id,
                    driver.pegawai_nama AS m_pegdriver_nama,
                    t_jadwal.m_peghelper_id,
                    helper.pegawai_nama AS m_peghelper_nama,
                    t_jadwal.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_jadwal.hari,
                    t_jadwal.bulan,
                    t_jadwal.tahun,
                    t_jadwal.rit,
                    t_jadwal.minggu1,
                    t_jadwal.minggu2,
                    t_jadwal.minggu3,
                    t_jadwal.minggu4,
                    t_jadwal.minggu5,
                    (kirim.satkonv_nilai * kirim.pengirimandet_qty) AS sudahkirim
                FROM t_jadwal
                LEFT JOIN m_pegawai AS driver ON driver.pegawai_id = t_jadwal.m_pegdriver_id
                LEFT JOIN m_pegawai AS helper ON helper.pegawai_id = t_jadwal.m_peghelper_id
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_jadwal.m_rekanan_id
                INNER JOIN m_barang ON m_barang.barang_id = t_jadwal.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id
                LEFT JOIN (
                    SELECT
                        t_pengiriman.m_rekanan_id,
                        t_pengiriman_detail.m_barang_id,
                        COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                        SUM(t_pengiriman_detail.pengirimandet_qty) AS pengirimandet_qty
                    FROM t_pengiriman_detail
                    LEFT JOIN t_pengiriman ON t_pengiriman.pengiriman_id = t_pengiriman_detail.t_pengiriman_id
                    LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id
                    WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y' AND t_pengiriman.pengiriman_tgl = '$tanggal'
                    GROUP BY t_pengiriman.m_rekanan_id, t_pengiriman_detail.m_barang_id, m_satuan_konversi.satkonv_nilai
                ) AS kirim ON kirim.m_barang_id = m_barang.barang_id AND kirim.m_rekanan_id = t_jadwal.m_rekanan_id
                WHERE m_rekanan.rekanan_aktif = 'Y' AND t_jadwal.rit = $rit ";
        if ($barangArr <> '') {
            $sql .= " AND m_barang.barang_id IN ($barangArr) ";
        }
        if ($rekananArr <> '') {
            $sql .= " AND m_rekanan.rekanan_id IN ($rekananArr) ";
        }
        if ($day != '') {
            $sql .= " AND hari = $day ";
        }
        if ($month != '') {
            $sql .= " AND bulan = $month ";
        }
        if ($year != '') {
            $sql .= " AND tahun = $year ";
        }
        $sql .= "ORDER BY t_jadwal.rit ASC, m_rekanan.rekanan_nama ASC";
        
        $qkirim = $this->conn2->query($sql);
        $hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $rkirim = [];
        while ($val = $qkirim->fetch_array()) {
            $qty = 0;
            if ($week == 1) {
                $qty = $val['minggu1'];
            }
            if ($week == 2) {
                $qty = $val['minggu2'];
            }
            if ($week == 3) {
                $qty = $val['minggu3'];
            }
            if ($week == 4) {
                $qty = $val['minggu4'];
            }
            if ($week == 5) {
                $qty = $val['minggu5'];
            }

            if ($qty>0) {
                $rkirim[] = array(
                    'jadwal_id' => $val['jadwal_id'],
                    'm_pegdriver_id' => $val['m_pegdriver_id'],
                    'm_pegdriver_nama' => $val['m_pegdriver_nama'],
                    'm_peghelper_id' => $val['m_peghelper_id'],
                    'm_peghelper_nama' => $val['m_peghelper_nama'],
                    'm_rekanan_id' => $val['m_rekanan_id'],
                    'rekanan_nama' => $val['rekanan_nama'],
                    'm_barang_id' => $val['m_barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'm_satuan_id' => $val['m_satuan_id'],
                    'satuan_nama' => $val['satuan_nama'],
                    'minggu' => $week,
                    'hari' => $hari[$day],
                    'rit' => $val['rit'],
                    'jadwal_qty' => $qty,
                    'sudahkirim' => $val['sudahkirim']
                );
            }
        }

        return $rkirim;
    }

}