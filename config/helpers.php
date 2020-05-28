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
    require_once('./../views/layouts/v_headeradmin.php');
    require_once($content);
    require_once('./../views/layouts/v_footeradmin.php');
}

function getRoleMenu($conn2, $username)
{
    $sql = "SELECT 
                s_menu.menu_id, s_menu.menu_kode, s_menu.menu_nama, s_menu.menu_level, s_menu.menu_parent,
                s_role.role_priviliges, s_menu.menu_url
            FROM s_menu 
            LEFT JOIN (
                SELECT 
                    s_role.s_menu_id, s_role.m_usergroup_id, s_role.role_priviliges
                FROM s_role WHERE role_aktif = 'Y'
            ) AS s_role ON s_role.s_menu_id = s_menu.menu_id
            JOIN m_user ON m_user.m_usergroup_id = s_role.m_usergroup_id
            WHERE menu_aktif = 'Y' 
            AND m_user.user_nama = '$username'";

    $quser = $conn2->query($sql);
    $role = array();
    while ($val = $quser->fetch_array()) {
        $role[] = array(
            "menu_id" => $val["menu_id"],
            "menu_kode" => $val["menu_kode"],
            "menu_nama" => $val["menu_nama"],
            "menu_level" => $val["menu_level"],
            "menu_parent" => $val["menu_parent"],
            "menu_url" => $val["menu_url"]
        );
    }

    return $role;
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