<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';

class C_profile
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;

    private $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;

        if(!isset($_SESSION["USERNAME"])) {
            header("Location: " . $config['base_url'] . "./controllers/C_login.php");
        }
    }

    public function getProfile($username)
    {
        $user = getUserProfile($this->conn2, $_SESSION["USERNAME"]);
        templateAdmin($this->conn2, '../views/v_profile.php', $user);
    }

    public function simpaProfile($data)
    {
        $fieldSave = ["user_nama", "user_password", "user_updated_by", "user_updated_date", "user_revised"];
        $dataSave = [$data["usernama"], md5($data["password"]), $_SESSION["USERNAME"], date("Y-m-d H:i:s"), "user_revised+1"];
        $where = "WHERE user_id = " . $data["user_id"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $action = query_update($this->conn2, 'm_user', $field, $where);
        if($action == "1") {
            echo "200";
        } else {
            echo "202";
        }
    }
}

$username = $_SESSION["USERNAME"];
$profile = new C_profile($conn, $conn2, $username);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'submit':
        $profile->simpaProfile($_POST);
        break;
    default:
        $profile->getProfile($username);
    break;
}
