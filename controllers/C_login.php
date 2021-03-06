<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_login.php';

class C_login
{
    public $conn = false;
    public $conn2 = false;
    public $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_login($conn, $conn2, $config);
    }

    public function login($config)
    {
        if(isset($_SESSION["USERNAME"]))  {
            if($_SESSION["USERNAME"] != '') {
                header("Location: " . $config['base_url'] . "/controllers/C_dashboard");
            }
        } else {
            template('../views/v_login.php');
        }
    }

    public function cekuser($data)
    {
        $username = $this->conn2->real_escape_string($data["username"]);
        $password = $this->conn2->real_escape_string(md5($data["password"]));
        $sql = "SELECT user_id, user_nama FROM m_user WHERE user_password = '" . $password . "' AND user_nama = '" . $username . "' and user_aktif = 'Y'";
        $quser = $this->conn2->query($sql);

        if($quser) {
            $rowuser = $quser->fetch_object();
            if($rowuser) {
                $_SESSION["USERNAME"] = $rowuser->user_nama;
                $_SESSION["USER_ID"] = $rowuser->user_id;
                $usergroup = $this->model->getRole($rowuser->user_id);
                $_SESSION["USERROLE"] = $usergroup;
                return "200";
            } else {
                return "202";
            }
        } else {
            return "202";
        }
    }

    public function cekpegawai($data)
    {
        $username = $data["username"];
        $password = date("Y-m-d", strtotime($data["password"]));
        $sql = "SELECT FS_NM_ALIAS FROM TD_PEG WHERE FS_KD_PEG = '" . $username . "' AND FD_TGL_LAHIR = '" . $password . "'";
        $stmt = sqlsrv_query($this->conn, $sql);
        $stmt = sqlsrv_fetch_object($stmt);
        if($stmt) {
            $_SESSION["USERNAME"] = $stmt->FS_NM_ALIAS;
            return "200";
        } else {
            return "202";
        }
    }
}

$login = new C_login($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'login':
        $cekuser = $login->cekuser($_POST);
        echo $cekuser;
        break;
    
    case 'logout':
        session_destroy();
        template('../views/v_login.php');
        break;
    
    default:
        $login->login($config);
        break;
}