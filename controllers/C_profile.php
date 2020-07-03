<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_profile.php';

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
            header("Location: " . $config['base_url'] . "./controllers/C_login");
        }

        $this->model = new M_profile($conn, $conn2, $config);
    }

    public function getProfile($username)
    {
        // $data["user"] = getUserProfile($this->conn2, $_SESSION["USERNAME"]);
        $data["pegawai"] = getPegawaiProfile($this->conn2, $_SESSION["USER_ID"]);
        templateAdmin($this->conn2, '../views/v_profile.php', $data);
    }

    public function simpanProfile($data)
    {
        $fieldSave = ["user_nama", "user_pegawai", "user_password", "user_updated_by", "user_updated_date", "user_revised"];
        $dataSave = [$data["user_nama"], $data["userpegawai"], md5($data["password"]), $_SESSION["USER_ID"], date("Y-m-d H:i:s"), "user_revised+1"];
        $where = "WHERE user_id = " . $data["user_id"];
        $_SESSION["USERNAME"] = $data["user_nama"];
        $field = "";

        /* cek nama user kembar */
        $sqlcekuser = "SELECT count(*) as cek FROM m_user WHERE m_user.user_id != " . $data["user_id"] . " AND m_user.user_nama = '" . $data["user_nama"] . "' ";
        $qcekuser = $this->conn2->query($sqlcekuser);
        if ($qcekuser) {
            $rcekuser = $qcekuser->fetch_object();
            if ($rcekuser->cek > 0) {
                echo "203";
                exit();
            }
        }

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

    public function getUnit()
    {
        $unit = $this->model->getUnit();
        echo json_encode($unit);
    }

    public function getRegional($search)
    {
        $regional = $this->model->getRegional($search);
        echo json_encode($regional);
    }

    public function getKota($search)
    {
        $regional = $this->model->getKota($search);
        echo json_encode($regional);
    }

    public function simpanPrivacy($data)
    {
        $pegawai_rtrw = isset($data["pegawai_rtrw"]) ? explode("/", $data["pegawai_rtrw"]) : null;
        $pegawai_rt = isset($pegawai_rtrw[0]) ? $pegawai_rtrw[0] : null;
        $pegawai_rw = isset($pegawai_rtrw[1]) ? $pegawai_rtrw[1] : null;
        $pegawai_tgllahir = isset($data["pegawai_tgllahir"]) ? date("Y-m-d", strtotime($data["pegawai_tgllahir"])) : null;
        $fieldSave = ["pegawai_alias", "m_unit_id", "pegawai_alamat", "pegawai_rt", "pegawai_rw", "pegawai_kode_pos", "m_kelurahan_id", 'pegawai_telp', 'pegawai_email', 
                        'pegawai_nokk', 'pegawai_noid', 'pegawai_kartubpjs', 'pegawai_tgllahir', 'm_kotalahir_id', 'pegawai_kelamin', 'pegawai_updated_by', 'pegawai_updated_date', 'pegawai_revised'];
        $dataSave = [$data['pegawai_alias'], $data['m_unit_id'], $data['pegawai_alamat'], $pegawai_rt, $pegawai_rw, $data['pegawai_kode_pos'], 
                        $data['m_kelurahan_id'], $data['pegawai_telp'], $data['pegawai_email'], $data['pegawai_nokk'], $data['pegawai_noid'], $data['pegawai_kartubpjs'], $pegawai_tgllahir, $data['m_kotalahir_id'], $data['pegawai_kelamin'],
                        $_SESSION["USER_ID"], date('Y-m-d H:i:s'), "pegawai_revised+1"];
        
        $field = '';
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        
        $where = "WHERE pegawai_id = " . $data["pegawai_id"];
        $action = query_update($this->conn2, 'm_pegawai', $field, $where);

        if($action == "1") {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function getKeluarga($pegawai_id)
    {
        $result = $this->model->getKeluarga($pegawai_id);
        echo json_encode($result);
    }

    public function submitKeluarga($data)
    {
        $index = $data["index"];
        $pegkeluarga_id = $data['pegkeluarga_id-' . $index];
        
        $pegkeluarga_kartubpjs = $data['pegkeluarga_kartubpjs-' . $index];
        $pegkeluarga_kartubpjstk = $data['pegkeluarga_kartubpjstk-' . $index];
        $pegkeluarga_nokk = $data['pegkeluarga_nokk-' . $index];
        $pegkeluarga_noid = $data['pegkeluarga_noid-' . $index];
        $m_kotalahir_id = $data['m_kotalahir_id-' . $index];
        $pegkeluarga_tgllahir = $data['pegkeluarga_tgllahir-' . $index];
        $pegkeluarga_tgllahir = date('Y-m-d', strtotime($pegkeluarga_tgllahir));
        $pegkeluarga_kelamin = $data['pegkeluarga_kelamin-' . $index];
        $pegkeluarga_nama = $data['pegkeluarga_nama-' . $index];
        $fieldSave = ["pegkeluarga_kartubpjs", "pegkeluarga_hub", "pegkeluarga_nama", "pegkeluarga_kartubpjstk", "pegkeluarga_nokk", "pegkeluarga_noid", "m_kotalahir_id", "pegkeluarga_tgllahir", "pegkeluarga_kelamin"];
        $dataSave = [$pegkeluarga_kartubpjs, $data['pegkeluarga_hub'], $pegkeluarga_nama, $pegkeluarga_kartubpjstk, $pegkeluarga_nokk, $pegkeluarga_noid, $m_kotalahir_id, $pegkeluarga_tgllahir, $pegkeluarga_kelamin];

        $field = '';
        foreach ($fieldSave as $key => $value) {
            $regex = (integer)$key < count($fieldSave)-1 ? "," : "";
            if (!preg_match("/revised/i", $value)) {
                $field .= "$value = '$dataSave[$key]'" . $regex . " ";
            } else {
                $field .= "$value = $dataSave[$key]" . $regex . " ";
            }
        }
        if ($pegkeluarga_id > 0) {
            $where = "WHERE pegkeluarga_id = " . $pegkeluarga_id;
            $action = query_update($this->conn2, 'm_pegawai_keluarga', $field, $where);
        } else {
            $fieldSave = ["m_pegawai_id", "pegkeluarga_nama", "pegkeluarga_kartubpjs", "pegkeluarga_hub", "pegkeluarga_kartubpjstk", "pegkeluarga_nokk", "pegkeluarga_noid", "m_kotalahir_id", "pegkeluarga_tgllahir", "pegkeluarga_kelamin"];
            $dataSave = [$data["m_pegawai_id-" . $index], $pegkeluarga_nama, $pegkeluarga_kartubpjs, $data['pegkeluarga_hub'], $pegkeluarga_kartubpjstk, $pegkeluarga_nokk, $pegkeluarga_noid, $m_kotalahir_id, $pegkeluarga_tgllahir, $pegkeluarga_kelamin];
            $action = query_create($this->conn2, 'm_pegawai_keluarga', $fieldSave, $dataSave);
            echo $action;
            exit();
        }
        
        if($action <> "0") {
            echo "200";
        } else {
            echo "202";
        }
    }

    public function uploadPhoto($data)
    {
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf' , 'doc' , 'ppt'); // valid extensions
        $path = '../assets/pegawai/' . $_SESSION["USER_ID"] . '/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $action = false;
        if(!empty($_POST['photoprofile']) || !empty($_POST['email']) || $_FILES['photoprofile']) {
            $img = $_FILES['photoprofile']['name'];
            $tmp = $_FILES['photoprofile']['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
            $final_image = "profile-" . $img;

            if(in_array($ext, $valid_extensions)){
                $path = $path.strtolower($final_image);
                if(move_uploaded_file($tmp, $path)) {
                    $pathsave = '/assets/pegawai/' . $_SESSION["USER_ID"] . '/' . strtolower($final_image);
                    $sql = "UPDATE m_pegawai SET pegawai_photo = '$pathsave' WHERE pegawai_id = " . $data['pegawai_id'];
                    $action = $this->conn2->query($sql);
                }
            }
        }

        if ($action) {
            echo '200';
        } else {
            echo '202';
        }
    }
}

$username = $_SESSION["USERNAME"];
$profile = new C_profile($conn, $conn2, $username);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'submit':
        $profile->simpanProfile($_POST);
        break;
    case 'getunit':
        $profile->getUnit();
        break;
    case 'getregional':
        $search = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
        $profile->getRegional($search);
        break;
    case 'getkota':
        $search = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
        $profile->getKota($search);
        break;
    case 'submitprivacy':
        $profile->simpanPrivacy($_POST);
        break;
    case 'getkeluarga':
        $profile->getKeluarga($_POST["pegawai_id"]);
        break;
    case 'uploadphoto':
        $profile->uploadPhoto($_POST);
        break;
    case 'submitkeluarga':
        $profile->submitKeluarga($_POST);
        break;
    default:
        $profile->getProfile($username);
    break;
}
