<?php

class M_rekanan
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

    public function getRekanan()
    {
        $sql = "SELECT
                    rekanan_id,
                    rekanan_kode,
                    rekanan_nama,
                    rekanan_telp,
                    rekanan_email,
                    rekanan_alamat,
                    CASE WHEN rekanan_aktif = 'Y' THEN 'Aktif'
                        ELSE 'Tidak Aktif' END AS rekanan_aktif
                FROM m_rekanan ";
        
        $qrekanan = $this->conn2->query($sql);
        $rekanan = [];
        while ($result = $qrekanan->fetch_array(MYSQLI_ASSOC)) {
            array_push($rekanan, $result);
        }
        return $rekanan;
    }
    
}