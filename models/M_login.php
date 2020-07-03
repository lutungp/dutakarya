<?php

class M_login
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

    public function getRole($user_id)
    {
        $sql = "SELECT
                    usergroup_kode,
                    s_role.s_menu_id,
                    s_menu.menu_kode,
                    s_menu.menu_nama,
                    s_role.role_priviliges
                FROM
                    m_usergroup
                LEFT JOIN s_role ON s_role.m_usergroup_id = m_usergroup.usergroup_id
                LEFT JOIN s_menu ON s_menu.menu_id = s_role.s_menu_id
                LEFT JOIN m_user ON m_user.m_usergroup_id = m_usergroup.usergroup_id
                WHERE
                    m_usergroup.usergroup_aktif = 'Y'
                AND s_role.role_aktif = 'Y' AND m_user.user_id = $user_id";

        $qrole = $this->conn2->query($sql);
        $rrole = array();
        while ($val = $qrole->fetch_array()) {
            $rrole[] = array(
                "usergroup_kode" => md5($val['usergroup_kode']),
                "s_menu_id" => md5($val['s_menu_id']),
                "menu_nama" => md5($val['menu_nama']),
                "menu_kode" => md5($val['menu_kode']),
                "role_priviliges" => $val['role_priviliges'],
            );
        }

        return $rrole;
    }

}