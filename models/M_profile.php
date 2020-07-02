<?php

class M_profile
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getUnit()
    {
        $sql = "SELECT unit_id AS id, unit_nama AS text FROM m_unit WHERE unit_aktif = 'Y'";
        $qunit = $this->conn2->query($sql);
        $runit = array();
        while ($val = $qunit->fetch_array()) {
            array_push($runit, $val);
        }

        return $runit;
    }

    public function getRegional($search)
    {
        if ($search <> '') {
            $search = " WHERE UPPER(reg_villages.NAME) LIKE '%".strtoupper($search)."%'";
        }
        
        $sql = " SELECT
                    kelurahan_id AS id,
                    CONCAT(kelurahan_nama, ', ', kecamatan_nama, ', ', kota_nama, ', ', provinsi_nama) AS text 
                FROM
                    (
                    SELECT
                        reg_villages.id AS kelurahan_id,
                        reg_villages.NAME AS kelurahan_nama,
                        reg_districts.NAME AS kecamatan_nama,
                        reg_regencies.NAME AS kota_nama,
                        reg_provinces.NAME AS provinsi_nama 
                    FROM
                        reg_villages
                        JOIN reg_districts ON reg_districts.id = reg_villages.district_id
                        JOIN reg_regencies ON reg_regencies.id = reg_districts.regency_id
                    JOIN reg_provinces ON reg_provinces.id = reg_regencies.province_id
                    $search
                    ) AS kelurahan
                LIMIT 20";
        
        $qkelurahan = $this->conn2->query($sql);
        $rkelurahan = array();
        while ($val = $qkelurahan->fetch_array()) {
            array_push($rkelurahan, $val);
        }

        return $rkelurahan;         
    }

    public function getKota($search)
    {
        if ($search <> '') {
            $search = " WHERE UPPER(reg_regencies.NAME) LIKE '%".strtoupper($search)."%'";
        }
        
        $sql = " SELECT
                    reg_regencies.id AS id,
                    reg_regencies.NAME AS text
                FROM reg_regencies
                $search
                LIMIT 20";
        
        $qkota = $this->conn2->query($sql);
        $rkota = array();
        while ($val = $qkota->fetch_array()) {
            array_push($rkota, $val);
        }

        return $rkota;         
    }

    public function getKeluarga($pegawai_id)
    {
        $sql = " SELECT
                    pegkeluarga_id,
                    pegkeluarga_hub,
                    pegkeluarga_alias,
                    pegkeluarga_nama,
                    pegkeluarga_telp,
                    pegkeluarga_nokk,
                    pegkeluarga_noid,
                    pegkeluarga_kartubpjs,
                    pegkeluarga_kartubpjstk,
                    m_kotalahir_id,
                    reg_regencies.name m_kotalahir_nama,
                    pegkeluarga_tgllahir,
                    pegkeluarga_kelamin
                 FROM m_pegawai_keluarga 
                 LEFT JOIN reg_regencies ON reg_regencies.id = m_pegawai_keluarga.m_kotalahir_id
                 WHERE m_pegawai_id = $pegawai_id
                 AND pegkeluarga_aktif = 'Y' ";
                 
        $qkeluarga = $this->conn2->query($sql);
        $rkeluarga = array();
        while ($val = $qkeluarga->fetch_array()) {
            array_push($rkeluarga, $val);
        }

        return $rkeluarga;
    }
}
