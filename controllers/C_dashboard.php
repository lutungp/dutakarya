<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';

class C_dashboard
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;

    private $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;

        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "/controllers/C_login.php");
        }
    }

    public function getTimeLine($username)
    {
        $bulan = (integer)date("m");
        $tahun = (integer)date("Y");
        $sqlusergroup = "SELECT 
                            usergroup_kode, usergroup_nama 
                         FROM m_usergroup
                         JOIN m_user ON m_user.m_usergroup_id = m_usergroup.usergroup_id
                         WHERE m_user.user_nama = '$username'";
                         
        $qusergroup = $this->conn2->query($sqlusergroup);
        $rusergroup = $qusergroup->fetch_object();

        $sql = "SELECT
                    t_gaji.gaji_bulan AS bulan,
                    t_gaji.gaji_tahun AS tahun,
                    Date(t_gaji.gaji_created_date) AS tanggal,
                    t_gaji.gaji_created_date AS created_date,
                    '' AS status,
                    'Pemberitahuan Gaji' AS title,
                    CONCAT('A-', gaji_id, '-', gaji_view_count) AS readmore,
                    '' AS deskripsi
                FROM t_gaji
                JOIN m_user ON m_user.user_id = t_gaji.m_user_id
                WHERE m_user.user_nama = '$username'";

        if ($rusergroup->usergroup_kode == "ADMINISTRATOR" || $rusergroup->usergroup_kode == "TELEMEDICINE") {
            $sql .= " UNION ";
            $sql .= "SELECT
                        MONTH(t_booking_hospital.bookinghosp_tanggal) AS bulan,
                        YEAR(t_booking_hospital.bookinghosp_created_date) AS tahun,
                        Date(t_booking_hospital.bookinghosp_created_date) AS tanggal,
                        t_booking_hospital.bookinghosp_created_date AS created_date,
                        t_booking_hospital.bookinghosp_status AS status,
                        'Booking Telemedicine' AS title,
                        'B' AS readmore,
                        json_object('pasien_nama', t_booking_hospital.pasien_nama, 
                                    'pasien_norm', t_booking_hospital.pasien_norm,
                                    'pegawai_kode', t_booking_hospital.pegawai_kode,
                                    'pegawai_nama', t_booking_hospital.pegawai_nama,
                                    'bookinghosp_tanggal', t_booking_hospital.bookinghosp_tanggal,
                                    'bookinghosp_jammulai', t_booking_hospital.bookinghosp_jammulai,
                                    'bookinghosp_jamselesai', t_booking_hospital.bookinghosp_jamselesai,
                                    'layanan_kode', t_booking_hospital.layanan_kode) AS deskripsi
                    FROM t_booking_hospital
                    WHERE bookinghosp_aktif = 'Y'";

                $sql .= " UNION ";
                $sql .= "SELECT
                            MONTH(t_tagihan_pasien.tagihanpasien_dibayar_tgl) AS bulan,
                            YEAR(t_tagihan_pasien.tagihanpasien_dibayar_tgl) AS tahun,
                            t_tagihan_pasien.tagihanpasien_dibayar_tgl AS tanggal,
                            t_tagihan_pasien.tagihanpasien_dibayar_tgl AS created_date,
                            '' AS status,
                            'Pembayaran Pasien Telemedicine' AS title,
                            'B' AS readmore,
                            json_object('pasien_nama', t_booking_hospital.pasien_nama, 
                                    'pasien_norm', t_booking_hospital.pasien_norm,
                                    'pegawai_kode', t_booking_hospital.pegawai_kode,
                                    'pegawai_nama', t_booking_hospital.pegawai_nama,
                                    'bookinghosp_tanggal', t_booking_hospital.bookinghosp_tanggal,
                                    'bookinghosp_jammulai', t_booking_hospital.bookinghosp_jammulai,
                                    'bookinghosp_jamselesai', t_booking_hospital.bookinghosp_jamselesai,
                                    'layanan_kode', t_booking_hospital.layanan_kode,
                                    'tagihan_pasien_total', t_tagihan_pasien.tagihanpasien_total,
                                    'user_pegawai', m_user.user_pegawai) AS deskripsi
                         FROM t_tagihan_pasien
                         JOIN t_booking_hospital ON t_booking_hospital.bookinghosp_id = t_tagihan_pasien.t_bookinghosp_id
                         JOIN m_user ON m_user.user_nama = t_tagihan_pasien.tagihanpasien_dibayar_petugas
                         WHERE t_tagihan_pasien.tagihanpasien_aktif = 'Y' AND t_booking_hospital.bookinghosp_aktif = 'Y'
                         AND t_tagihan_pasien.tagihanpasien_dibayar = 'SUDAH' ";
        }
        $bulan2 = $bulan+1;
        $sql = "SELECT * FROM ($sql) AS timeline 
                WHERE (bulan = $bulan OR bulan = $bulan2) AND tahun = '$tahun' 
                ORDER BY tanggal DESC";
                
        $qtimeline = $this->conn2->query($sql);
        $data = [];
        if ($qtimeline) {
            $bulan = ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            while ($val = $qtimeline->fetch_array()) {
                $description = "";
                if ($val["title"] == "Pemberitahuan Gaji") {
                    $description = "Gaji Bulan " . $bulan[$val["bulan"]] . ", Tahun : " . $val["tahun"];
                } else {
                    $description = $val["deskripsi"];
                }
                
                $data[] = array(
                    "bulan" => $val["bulan"],
                    "tahun" => $val["tahun"],
                    "status" => $val["status"],
                    "title" => $val["title"],
                    "tanggal" => $val["tanggal"],
                    "created_date" => $val["created_date"],
                    "description"  => $description,
                    "readmore"  => $val["readmore"]
                );
            }
        }

        echo json_encode($data);
    }
}

$dashboard = new C_dashboard($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {   
    case "timeline":
        $dashboard->getTimeLine($_SESSION["USERNAME"]);
        break;
    default:
        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "/controllers/C_login");
        } else {
            templateAdmin($conn2, '../views/v_dashboard.php'); 
        }
        break;
}
