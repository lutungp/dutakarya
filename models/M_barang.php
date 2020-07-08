<?php

class M_barang
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
        $sql = "SELECT
                    barang_id,
                    barang_kode,
                    barang_nama,
                    m_satuan_id,
                    m_satuan.satuan_nama,
                    CASE WHEN barang_aktif = 'Y' THEN 'Aktif'
                        ELSE 'Tidak Aktif' END AS barang_aktif
                FROM m_barang 
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id";
        $qbarang = $this->conn2->query($sql);
        $barang = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($barang, $result);
        }
        return $barang;
    }

    public function getSatuan()
    {
        $sql = "SELECT
                    m_satuan.satuan_id as id,
                    m_satuan.satuan_kode,
                    m_satuan.satuan_nama as text
                FROM m_satuan WHERE satuan_aktif = 'Y'";
        $qsatuan = $this->conn2->query($sql);
        $satuan = [];
        while ($result = $qsatuan->fetch_array(MYSQLI_ASSOC)) {
            array_push($satuan, $result);
        }
        return $satuan;
    }

    public function getSatkonv($barang_id)
    {
        $sql = " SELECT satkonv_id, m_barang_id, m_satuan_id, satkonv_nilai FROM m_satuan_konversi WHERE satkonv_aktif = 'Y' AND m_barang_id = $barang_id";
        $qsatkonv = $this->conn2->query($sql);
        $satkonv = [];
        while ($result = $qsatkonv->fetch_array(MYSQLI_ASSOC)) {
            array_push($satkonv, $result);
        }
        return $satkonv;
    }
    
    public function getHarga($barang_id)
    {
        $sql = "SELECT 
                    t_baranghna.baranghna_no,
                    t_baranghna_detail.baranghnadet_tglawal,
                    t_baranghna_detail.baranghnadet_harga
                FROM t_baranghna
                JOIN t_baranghna_detail ON t_baranghna_detail.t_baranghna_id = t_baranghna.baranghna_id
                WHERE t_baranghna.baranghna_aktif = 'Y'
                AND t_baranghna_detail.m_barang_id = $barang_id
                AND t_baranghna_detail.baranghnadet_aktif = 'Y'
                ORDER BY t_baranghna_detail.baranghnadet_tglawal ASC, t_baranghna_detail.baranghnadet_created_date ASC ";
        $qbaranghna = $this->conn2->query($sql);
        $rbaranghna = array();
        while ($val = $qbaranghna->fetch_array()) {
            $rbaranghna[] = array(
                'baranghna_no' => $val['baranghna_no'],
                'baranghnadet_tglawal' => $val['baranghnadet_tglawal'],
                'baranghnadet_harga' => $val['baranghnadet_harga'],
            );
        }

        return $rbaranghna;
    }
}