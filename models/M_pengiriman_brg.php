<?php

class M_pengiriman_brg
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPengiriman()
    {
        $sql = " SELECT 
                    pengiriman_id,
                    pengiriman_no,
                    pengiriman_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama
                FROM t_pengiriman
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_pengiriman.m_rekanan_id
                WHERE pengiriman_aktif = 'Y'";
        $qpengiriman = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpengiriman->fetch_array()) {
            $result[] = array(
                'pengiriman_id' => $val['pengiriman_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
            );
        }

        return $result;
    }

    public function getRekanan($search)
    {
        $sql = "SELECT
                m_rekanan.rekanan_id AS id,
                m_rekanan.rekanan_kode,
                m_rekanan.rekanan_nama AS text
            FROM m_rekanan WHERE rekanan_aktif = 'Y'";
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

    public function getBarangSatkonv()
    {
        $sql = " SELECT  
                    barang_id,
                    barang_kode,
                    barang_nama,
                    m_satuan_id,
                    m_satuan.satuan_nama
                FROM m_barang
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id
                WHERE m_barang.barang_aktif = 'Y' ";

        $qbarang = $this->conn2->query($sql);
        $data['barang'] = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['barang'], $result);
        }

        $sql = " SELECT  
                    satkonv_id,
                    m_barang_id,
                    m_satuan.satuan_nama,
                    m_satuan_id,
                    satkonv_nilai
                FROM m_satuan_konversi
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_satuan_konversi.m_satuan_id
                WHERE m_satuan_konversi.satkonv_aktif = 'Y' ";

        $qsatuankonv = $this->conn2->query($sql);
        $data['satuan_konversi'] = [];
        while ($result = $qsatuankonv->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['satuan_konversi'], $result);
        }

        $sql = " SELECT  
                    satuan_id,
                    satuan_nama
                FROM m_satuan
                WHERE m_satuan.satuan_aktif = 'Y' ";

        $qsatuankonv = $this->conn2->query($sql);
        $data['satuan'] = [];
        while ($result = $qsatuankonv->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['satuan'], $result);
        }

        return $data;
    }

    public function getBarangSatuan($barang_id)
    {
        $sql = " SELECT  
                barang_id,
                barang_kode,
                barang_nama,
                m_satuan_id
            FROM m_barang
            WHERE m_barang.barang_id = $barang_id ";

        $qbarang = $this->conn2->query($sql);
        $rbarang = $qbarang->fetch_object();

        return $rbarang->m_satuan_id;
    }

    public function getStokAkhir($barang_id, $tanggal)
    {
        $sql = "SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_barangtrans.m_barang_id ORDER BY barangtrans_tgl DESC, barangtrans_id DESC ) AS rnumber,
                        barangtrans_akhir 
                    FROM
                        t_barangtrans 
                    WHERE
                        m_barang_id = $barang_id AND barangtrans_tgl <= '$tanggal'
                    ) AS barangtrans 
                WHERE barangtrans.rnumber <= 1 ";
                
        $qbarang = $this->conn2->query($sql);
        $barangtrans_akhir = 0;
        if ($qbarang) {
            $rbarang = $qbarang->fetch_object();
            $barangtrans_akhir = isset($rbarang->barangtrans_akhir) ? $rbarang->barangtrans_akhir : 0;
        }
        return $barangtrans_akhir;
    }

    public function getPengirimanData($pengiriman_id)
    {
        $sql = " SELECT 
                    pengiriman_id,
                    pengiriman_no,
                    pengiriman_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    t_pengiriman.t_penagihan_no
                FROM t_pengiriman
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_pengiriman.m_rekanan_id
                WHERE pengiriman_aktif = 'Y' 
                AND pengiriman_id = $pengiriman_id ";
        $qpengiriman = $this->conn2->query($sql);
        $rpengiriman = $qpengiriman->fetch_object();
        return $rpengiriman;
    }

    public function getPengirimanDataDetail($pengiriman_id, $pengirimandet_id = '')
    {
        $sql = " SELECT
                    t_pengiriman_detail.pengirimandet_id,
                    t_pengiriman_detail.t_pengiriman_id,
                    t_pengiriman_detail.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    t_pengiriman_detail.m_satuan_id,
                    m_satuan.satuan_nama,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    (t_pengiriman_detail.pengirimandet_harga/COALESCE(m_satuan_konversi.satkonv_nilai, 1)) AS baranghnadet_harga,
                    t_pengiriman_detail.pengirimandet_harga,
                    t_pengiriman_detail.pengirimandet_qty,
                    t_pengiriman_detail.pengirimandet_subtotal,
                    t_pengiriman_detail.pengirimandet_subtotal,
                    t_pengiriman_detail.pengirimandet_potongan,
                    t_pengiriman_detail.pengirimandet_total
                FROM t_pengiriman_detail
                JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                JOIN m_satuan ON m_satuan.satuan_id = t_pengiriman_detail.m_satuan_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id ";
        if ($pengirimandet_id <> '') {
            $sql .= "AND t_pengiriman_id IN (" . $pengirimandet_id . ") ";
        }

        if ($pengiriman_id > 0) {
            $sql .= " WHERE pengirimandet_aktif = 'Y' AND t_pengiriman_id = $pengiriman_id ";
        }
        
        $qpengiriman = $this->conn2->query($sql);
        $rpengiriman = array();
        while ($val = $qpengiriman->fetch_array()) {
            $rpengiriman[] = array(
                'pengirimandet_id' => $val['pengirimandet_id'],
                't_pengiriman_id' => $val['t_pengiriman_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'baranghnadet_harga' => $val['baranghnadet_harga'],
                'pengirimandet_harga' => $val['pengirimandet_harga'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'pengirimandet_qty' => $val['pengirimandet_qty'],
                'pengirimandet_subtotal' => $val['pengirimandet_subtotal'],
                'pengirimandet_potongan' => $val['pengirimandet_potongan'],
                'pengirimandet_total' => $val['pengirimandet_total']
            );
        }

        return $rpengiriman;
    }

    public function getPengirimanDataDetail2($pengirimandet_id)
    {
        $sql = " SELECT
                    t_pengiriman_detail.pengirimandet_id,
                    t_pengiriman_detail.t_pengiriman_id,
                    t_pengiriman_detail.m_barang_id,
                    m_barang.m_satuan_id AS satuanutama,
                    t_pengiriman_detail.m_satuan_id,
                    m_satuan_konversi.satkonv_nilai,
                    t_pengiriman_detail.pengirimandet_qty 
                FROM t_pengiriman_detail 
                LEFT JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id ";
        if ($pengirimandet_id <> '') {
            $sql .= "AND t_pengiriman_id IN (" . $pengirimandet_id . ") ";
        }
        
        $qpengiriman = $this->conn2->query($sql);
        $rpengiriman = array();
        while ($val = $qpengiriman->fetch_array()) {
            $rpengiriman[] = array(
                'pengirimandet_id' => $val['pengirimandet_id'],
                't_pengiriman_id' => $val['t_pengiriman_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuanutama' => $val['satuanutama'],
                'pengirimandet_qty' => $val['pengirimandet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai']
            );
        }

        return $rpengiriman;
    }

    public function getJadwal($day, $week, $month, $year, $tanggal)
    {
        $sql = "SELECT
                    t_jadwal.jadwal_id,
	                t_jadwal.m_rekanan_id,
                    t_jadwal.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    t_jadwal.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id,
                    m_satuan.satuan_nama,
                    kontrak.hargakontrak,
                    t_jadwal.hari,
                    t_jadwal.bulan,
                    t_jadwal.tahun,
                    t_jadwal.minggu1,
                    t_jadwal.minggu2,
                    t_jadwal.minggu3,
                    t_jadwal.minggu4,
                    t_jadwal.minggu5,
                    (kirim.satkonv_nilai * kirim.pengirimandet_qty) AS sudahkirim
                FROM t_jadwal
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_jadwal.m_rekanan_id
                INNER JOIN m_barang ON m_barang.barang_id = t_jadwal.m_barang_id
                INNER JOIN (
                    SELECT * FROM (
                        SELECT
                            ROW_NUMBER() OVER ( PARTITION BY t_hargakontrak_detail.m_barang_id, m_rekanan_id ORDER BY hargakontrakdet_tgl DESC, hargakontrakdet_id DESC ) AS rnumber,
                            m_rekanan_id, m_barang_id,
                            hargakontrakdet_harga AS hargakontrak
                        FROM
                            t_hargakontrak_detail 
                        WHERE hargakontrakdet_tgl <= '$tanggal'
                        ) AS kontrak 
                    WHERE kontrak.rnumber <= 1
                ) AS kontrak ON kontrak.m_barang_id = m_barang.barang_id AND kontrak.m_rekanan_id = t_jadwal.m_rekanan_id
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
                WHERE hari = $day AND bulan = $month AND tahun = $year";
        
        $qkirim = $this->conn2->query($sql);
        $rkirim = array();
        $hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
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
            $rkirim[] = array(
                'jadwal_id' => $val['jadwal_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'hargakontrak' => $val['hargakontrak'],
                'minggu' => $week,
                'hari' => $hari[$day],
                'jadwal_qty' => $qty,
                'sudahkirim' => $val['sudahkirim']
            );
        }

        return $rkirim;
    }

    public function getHargaKontrak($barang_id, $tanggal, $m_rekanan_id)
    {
        $sql = "SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_hargakontrak_detail.m_barang_id ORDER BY hargakontrakdet_tgl DESC, hargakontrakdet_id DESC ) AS rnumber,
                        hargakontrakdet_harga 
                    FROM
                        t_hargakontrak_detail 
                    WHERE
                        m_barang_id = $barang_id AND hargakontrakdet_tgl <= '$tanggal' AND m_rekanan_id = $m_rekanan_id
                    ) AS kontrak 
                WHERE kontrak.rnumber <= 1";
        $qbarang = $this->conn2->query($sql);
        $hargakontrakdet_harga = 0;
        if ($qbarang) {
            $rbarang = $qbarang->fetch_object();
            $hargakontrakdet_harga = isset($rbarang->hargakontrakdet_harga) ? $rbarang->hargakontrakdet_harga : 0;
        }
        return $hargakontrakdet_harga;
    }

}