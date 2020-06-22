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

    public function getMenu()
    {
        $result = $this->model->getMenu();
        return $result;
    }

    public function createusergroup($data)
    {
        # code...
    }

    public function updateUserGroup($data)
    {
        $fieldSave = ["usergroup_kode", "usergroup_nama", "usergroup_updated_by", "usergroup_updated_date", "usergroup_revised"];
        $dataSave = [$data["usergroup_kode"], $data["usergroup_nama"], $_SESSION["USERNAME"], date("Y-m-d H:i:s"), "usergroup_revised+1"];
        $field = "";
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }

        $where = "WHERE usergroup_id = " . $data['usergroup_id'];
        $action = query_update($this->conn2, 'm_usergroup', $field, $where);
        if ($action) {
            $datacheck = $data["datacheck"];
            foreach ($datacheck as $key => $value) {
                $role_id = $value['role_id'];
                $role_priviliges = implode(",", $value['role']);
                if ($role_id > 0) {
                    $field = '';
                    $fieldSave = ["s_menu_id", "m_usergroup_id", "role_priviliges", "role_updated_by", "role_updated_date", "role_revised"];
                    $dataSave = [$value["menu_id"], $data['usergroup_id'], $role_priviliges, $_SESSION["USERNAME"], date("Y-m-d H:i:s"), "role_revised+1"];
                    foreach ($fieldSave as $key => $value) {
                        $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
                        if (!preg_match("/revised/i", $value)) {
                            $field .= "$value = '$dataSave[$key]'" . $regex . " ";
                        } else {
                            $field .= "$value = $dataSave[$key]" . $regex . " ";
                        }
                    }
                    $where = "WHERE role_id = " . $role_id;
                    query_update($this->conn2, 's_role', $field, $where);
                } else {
                    $fieldSave = ["s_menu_id", "m_usergroup_id", "role_priviliges", "role_created_by", "role_created_date"];
                    $dataSave = [$value["menu_id"], $data['usergroup_id'], $role_priviliges, $_SESSION["USERNAME"], date("Y-m-d H:i:s")];
                    query_create($this->conn2, 's_role', $fieldSave, $dataSave);
                }
            }
        }

        if($action){
            echo '200';
        } else {
            echo '202';
        }
    }

    public function getRole($usergroup_id)
    {
        $result = $this->model->getRole($usergroup_id);
        echo json_encode($result);
    }
}

$usergroup = new C_usergroup($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
switch ($action) {
    case 'createusergroup':
        if ($_POST["usergroup_id"] == 0) {
            $usergroup->createUserGroup($_POST);
        } else {
            $usergroup->updateUserGroup($_POST);
        }
        break;
    case 'getusergroup':
        $usergroup->getUserGroup();
        break;
    case 'getrole':
        $usergroup->getRole($_POST["usergroup_id"]);
        break;
    default:
        $data["menu"] = $usergroup->getMenu();
        templateAdmin($conn2, '../views/v_usergroup.php', json_encode($data), "MASTER", "MASTER USER GROUP");
        break;
}