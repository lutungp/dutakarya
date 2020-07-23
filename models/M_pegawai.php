<?php

class M_pegawai
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

    public function getPegawai()
    {
        $sql = " SELECT 
                    m_pegawai.pegawai_id, 
                    m_pegawai.pegawai_no,
                    m_pegawai.pegawai_nama,
                    m_pegawai.pegawai_bagian,
                    m_pegawai.pegawai_notelp,
                    m_pegawai.pegawai_alamat,
                    m_pegawai.pegawai_bagian,
                    CASE WHEN pegawai_aktif = 'Y' THEN 'Aktif' ELSE 'Tidak Aktif' END AS pegawaiaktif
                FROM m_pegawai";
        $qpegawai = $this->conn2->query($sql);
        $rpegawai = array();
        while ($val = $qpegawai->fetch_array()) {
            $rpegawai[] = array(
                "pegawai_id" => $val['pegawai_id'],
                "pegawai_no" => $val['pegawai_no'],
                "pegawai_nama" => $val['pegawai_nama'],
                "pegawai_notelp" => $val['pegawai_notelp'],
                "pegawai_bagian" => $val['pegawai_bagian'],
                "pegawaiaktif" => $val['pegawaiaktif'],
                "pegawai_alamat" => str_replace("<br />", "\n", $val['pegawai_alamat']),
            );
        }

        return $rpegawai;
    }

    public function getJadwal($pegawai_id)
    {
        $sql = "SELECT
                    jadwalpeg_id,
                    m_pegawai_id,
                    hari,
                    jadwalpeg_aktif
                FROM t_jadwalpeg
                WHERE jadwalpeg_aktif = 'Y'
                AND t_jadwalpeg.m_pegawai_id = $pegawai_id
                ORDER BY t_jadwalpeg.hari ASC ";
        $rjadwal = array();
        $qjadwal = $this->conn2->query($sql);
        while ($val = $qjadwal->fetch_array()) {
            $rjadwal[] = array(
                "jadwalpeg_id" => $val['jadwalpeg_id'],
                "m_pegawai_id" => $val['m_pegawai_id'],
                "hari" => $val['hari'],
                "jadwalpeg_aktif" => $val['jadwalpeg_aktif']
            );
        }

        return $rjadwal;   
    }
    
}