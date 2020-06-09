<?php

class M_usergroup
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

    public function getUserGroup()
    {
        $sql = "SELECT
                    usergroup_id,
                    usergroup_kode,
                    usergroup_nama,
                    CASE WHEN usergroup_aktif = 'Y' THEN 'Aktif' ELSE 'Tidak Aktif' END AS usergroup_aktif
                FROM m_usergroup ";
        $quser = $this->conn2->query($sql);
        $usergroup = array();
        while ($val = $quser->fetch_array()) {
            $usergroup[] = array(
                "usergroup_id" => $val["usergroup_id"],
                "usergroup_kode" => $val["usergroup_kode"],
                "usergroup_nama" => $val["usergroup_nama"],
                "usergroup_aktif" => $val["usergroup_aktif"],
            );
        }
        return $usergroup;
    }

}