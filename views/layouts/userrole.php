<?php

/*  
    @ $active1 menu aktif
    @ $_SESSION["USERROLE"] role user
*/

$USERROLE = $_SESSION["USERROLE"];
$role = array_filter($USERROLE, function($obj) use ($active2) {
    if (isset($obj["usergroup_kode"])) {
        if ($obj["menu_kode"] == md5($active2)) {
            return true;
        }
    }
});
$role = array_values($role);
$role = $role[0]["role_priviliges"];
$role = explode(',', $role);
$create = isset($role[0]) ? $role[0] : '';
$read   = isset($role[1]) ? $role[1] : '';
$update = isset($role[2]) ? $role[2] : '';
$delete = isset($role[3]) ? $role[3] : '';