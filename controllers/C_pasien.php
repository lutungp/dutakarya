<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';

class C_pasien
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $FS_MR)
    {
        $this->conn2 = $conn2;
        $FS_MR2 = str_pad($FS_MR, 8, "0", STR_PAD_LEFT);
        $tsql = "SELECT FS_MR, FD_TGL_MR, FS_NM_ALIAS, FS_NM_PASIEN FROM TC_MR WHERE FS_MR like '%$FS_MR2' ";
        $stmt = sqlsrv_query( $conn, $tsql);
        /* cek pasien antrian */
        $sql = "SELECT COUNT(*) AS booking FROM t_booking_hospital WHERE pasien_norm like '%$FS_MR' AND bookinghosp_aktif = 'Y' AND (bookinghosp_status = 'ANTRIAN' OR bookinghosp_status = 'SUDAH BAYAR')";
        $query = $this->conn2->query($sql);

        $return = array(
            "code" => "202"
        );

        if($stmt){
            $stmt = sqlsrv_fetch_object($stmt);
            $return = array(
                "code" => "200",
                "data" => $stmt
            );
        }

        if($query) {
            $row = mysqli_fetch_object($query);
            if ($row->booking > 0) {
                $return = array(
                    "code" => "203",
                    "message" => "Pasien No. RM " . $FS_MR . ", atas nama " . $stmt->FS_NM_PASIEN . " sudah dalam daftar antrian"
                );
            }
        }

        echo json_encode($return);
    }
}

$FS_MR = $_GET["rm"];
new C_pasien($conn, $conn2, $FS_MR);
