<?php

class M_gajikaryawan
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

    public function getGajiKaryawan($offset, $limit, $username)
    {
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
                    gaji_created_date
                FROM t_gaji
                JOIN m_user on m_user.user_id = t_gaji.m_user_id
                WHERE m_user.user_nama = '$username'
                ORDER BY gaji_tahun DESC, gaji_view_count ASC, gaji_bulan DESC
                LIMIT $limit OFFSET $offset";
                
        $qgaji = $this->conn2->query($sql);
        $gaji = [];
        if($qgaji)
            while ($result = $qgaji->fetch_array(MYSQLI_ASSOC)) {
                array_push($gaji, $result);
            }

        return $gaji;
    }

    public function getDataGaji($gaji_id)
    {
        $sql = "SELECT 
                    gaji_id,
                    gaji_bulan,
                    gaji_tahun,
                    m_user_id,
                    gaji_nama,
                    gaji_nopeg,
                    gaji_unitpeg,
                    gaji_golpangkat_peg,
                    gaji_norekening,
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
                    gaji_diterima
                FROM t_gaji
                JOIN m_user on m_user.user_id = t_gaji.m_user_id
                WHERE t_gaji.gaji_id = '$gaji_id'";
        $qgaji = $this->conn2->query($sql);
        
        return $qgaji->fetch_object();
    }

}