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
                    rekanan_jenis,
                    rekanan_telp,
                    rekanan_email,
                    rekanan_alamat,
                    CASE WHEN rekanan_aktif = 'Y' THEN 'Aktif'
                        ELSE 'Tidak Aktif' END AS rekanan_aktif
                FROM m_rekanan ";
        
        $qrekanan = $this->conn2->query($sql);
        $rekanan = [];
        while ($result = $qrekanan->fetch_array(MYSQLI_ASSOC)) {
            $rekanan[] = array(
                "rekanan_id" => $result['rekanan_id'],
                "rekanan_kode" => $result['rekanan_kode'],
                "rekanan_nama" => $result['rekanan_nama'],
                "rekanan_jenis" => $result['rekanan_jenis'],
                "rekanan_telp" => $result['rekanan_telp'],
                "rekanan_email" => $result['rekanan_email'],
                "rekanan_alamat" => str_replace("<br />", "\n", $result['rekanan_alamat']),
            );
        }
        return $rekanan;
    }
    
}