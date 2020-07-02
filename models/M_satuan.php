<?php

class M_satuan
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

    public function getSatuan()
    {
        $sql = "SELECT
                    satuan_id,
                    satuan_kode,
                    satuan_nama,
                    CASE WHEN satuan_aktif = 'Y' THEN 'Aktif'
                        ELSE 'Tidak Aktif' END AS satuan_aktif
                FROM m_satuan ";
        
        $qsatuan = $this->conn2->query($sql);
        $satuan = [];
        while ($result = $qsatuan->fetch_array(MYSQLI_ASSOC)) {
            array_push($satuan, $result);
        }
        return $satuan;
    }
    
}