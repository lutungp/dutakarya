<?php

include '../config/database.php';
include '../config/helpers.php';
include '../config/config.php';

class C_login
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
    }

    public function login($config)
    {
        if(isset($_SESSION["USERNAME"]))  {
            if($_SESSION["USERNAME"] != '') {
                header("Location: " . $config['base_url'] . "/controllers/C_dashboard.php");
            }
        } else {
            template('../views/v_login.php');
        }
    }

    public function cekuser($data)
    {
        $username = $data["username"];
        $password = md5($data["password"]);
        $sql = "SELECT user_nama FROM m_user WHERE user_password = '" . $password . "' AND user_nama = '" . $username . "' and user_aktif = 'Y'";
        $quser = $this->conn2->query($sql);

        if($quser) {
            $rowuser = $quser->fetch_object();
            if($rowuser) {
                $_SESSION["USERNAME"] = $rowuser->user_nama;
                echo "200";
            } else {
                echo "202";
            }
        } else {
            echo "202";
        }
    }
}

$login = new C_login($conn, $conn2);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'login':
        $login->cekuser($_POST);
        break;
    
    default:
        $login->login($config);
        break;
}