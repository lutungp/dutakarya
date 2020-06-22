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

    public function getJadwalDokter()
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
                WHERE FS_KD_LAYANAN = 'TM001'
                AND TA_JADWAL_DOKTER.FS_KD_DOKTER NOT IN (
                    SELECT 
                        TA_TRS_CLOSED_POLI.FS_KD_PETUGAS_MEDIS
                    FROM TA_TRS_CLOSED_POLI
                    INNER JOIN TA_TRS_CLOSED_POLI2 on TA_TRS_CLOSED_POLI2.FS_KD_TRS = TA_TRS_CLOSED_POLI.FS_KD_TRS
                    WHERE TA_TRS_CLOSED_POLI.FS_KD_LAYANAN = 'TM001'
                    AND TA_TRS_CLOSED_POLI2.FD_TGL_CLOSED IN ($tanggalStr)
                ) ORDER BY TA_JADWAL_DOKTER.FN_HARI ASC";
        
        $stmt = sqlsrv_query($this->conn, $tsql);
        if($stmt){
            return $stmt;
        } else {
            return false;
        }
    }

    public function getJadwalDokter2()
    {
        $concat = "CONCAT(TA_JADWAL_DOKTER.FS_JAM_MULAI, ' - ', TA_JADWAL_DOKTER.FS_JAM_SELESAI) AS JAM_KERJA";
        // $where = "WHERE FS_KD_LAYANAN IN ('RJ001', 'FS001', 'FS002', 'FS003', 'MCU01', 'RJ002', 'RJ003', 'RJ004', 'RJ005', 'RJ006', 'RJ007', 'RJ008', 'RJ009', 'RJ010', 'RJ011', 'RJ012', 'RJ013', 'RJ014', 'RJ015', 'RJ016', 'RJ017', 'RJ018', 'RJ019', 'RJ020', 'RJ021', 'RJ022', 'RJ023', 'PTK01', 'VKS01', 'DRS01', 'RJ024')";
        $where = " WHERE FS_KD_LAYANAN = 'TM001' ";
        $select = " jadwal.FS_KD_DOKTER, 
                    TD_PEG.FS_NM_PEG,
                    jadwal.FS_KD_LAYANAN";
        $tsql = 'SELECT 
                    ' . $select . ',
                    "1", "2", "3", "4", "5", "6", "7", TA_SMF.FS_KD_SMF, TA_SMF.FS_NM_SMF
                    FROM (SELECT FS_KD_DOKTER, FS_KD_LAYANAN, "1", "2", "3", "4", "5", "6", "7" FROM (
                    SELECT
                        TA_JADWAL_DOKTER.FS_KD_DOKTER,
                        ' . $concat . ',
                        TA_JADWAL_DOKTER.FS_KD_LAYANAN,
                        TA_JADWAL_DOKTER.FN_HARI
                    FROM TA_JADWAL_DOKTER
                    '.$where.'
                ) AS JADWAL
                PIVOT (
                    MIN(JAM_KERJA) FOR
                FN_HARI IN ([1], [2], [3], [4], [5], [6], [7])
                )  AS p) jadwal
                INNER JOIN TD_PEG ON TD_PEG.FS_KD_PEG = jadwal.FS_KD_DOKTER
                LEFT JOIN TA_SMF ON TA_SMF.FS_KD_SMF = TD_PEG.FS_KD_SMF';
        
        $stmt = sqlsrv_query($this->conn, $tsql);
        if($stmt){
            return $stmt;
        } else {
            return false;
        }
    }

    public function getJmlPasien($tgl, $kodedokter)
    {
        $sql = "SELECT COUNT(*) AS jmlantrian FROM t_booking_hospital 
                WHERE bookinghosp_tanggal = '".$tgl."' 
                AND t_booking_hospital.bookinghosp_aktif = 'Y'
                AND t_booking_hospital.bookinghosp_status != 'EXPIRED' ";
        $jml = 0;
        $query = $this->conn2->query($sql);
        if ($query) {
            $jml = $query->fetch_object()->jmlantrian;
        }

        /* jml sesuai jadwal */
        $harin = date('N', strtotime($tgl));
        /* menyesuaikan urutan jadwal dari hospital */
        $hariArr = ["2", "3", "4", "5", "6", "7", "1"];
        $hari = $hariArr[$harin-1];
        
        $tsql = "SELECT 
                    TA_JADWAL_DOKTER.FN_MAX
                FROM TA_JADWAL_DOKTER
                WHERE TA_JADWAL_DOKTER.FS_KD_LAYANAN = 'TM001' AND TA_JADWAL_DOKTER.FN_HARI = '$hari'
                AND TA_JADWAL_DOKTER.FS_KD_DOKTER = '$kodedokter'";

        $stmt = sqlsrv_query($this->conn, $tsql);
        $max = 6;
        if($stmt){
            $stmt = sqlsrv_fetch_object($stmt);
            $max = $stmt->FN_MAX;
        }

        if ($jml >= $max) {
            return "1";
        } else {
            return "0";
        }
    }
}