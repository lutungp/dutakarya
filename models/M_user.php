<?php

class M_user
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

    public function getUser()
    {
        $sql = "SELECT
                    user_id,
                    user_nama,
                    user_pegawai,
                    m_usergroup_id,
                    m_usergroup.usergroup_nama,
                    CASE WHEN user_aktif = 'Y' THEN 'Aktif' ELSE 'Tidak Aktif' END AS useraktif
                FROM m_user 
                LEFT JOIN m_usergroup ON m_usergroup.usergroup_id = m_user.m_usergroup_id ";
        $quser = $this->conn2->query($sql);
        $user = [];
        while ($result = $quser->fetch_array(MYSQLI_ASSOC)) {
            array_push($user, $result);
        }
        return $user;
    }

    public function getUserGroup()
    {
        $sql = "SELECT
                    usergroup_id,
                    usergroup_kode,
                    usergroup_nama
                FROM m_usergroup WHERE usergroup_aktif = 'Y' ";
        $quser = $this->conn2->query($sql);
        $usergroup = array();
        while ($val = $quser->fetch_array()) {
            $usergroup[] = array(
                "usergroup_id" => $val["usergroup_id"],
                "usergroup_kode" => $val["usergroup_kode"],
                "usergroup_nama" => $val["usergroup_nama"]
            );
        }
        return $usergroup;
    }
    
}