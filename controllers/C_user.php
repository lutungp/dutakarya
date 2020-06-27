<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_user.php';

class C_user
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_user($conn, $conn2, $config);
    }

    public function getUser()
    {
        $result = $this->model->getUser();
        echo json_encode($result);
    }

    public function getUserGroup()
    {
        return $this->model->getUserGroup();
    }

    public function createUser($data)
    {
        $fieldSave = ["user_nama", "user_password", "m_usergroup_id", "user_created_by", "user_created_date", "user_revised"];
        $password = md5($data["user_password"]);
        $dataSave = [$data["user_nama"], $password, $data["m_usergroup_id"], $_SESSION["USERNAME"], date("Y-m-d H:i:s"), 0];
        $action = query_create($this->conn2, 'm_user', $fieldSave, $dataSave);
        if ($action > 0) {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function updateUser($data)
    {
        $fieldSave = ["user_nama", "user_password", "m_usergroup_id", "user_updated_by", "user_updated_date", "user_revised"];
        $password = md5($data["user_password"]);
        $dataSave = [$data["user_nama"], $password, $data["m_usergroup_id"], $_SESSION["USERNAME"], date("Y-m-d H:i:s"), "user_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        $where = "WHERE user_id = " . $data['user_id'];
        $action = query_update($this->conn2, 'm_user', $field, $where);

        if ($action) {
            echo "200";
        } else {
            echo "202";
        }
    }
}

$user = new C_user($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getUser':
        $user->getUser();
        break;
    
    case 'createuser':
        if($_POST["user_id"] == 0) {
            $user->createUser($_POST);
        } else {
            $user->updateUser($_POST);
        }
        
        break;
    
    default:
        $data["usergroup"] = $user->getUserGroup();
        templateAdmin($conn2, '../views/v_user.php', $data, "MASTER", "MASTER USER");
        break;
}