<?php

class M_maklon
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getMaklon()
    {
        $sql = " SELECT 
                    maklon_id,
                    maklon_no,
                    maklon_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_user.user_nama,
                    t_maklon.maklon_created_date
                FROM t_maklon
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_maklon.m_rekanan_id
                LEFT JOIN m_user ON m_user.user_id = t_maklon.maklon_created_by
                WHERE maklon_aktif = 'Y' ORDER BY t_maklon.maklon_created_date DESC";
        $qmaklon = $this->conn2->query($sql);
        $result = array();
        while ($val = $qmaklon->fetch_array()) {
            $result[] = array(
                'maklon_id' => $val['maklon_id'],
                'maklon_no' => $val['maklon_no'],
                'maklon_tgl' => $val['maklon_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'user_nama' => $val['user_nama'],
                'maklon_created_date' => $val['maklon_created_date']
            );
        }

        return $result;
    }

    public function getRekanan($search, $jenis)
    {
        $sql = "SELECT
                m_rekanan.rekanan_id AS id,
                m_rekanan.rekanan_kode,
                CONCAT('[', m_rekanan.rekanan_kode, '] ', m_rekanan.rekanan_nama) AS text
            FROM m_rekanan WHERE rekanan_aktif = 'Y' AND rekanan_jenis = '$jenis'";
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

    public function getMaklonData($maklon_id)
    {
        $sql = " SELECT 
                    maklon_id,
                    maklon_no,
                    maklon_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_kode,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    m_user.user_nama
                FROM t_maklon
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_maklon.m_rekanan_id
                LEFT JOIN m_user ON m_user.user_id = t_maklon.maklon_created_by
                WHERE maklon_aktif = 'Y' 
                AND maklon_id = $maklon_id ";
                
        $qmaklon = $this->conn2->query($sql);
        $row = $qmaklon->fetch_object();

        $rmaklon = new stdClass();
        $rmaklon->maklon_id = $row->maklon_id;
        $rmaklon->maklon_no = $row->maklon_no;
        $rmaklon->maklon_tgl = $row->maklon_tgl;
        $rmaklon->m_rekanan_id = $row->m_rekanan_id;
        $rmaklon->rekanan_kode = $row->rekanan_kode;
        $rmaklon->rekanan_nama = $row->rekanan_nama;
        $rmaklon->rekanan_alamat = str_replace("<br />", "\\n", $row->rekanan_alamat);
        $rmaklon->user_nama = $row->user_nama;

        return $rmaklon;
    }

    public function getMaklonDataDetail($maklon_id, $maklondet_id = '')
    {
        $sql = " SELECT
                    t_maklondet.maklondet_id,
                    t_maklondet.t_maklon_id,
                    t_maklondet.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    t_maklondet.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_maklondet.m_bahanbakubrg_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    (t_maklondet.maklondet_harga/COALESCE(m_satuan_konversi.satkonv_nilai, 1)) AS hargaet_harga,
                    t_maklondet.maklondet_harga,
                    t_maklondet.maklondet_ppn,
                    CASE WHEN t_maklondet.maklondet_ppn > 0 THEN 'Y'
                        ELSE 'N' END AS hargakontrakdet_ppn,
                    t_maklondet.maklondet_qty,
                    t_maklondet.maklondet_subtotal,
                    t_maklondet.maklondet_subtotal,
                    t_maklondet.maklondet_total
                FROM t_maklondet
                JOIN m_barang ON m_barang.barang_id = t_maklondet.m_barang_id
                JOIN m_satuan ON m_satuan.satuan_id = t_maklondet.m_satuan_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_maklondet.m_satuan_id AND m_satuan_konversi.m_barang_id = t_maklondet.m_barang_id ";
        if ($maklondet_id <> '') {
            $sql .= "AND t_maklon_id IN (" . $maklondet_id . ") ";
        }

        if ($maklon_id > 0) {
            $sql .= " WHERE maklondet_aktif = 'Y' AND t_maklon_id = $maklon_id ";
        }
        
        $qmaklon = $this->conn2->query($sql);
        $rmaklon = array();
        while ($val = $qmaklon->fetch_array()) {
            $rmaklon[] = array(
                'maklondet_id' => $val['maklondet_id'],
                't_maklon_id' => $val['t_maklon_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'm_satuan_nama' => $val['satuan_nama'],
                'hargaet_harga' => $val['hargaet_harga'],
                'maklondet_harga' => $val['maklondet_harga'],
                'maklondet_ppn' => $val['maklondet_ppn'],
                'hargakontrak' => $val['maklondet_harga'] / $val['satkonv_nilai'],
                'hargakontrakdet_ppn' => $val['hargakontrakdet_ppn'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'm_bahanbakubrg_id' => $val['m_bahanbakubrg_id'],
                'maklondet_qty' => $val['maklondet_qty'],
                'maklondet_subtotal' => $val['maklondet_subtotal'],
                'maklondet_total' => $val['maklondet_total']
            );
        }

        return $rmaklon;
    }

    public function getMaklonDataDetail2($maklondet_id)
    {
        $sql = " SELECT
                    t_maklondet.maklondet_id,
                    t_maklondet.t_maklon_id,
                    t_maklondet.m_barang_id,
                    m_barang.m_satuan_id AS satuanutama,
                    t_maklondet.m_satuan_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    t_maklondet.maklondet_qty,
                    t_maklondet.t_returdet_qty
                FROM t_maklondet 
                LEFT JOIN m_barang ON m_barang.barang_id = t_maklondet.m_barang_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_maklondet.m_satuan_id AND m_satuan_konversi.m_barang_id = t_maklondet.m_barang_id 
                WHERE t_maklondet.maklondet_aktif = 'Y' ";
        if ($maklondet_id == '') {
            $sql .= "AND t_maklon_id IN (" . $maklondet_id . ") ";
        } else {
            $sql .= "AND maklondet_id IN (" . $maklondet_id . ") ";
        }
        
        $qmaklon = $this->conn2->query($sql);
        $rmaklon = array();
        while ($val = $qmaklon->fetch_array()) {
            $rmaklon[] = array(
                'maklondet_id' => $val['maklondet_id'],
                't_maklon_id' => $val['t_maklon_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuanutama' => $val['satuanutama'],
                'maklondet_qty' => $val['maklondet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                't_returdet_qty' => $val['t_returdet_qty']
            );
        }

        return $rmaklon;
    }

    public function getHargaKontrak($barang_id, $tanggal, $m_rekanan_id)
    {
        $sql = "SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_hargakontrak_detail.m_barang_id ORDER BY hargakontrakdet_tgl DESC, hargakontrakdet_id DESC ) AS rnumber,
                        hargakontrakdet_harga,
                        hargakontrakdet_ppn
                    FROM
                        t_hargakontrak_detail 
                    WHERE
                        m_barang_id = $barang_id AND hargakontrakdet_tgl <= '$tanggal' AND m_rekanan_id = $m_rekanan_id
                    ) AS kontrak 
                WHERE kontrak.rnumber <= 1";
        $qbarang = $this->conn2->query($sql);
        $result['hargakontrakdet_harga'] = 0;
        $result['hargakontrakdet_ppn'] = 'N';
        if ($qbarang) {
            $rbarang = $qbarang->fetch_object();
            $result['hargakontrakdet_harga'] = isset($rbarang->hargakontrakdet_harga) ? $rbarang->hargakontrakdet_harga : 0;
            $result['hargakontrakdet_ppn'] = isset($rbarang->hargakontrakdet_ppn) ? $rbarang->hargakontrakdet_ppn : 'N';
        }
        return $result;
    }

    public function getPegawai($search, $bagian, $tanggal)
    {
        $tanggal = strtotime(date('Y-m-d', strtotime($tanggal)));
        $day = date('N', $tanggal);
        $sql = "SELECT
                DISTINCT m_pegawai.pegawai_id AS id,
                m_pegawai.pegawai_nama AS text
            FROM m_pegawai
            INNER JOIN t_jadwalpeg ON t_jadwalpeg.m_pegawai_id = m_pegawai.pegawai_id
            WHERE pegawai_aktif = 'Y' AND m_pegawai.pegawai_bagian = '$bagian' AND t_jadwalpeg.hari = $day";

        if ($search <> '') {
            $sql .= " AND m_pegawai.pegawai_nama LIKE '%".$search."%' ";
        }
        
        $qpegawai = $this->conn2->query($sql);
        $pegawai = [];
        while ($result = $qpegawai->fetch_array(MYSQLI_ASSOC)) {
            array_push($pegawai, $result);
        }
        return $pegawai;
    }

    public function getBahanBaku($barang_id)
    {
        $sql = "SELECT 
                    bahanbrg_id, 
                    m_brg_id, 
                    m_barang_id, 
                    m_barang.barang_nama, 
                    m_barang.m_satuan_id,
                    m_satuan.satuan_nama,
                    bahanbrg_qty, 
                    bahanbrg_ketika
                FROM m_bahanbrg
                LEFT JOIN m_barang ON m_barang.barang_id = m_bahanbrg.m_barang_id
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id
                WHERE m_bahanbrg.bahanbrg_aktif = 'Y'
                AND m_bahanbrg.m_brg_id = $barang_id AND bahanbrg_ketika = 'produksi'";
        
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        while ($val = $qbarang->fetch_array()) {
            $rbarang[] = array(
                'bahanbrg_id' => $val['bahanbrg_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'bahanbrg_qty' => $val['bahanbrg_qty'],
                'bahanbrg_ketika' => $val['bahanbrg_ketika'],
            );
        }

        return $rbarang;
    }

}