<?php

class M_infostock
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

    public function getStock($tanggal)
    {
        $tanggal = $tanggal <> '' ? date('Y-m-d', strtotime($tanggal)) : date("Y-m-d");
        $sql = "SELECT
                    barangtrans_tgl,
                    m_satuan.satuan_id,
                    m_satuan.satuan_nama,
                    m_barang.barang_id,
                    m_barang.barang_nama,
                    barangtrans_akhir 
                FROM
                    (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_barangtrans.m_barang_id ORDER BY barangtrans_tgl DESC, barangtrans_id DESC ) AS rnumber,
                        barangtrans_tgl,
                        t_barangtrans.m_satuan_id,
                        t_barangtrans.m_barang_id,
                        barangtrans_akhir 
                    FROM
                        t_barangtrans 
                    WHERE
                        barangtrans_tgl <= '$tanggal'
                ) AS barangtrans
                    INNER JOIN m_barang ON m_barang.barang_id = barangtrans.m_barang_id
                    INNER JOIN m_satuan ON m_satuan.satuan_id = barangtrans.m_satuan_id 
                WHERE barangtrans.rnumber <= 1 ";
                
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        if ($qbarang) {
            while ($val = $qbarang->fetch_array()) {
                $rbarang[] = array(
                    'satuan_id' => $val['satuan_id'],
                    'satuan_nama' => $val['satuan_nama'],
                    'barang_id' => $val['barang_id'],
                    'barang_nama' => $val['barang_nama'],
                    'barangtrans_akhir' => $val['barangtrans_akhir'],
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
}