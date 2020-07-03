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
                WHERE penerimaan_aktif = 'Y'";
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
                    m_satuan_id
                FROM m_barang
                WHERE m_barang.barang_aktif = 'Y' ";

        $qbarang = $this->conn2->query($sql);
        $data['barang'] = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['barang'], $result);
        }

        $sql = " SELECT  
                    satkonv_id,
                    m_barang_id,
                    m_satuan_id,
                    satkonv_nilai
                FROM m_satuan_konversi
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

}