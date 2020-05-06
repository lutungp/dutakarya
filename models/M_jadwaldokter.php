<?php

class M_jadwaldokter
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

    public function getJadwalDokter($layanan_kode, $tanggal)
    {
        $day = getWeekDaysFromDate($tanggal);
        $tanggalArr = [];
        $hariArr = [];
        foreach ($day as $val) {
            array_push($tanggalArr, "'".date("Y-m-d", strtotime($val["tanggal"]))."'");
            array_push($hariArr, $val["hari"]);
        }

        $tanggalStr = implode(",", $tanggalArr);
        $tsql = "SELECT 
                        TA_JADWAL_DOKTER.FS_KD_DOKTER,
                        TD_PEG.FS_NM_PEG,
                        TA_JADWAL_DOKTER.FS_JAM_MULAI,
                        TA_JADWAL_DOKTER.FS_JAM_SELESAI,
                        TA_JADWAL_DOKTER.FS_KD_LAYANAN,
                        TA_JADWAL_DOKTER.FN_HARI
                FROM TA_JADWAL_DOKTER
                INNER JOIN TD_PEG ON TD_PEG.FS_KD_PEG = TA_JADWAL_DOKTER.FS_KD_DOKTER
                WHERE FS_KD_LAYANAN = '$layanan_kode'
                AND TA_JADWAL_DOKTER.FS_KD_DOKTER NOT IN (
                    SELECT 
                        TA_TRS_CLOSED_POLI.FS_KD_PETUGAS_MEDIS
                    FROM TA_TRS_CLOSED_POLI
                    INNER JOIN TA_TRS_CLOSED_POLI2 on TA_TRS_CLOSED_POLI2.FS_KD_TRS = TA_TRS_CLOSED_POLI.FS_KD_TRS
                    WHERE TA_TRS_CLOSED_POLI.FS_KD_LAYANAN = '$layanan_kode' 
                    AND TA_TRS_CLOSED_POLI2.FD_TGL_CLOSED IN ($tanggalStr)
                ) ORDER BY TA_JADWAL_DOKTER.FN_HARI ASC";
        
        $stmt = sqlsrv_query($this->conn, $tsql);
        if($stmt){
            return $stmt;
        } else {
            return false;
        }
    }
}