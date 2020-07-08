<?php

function template($content, $data = "")
{
    $dataJadwalDokter = $data;
    $dataparse = $data;
    require_once('./../views/layouts/v_header.php');
    require_once($content);
    require_once('./../views/layouts/v_footer.php');
}

function templateAdmin($conn2, $content, $data = "", $active1 = "", $active2 = "")
{
    if(!isset($_SESSION["USERNAME"])) {
        header("Location: " . $config['base_url'] . "./controllers/C_login.php");
    }
    $dataparse = $data;
    $active1 = $active1;
    $active2 = $active2;
    $rolemenu = getRoleMenu($conn2, $_SESSION["USERNAME"]);
    $user = getUserProfile($conn2, $_SESSION["USERNAME"]);
    require_once('./../views/layouts/v_headeradmin.php');
    require_once($content);
    require_once('./../views/layouts/v_footeradmin.php');
}

function getRoleMenu($conn2, $username)
{
    $sql = "SELECT 
                s_menu.menu_id, s_menu.menu_kode, s_menu.menu_nama, s_menu.menu_level, s_menu.menu_parent,
                s_role.role_priviliges, s_menu.menu_url, s_menu.menu_icon
            FROM s_menu 
            JOIN (
                SELECT 
                    s_role.s_menu_id, s_role.m_usergroup_id, s_role.role_priviliges
                FROM s_role WHERE role_aktif = 'Y' AND (s_role.role_priviliges!='' AND role_priviliges IS NOT NULL)
            ) AS s_role ON s_role.s_menu_id = s_menu.menu_id
            JOIN m_user ON m_user.m_usergroup_id = s_role.m_usergroup_id
            WHERE menu_aktif = 'Y' 
            AND m_user.user_nama = '$username'
            ORDER BY menu_level ASC, menu_urutan ASC";
    
    $quser = $conn2->query($sql);
    $role = array();
    while ($val = $quser->fetch_array()) {
        $role_priviliges = explode(",", $val['role_priviliges']);
        $role_priviliges = array_filter($role_priviliges, function ($var) {
                            return $var != '' && $var != null;
                        });
        if (count($role_priviliges) > 0) {
            $role[] = array(
                "menu_id" => $val["menu_id"],
                "menu_kode" => $val["menu_kode"],
                "menu_nama" => $val["menu_nama"],
                "menu_level" => $val["menu_level"],
                "menu_parent" => $val["menu_parent"],
                "menu_url" => $val["menu_url"],
                "menu_icon" => $val["menu_icon"]
            );
        }
    }

    return $role;
}

function getUserProfile($conn2, $username)
{
    $sql = "SELECT user_id, user_nama, user_pegawai, user_nopegawai, user_nopegawai FROM m_user WHERE m_user.user_aktif = 'Y' AND m_user.user_nama = '$username'";
    $action = $conn2->query($sql);
    return $action->fetch_object();
}

