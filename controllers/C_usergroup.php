<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_usergroup.php';

class C_usergroup
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_usergroup($conn, $conn2, $config);
    }

    public function getUserGroup()
    {
        $result = $this->model->getUserGroup();
        echo json_encode($result);
    }
}

$usergroup = new C_usergroup($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
switch ($action) {
    case 'getusergroup':
        $usergroup->getUserGroup();
        break;
    default:
        templateAdmin($conn2, '../views/v_usergroup.php', NULL, "MASTER", "MASTER USER GROUP");
        break;
}