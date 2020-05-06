<?php

function template($content, $data = "")
{
    $dataJadwalDokter = $data;
    $dataparse = $data;
    require_once('./../views/layouts/v_header.php');
    require_once($content);
    require_once('./../views/layouts/v_footer.php');
}

function query_create($conn2, $table, $field, $data){
    $field_values= implode(',',$field);
    $data_values=implode("','",$data);
    $sql = "INSERT INTO $table (".$field_values.") VALUES ('".$data_values."') ";
    $action = $conn2->query($sql);
    if($action) {
        return "200";
    } else {
        return "202";
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