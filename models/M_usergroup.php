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

    public function getMenu()
    {
        $sql = "SELECT menu_id, menu_urutan, menu_nama, menu_level, menu_parent FROM s_menu WHERE menu_aktif = 'Y' ORDER BY menu_level, menu_urutan";
        $quser = $this->conn2->query($sql);
        $menu = array();
        while ($val = $quser->fetch_array()) {
            $menu[] = array(
                "menu_id" => $val["menu_id"],
                "menu_urutan" => $val["menu_urutan"],
                "menu_nama" => $val["menu_nama"],
                "menu_level" => $val["menu_level"],
                "menu_parent" => $val["menu_parent"]
            );
        }
        return $menu;
    }

    public function getRole($usergroup_id)
    {
        $sql = "SELECT
                    role_id,
                    s_menu_id,
                    m_usergroup_id,
                    role_priviliges
                FROM s_role WHERE role_aktif = 'Y' AND m_usergroup_id = $usergroup_id";
        $quser = $this->conn2->query($sql);
        $menu = array();
        while ($val = $quser->fetch_array()) {
            $menu[] = array(
                "role_id" => $val["role_id"],
                "s_menu_id" => $val["s_menu_id"],
                "m_usergroup_id" => $val["m_usergroup_id"],
                "role_priviliges" => $val["role_priviliges"]
            );
        }
        return $menu;
    }

}