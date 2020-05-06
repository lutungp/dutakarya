<?php

include '../config/database.php';
include '../config/helpers.php';

class C_pasien
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $FS_MR)
    {
        $this->conn2 = $conn2;
        $tsql = "SELECT FS_MR, FD_TGL_MR, FS_NM_ALIAS, FS_NM_PASIEN FROM TC_MR WHERE FS_MR = $FS_MR";
        $stmt = sqlsrv_query( $conn, $tsql);
        if($stmt){
            $stmt = sqlsrv_fetch_object($stmt);
            echo json_encode($stmt);
        } else {
            echo json_encode([]);
        }
    }
}

$FS_MR = $_GET["rm"];
new C_pasien($conn, $conn2, $FS_MR);
