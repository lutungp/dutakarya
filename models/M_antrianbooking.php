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
                    t_booking_hospital.pasien_norm, pasien_nama, pasien_telp, 
                    bookinghosp_urutan, pasien_email, bookinghosp_tanggal,
                    layanan_kode, bookinghosp_status,
                    tagihan.total
                FROM t_booking_hospital 
                LEFT JOIN (
                    SELECT pasien_norm, SUM(t_tagihan_pasien.tagihanpasien_total) AS total FROM t_tagihan_pasien WHERE tagihanpasien_aktif = 'Y' GROUP BY pasien_norm
                ) tagihan ON  tagihan.pasien_norm = t_booking_hospital.pasien_norm
                WHERE bookinghosp_aktif = 'Y' 
                AND bookinghosp_status = 'ANTRIAN' 
                AND t_booking_hospital.pasien_norm = '$pasien_norm'";
        
        $query = $this->conn2->query($tsql);
        if($query){
            $row = mysqli_fetch_object($query);
            return json_encode($row);
        } else {
            return 'false';
        }
    }

    public function getTagihan($pasien_norm)
    {
        $sql = "SELECT SUM(t_tagihan_pasien.tagihanpasien_total) FROM t_tagihan_pasien.tagihanpasien_total WHERE pasien_norm = '" . $pasien_norm . " AND tagihanpasien_aktif = 'Y'";
        $query = $this->conn2->query($tsql);
        if($query){
            $row = mysqli_fetch_object($query);
            return json_encode($row);
        } else {
            return 'false';
        }
    }
}

