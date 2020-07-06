<?php

class M_hargabrg
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

    public function getBarangHna()
    {
        $sql = " SELECT 
                    baranghna_id,
                    baranghna_tgl,
                    baranghna_no,
                    baranghna_tglawal,
                    baranghna_tglakhir,
                    m_user.user_nama,
                    t_baranghna.baranghna_created_date
                FROM t_baranghna
                LEFT JOIN m_user ON m_user.user_id = t_baranghna.baranghna_created_by
                WHERE t_baranghna.baranghna_aktif = 'Y' ";
        $qbaranghna = $this->conn2->query($sql);
        $result = array();
        while ($val = $qbaranghna->fetch_array()) {
            $result[] = array(
                'baranghna_id' => $val['baranghna_id'],
                'baranghna_tgl' => $val['baranghna_tgl'],
                'baranghna_no' => $val['baranghna_no'],
                'baranghna_tglawal' => $val['baranghna_tglawal'],
                'baranghna_tglakhir' => $val['baranghna_tglakhir'],
                'user_nama' => $val['user_nama'],
                'baranghna_created_date' => $val['baranghna_created_date']
            );
        }

        return $result;
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
        
        $data['satuan_konversi'] = [];

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