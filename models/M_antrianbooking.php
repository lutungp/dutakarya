<?php

class M_antrianbooking
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

    public function getAntrianBooking($pasien_norm)
    {
        $tsql = "SELECT 
                    pasien_norm, pasien_nama, pasien_telp, 
                    bookinghosp_urutan, pasien_email, bookinghosp_tanggal,
                    layanan_kode, bookinghosp_status
                FROM t_booking_hospital 
                WHERE bookinghosp_aktif = 'Y' 
                AND bookinghosp_status = 'ANTRIAN' 
                AND pasien_norm = '$pasien_norm'";

        $query = $this->conn2->query($tsql);
        if($query){
            $row = mysqli_fetch_object($query);
            return json_encode($row);
        } else {
            return 'false';
        }
    }
}

