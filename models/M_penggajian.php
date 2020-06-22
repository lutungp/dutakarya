<?php

class M_penggajian
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

    public function getDataPenggajian($bulan, $tahun)
    {
        $bulanArr = array(
            "JANUARI" => 1,
            "FEBRUARI" => 2,
            "MARET" => 3,
            "APRIL" => 4,
            "MEI" => 5,
            "JUNI" => 6,
            "JULI" => 7,
            "AGUSTUS" => 8,
            "SEPTEMBER" => 9,
            "OKTOBER" => 10,
            "NOVEMBER" => 11,
            "DESEMBER" => 12
        );
        
        $sql = "SELECT 
                    gaji_id,
                    gaji_bulan,
                    gaji_tahun,
                    m_user_id,
                    gaji_pokok,
                    gaji_kehadiran,
                    gaji_transport,
                    gaji_operasional,
                    gaji_rapel,
                    gaji_jasalayanan,
                    gaji_shift,
                    gaji_lembur,
                    gaji_oncall,
                    gaji_uangcuti,
                    gaji_potsimwa,
                    gaji_potkoperasi,
                    gaji_potparkir,
                    gaji_potsimpok,
                    gaji_potkesehatan,
                    gaji_potabsensi,
                    gaji_potzis,
                    gaji_potqurban,
                    gaji_potinfaqmasjid,
                    gaji_potbpjstk,
                    gaji_potbpjspensiun,
                    gaji_potpajak,
                    gaji_potsekolah,
                    gaji_potlain,
                    gaji_potfkk,
                    gaji_potsp,
                    gaji_potibi,
                    gaji_nilai,
                    gaji_insentif,
                    gaji_potongan,
                    gaji_diterima,
                    gaji_view_count,
                    gaji_created_date,
                    gaji_nama,
                    gaji_nopeg,
                    gaji_unitpeg,
                    gaji_golpangkat_peg,
                    gaji_norekening
                FROM t_gaji
                JOIN m_user on m_user.user_id = t_gaji.m_user_id
                WHERE t_gaji.gaji_tahun = '$tahun' AND t_gaji.gaji_bulan = '" . $bulanArr[$bulan] . "'
                ORDER BY gaji_tahun DESC";
                
        $qgaji = $this->conn2->query($sql);
        $gaji = [];
        if($qgaji)
            while ($result = $qgaji->fetch_array(MYSQLI_ASSOC)) {
                array_push($gaji, $result);
            }
        return $gaji;
    }

    public function validasiGaji($bulan, $tahun)
    {
        $sql = "SELECT count(*) as count FROM t_gaji WHERE gaji_bulan = $bulan AND gaji_tahun = $tahun";
        $qgaji = $this->conn2->query($sql);
        if ($qgaji) {
            $rgaji = $qgaji->fetch_object();
            if ($rgaji->count > 0) {
                return "sudah ada";
            } else {
                return "belum ada";
            }
        } else {
            return "belum ada";
        }
        
    }
}
    