function getPegawaiProfile($conn2, $user_id)
{
    $sql = "SELECT 
                m_pegawai.pegawai_id,
                m_pegawai.pegawai_photo,
                m_pegawai.pegawai_kode,
                m_pegawai.pegawai_alias,
                m_pegawai.pegawai_nama,
                m_pegawai.pegawai_alamat,
                m_pegawai.pegawai_rt,
                m_pegawai.pegawai_rw,
                m_pegawai.pegawai_kode_pos,
                m_pegawai.m_kota_id,
                m_pegawai.m_kelurahan_id,
                m_pegawai.pegawai_telp,
                m_pegawai.pegawai_hp,
                m_pegawai.pegawai_nokk,
                m_pegawai.pegawai_noid,
                m_pegawai.pegawai_kartubpjs,
                m_pegawai.m_kotalahir_id,
                kotalahir.name AS kotalahir_nama,
                m_pegawai.pegawai_tgllahir,
                m_pegawai.pegawai_kelamin,
                m_pegawai.pegawai_stskawin,
                m_pegawai.pegawai_anak,
                m_pegawai.m_pegawaists_id,
                m_pegawai.m_pegawaijns_id,
                m_pegawai.m_unit_id,
                m_pegawai.m_pegawaigol_id,
                m_pegawai.m_pegawaibag_id,
                m_pegawai.m_pegawaijbt_id,
                m_pegawai.m_pegawaipangkat_id,
                m_pegawai.m_satuanmedis_id,
                m_pegawai.m_lokasi_id,
                m_pegawai.pegawai_noijin,
                m_pegawai.pegawai_tglijin,
                m_pegawai.pegawai_email,
                m_pegawaijbt.pegawaijbt_nama,
                m_unit.unit_nama,
                alamatprovinsi.name as alamatprovinsi,
                alamatkota.name as alamatkota,
                alamatkelurahan.name as alamatkelurahan,
                alamatkecamatan.name as alamatkecamatan,
                CONCAT(alamatkelurahan.name, ', ', alamatkecamatan.name, ', ', alamatkota.name, ', ', alamatprovinsi.name) AS regional
            FROM m_pegawai
            JOIN m_user ON m_user.m_pegawai_id = m_pegawai.pegawai_id
            LEFT JOIN m_pegawaijbt ON m_pegawaijbt.pegawaijbt_id = m_pegawai.m_pegawaijbt_id
            LEFT JOIN m_unit ON m_unit.unit_id = m_pegawai.m_unit_id
            LEFT JOIN reg_regencies alamatkota ON alamatkota.id = m_pegawai.m_kota_id
            LEFT JOIN reg_villages alamatkelurahan ON alamatkelurahan.id = m_pegawai.m_kelurahan_id
            LEFT JOIN reg_districts alamatkecamatan ON alamatkecamatan.id = alamatkelurahan.district_id
            LEFT JOIN reg_provinces alamatprovinsi ON alamatprovinsi.id = alamatkota.province_id
            LEFT JOIN reg_regencies kotalahir ON kotalahir.id = m_pegawai.m_kotalahir_id
            WHERE m_user.user_aktif = 'Y' AND m_user.user_id = '$user_id'";
    $action = $conn2->query($sql);
    return $action->fetch_object();
}

function query_create($conn2, $table, $field, $data){
    $field_values= implode(',',$field);
    $data_values=implode("','",$data);
    $sql = "INSERT INTO $table (".$field_values.") VALUES ('".$data_values."') ";
    $action = $conn2->query($sql);
    $lastid = $conn2->insert_id;
    if($action) {
        return $lastid;
    } else {
        return "0";
    }
}

function query_update($conn2, $table, $field, $where){
    $sql = "UPDATE $table SET $field $where";
    $action = $conn2->query($sql);
    if($action) {
        return "1";
    } else {
        return "0";
    }
}

function hariIndo($tanggal){
    $hariIndo = ["SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU", "MINGGU"];
    $harinomor =  date('N', strtotime($tanggal));
    return $hariIndo[$harinomor-1];
}

function getWeekDaysFromDate($tanggal)
{
    $week = date("W", strtotime($tanggal)); // get week
    $y =    date("Y", strtotime($tanggal)); // get year
    $first_date =  date('d-m-Y',strtotime($y."W".$week));
    $dayofweek = [];
    for ($i=0; $i < 7; $i++) {
        $tanggal = date("d-m-Y",strtotime("+" . $i . " day", strtotime($first_date)));
        $dayofweek[] = array(
            "tanggal" => $tanggal,
            "hari"    => hariIndo($tanggal)
        );
    }

    return $dayofweek;
}

function getPenomoran($conn2, $kode, $table, $id, $no, $tanggal)
{
    $sql = "SELECT SUBSTRING_INDEX(TRIM($no), '/', -1) AS last  FROM $table ORDER BY $id DESC LIMIT 1 ";
    $action = $conn2->query($sql);
    $nourut = 1;
    if ($action) {
        $row = $action->fetch_object();
        $nourut = intval($row->last) + 1;
    }
    $bulan = date('m', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));
    $nourut = str_pad(($nourut + 1), 4, "0", STR_PAD_LEFT );
    $notransaksi = $kode . '/' . $bulan . '/' . $tahun . '/' . $nourut;

    return $notransaksi;
}