<?php

class M_penerimaan_brg
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPenerimaan()
    {
        $sql = " SELECT 
                    penerimaan_id,
                    penerimaan_no,
                    penerimaan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    penerimaan_catatan
                FROM t_penerimaan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penerimaan.m_rekanan_id
                WHERE penerimaan_aktif = 'Y' ORDER BY t_penerimaan.penerimaan_created_date DESC ";
        $qpenerimaan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenerimaan->fetch_array()) {
            $result[] = array(
                'penerimaan_id' => $val['penerimaan_id'],
                'penerimaan_no' => $val['penerimaan_no'],
                'penerimaan_tgl' => $val['penerimaan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'penerimaan_catatan' => $val['penerimaan_catatan'],
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

    public function getPenerimaanData($penerimaan_id)
    {
        $sql = " SELECT 
                    penerimaan_id,
                    penerimaan_no,
                    penerimaan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    penerimaan_catatan
                FROM t_penerimaan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penerimaan.m_rekanan_id
                WHERE penerimaan_aktif = 'Y' 
                AND penerimaan_id = $penerimaan_id ";
        $qpenerimaan = $this->conn2->query($sql);
        $rpenerimaan = $qpenerimaan->fetch_object();
        return $rpenerimaan;
    }

    public function getPenerimaanDataDetail($penerimaan_id, $penerimaandet_id = '')
    {
        $sql = " SELECT
                    t_penerimaan_detail.penerimaandet_id,
                    t_penerimaan_detail.t_penerimaan_id,
                    t_penerimaan_detail.m_barang_id,
                    t_penerimaan_detail.m_satuan_id,
                    m_satuan_konversi.satkonv_nilai,
                    t_penerimaan_detail.penerimaandet_qty 
                FROM t_penerimaan_detail 
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_penerimaan_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_penerimaan_detail.m_barang_id ";
        if ($penerimaandet_id <> '') {
            $sql .= "AND t_penerimaan_id IN (" . $penerimaandet_id . ") ";
        }

        if ($penerimaan_id > 0) {
            $sql .= " WHERE penerimaandet_aktif = 'Y' AND t_penerimaan_id = $penerimaan_id ";
        }
        
        $qpenerimaan = $this->conn2->query($sql);
        $rpenerimaan = array();
        while ($val = $qpenerimaan->fetch_array()) {
            $rpenerimaan[] = array(
                'penerimaandet_id' => $val['penerimaandet_id'],
                't_penerimaan_id' => $val['t_penerimaan_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'penerimaandet_qty' => $val['penerimaandet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai']
            );
        }

        return $rpenerimaan;
    }

    public function getPenerimaanDataDetail2($penerimaandet_id)
    {
        $sql = " SELECT
                    t_penerimaan_detail.penerimaandet_id,
                    t_penerimaan_detail.t_penerimaan_id,
                    t_penerimaan_detail.m_barang_id,
                    m_barang.m_satuan_id AS satuanutama,
                    t_penerimaan_detail.m_satuan_id,
                    m_satuan_konversi.satkonv_nilai,
                    t_penerimaan_detail.penerimaandet_qty 
                FROM t_penerimaan_detail 
                LEFT JOIN m_barang ON m_barang.barang_id = t_penerimaan_detail.m_barang_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_penerimaan_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_penerimaan_detail.m_barang_id 
                WHERE 1=1 ";
        if ($penerimaandet_id <> '') {
            $sql .= "AND penerimaandet_id IN (" . $penerimaandet_id . ") ";
        }
        
        $qpenerimaan = $this->conn2->query($sql);
        $rpenerimaan = array();
        while ($val = $qpenerimaan->fetch_array()) {
            $rpenerimaan[] = array(
                'penerimaandet_id' => $val['penerimaandet_id'],
                't_penerimaan_id' => $val['t_penerimaan_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuanutama' => $val['satuanutama'],
                'penerimaandet_qty' => $val['penerimaandet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai']
            );
        }

        return $rpenerimaan;
    }

}