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
}

$user = new C_user($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getUser':
        $user->getUser();
        break;
    
    default:
        $data["usergroup"] = $user->getUserGroup();
        templateAdmin($conn2, '../views/v_user.php', $data, "MASTER", "MASTER USER");
        break;
}