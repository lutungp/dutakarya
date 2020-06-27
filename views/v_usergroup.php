<link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdata.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.sort.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.pager.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.edit.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/scripts/demos.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_usergroup.php?action=getusergroup";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'usergroup_id', type: 'int' },
                { name: 'usergroup_kode', type: 'string' },
                { name: 'usergroup_nama', type: 'string' },
                { name: 'usergroup_aktif', type: 'string' }
            ],
            id: 'user_id',
            url: url,
            updaterow: function (rowid, rowdata, commit) {
                // synchronize with the server - send update command
                // call commit with parameter true if the synchronization with the server is successful 
                // and with parameter false if the synchronization failder.
                commit(true);
            }
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        $("#grid").jqxGrid(
        {
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            autoheight: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            editable: true,
            selectionmode: 'multiplecellsadvanced',
            columns: [
                { text: 'Group Kode', datafield: 'usergroup_kode'},
                { text: 'Group Nama', datafield: 'usergroup_nama'},
                { text: 'Group Aktif', datafield: 'usergroup_aktif'},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        resetForm();
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#ModalUserGroup").modal('toggle');
                        $("#usergroup_id").val(dataRecord.usergroup_id);
                        $("#usergroup_kode").val(dataRecord.usergroup_kode);
                        $("#usergroup_nama").val(dataRecord.usergroup_nama);
                        getRole(dataRecord.usergroup_id);
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        
                    }
                }
            ]
        });

    });
</script>
<?php
    $data = json_decode($dataparse);
?>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addUserGroup()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
                <div class="modal fade" id="ModalUserGroup" tabindex="-1" role="dialog" aria-labelledby="ModalUserGroupLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalUserGroupLabel">Data Group</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="usergroup_id" name="usergroup_id">
                                            <label for="usergroup_nama">Group Nama</label>
                                            <input type="text" id="usergroup_nama" name="usergroup_nama" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="usergroup_kode">Group Kode</label>
                                            <input type="text" id="usergroup_kode" name="usergroup_kode" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 300px; padding: 5px !important;">
                                        <table class="table table-head-fixed text-nowrap table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="40%">Menu</th>
                                                    <th width="10%" style="text-align: center;">Create</th>
                                                    <th width="10%" style="text-align: center;">Read</th>
                                                    <th width="10%" style="text-align: center;">Update</th>
                                                    <th width="10%" style="text-align: center;">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $row = "";
                                                    $menu_lv1 = array_filter($data->menu, function ($value) { 
                                                        return $value->menu_level == 1; 
                                                    });
                                                    foreach ($menu_lv1 as $key => $val1) {
                                                        $row .= "<tr>";
                                                        $row .= "<td>".$val1->menu_nama."</td>";
                                                        $row .= "<td>
                                                                    <div class='icheck-primary d-inline'>
                                                                    <input type='checkbox' id='checkboxCreate-" . $val1->menu_id . "' menu-id='".$val1->menu_id."' role-id='0' menu-parent='".$val1->menu_parent."' role-type='create' class='checkbox'>
                                                                    <label for='checkboxCreate-" . $val1->menu_id . "'>
                                                                    </label>
                                                                    </div>
                                                                </td>";
                                                        $row .= "<td>
                                                                    <div class='icheck-primary d-inline'>
                                                                    <input type='checkbox' id='checkboxRead-" . $val1->menu_id . "' menu-id='".$val1->menu_id."' role-id='0' menu-parent='".$val1->menu_parent."' role-type='read' class='checkbox'>
                                                                    <label for='checkboxRead-" . $val1->menu_id . "'>
                                                                    </label>
                                                                    </div>
                                                                </td>";
                                                        $row .= "<td>
                                                                    <div class='icheck-primary d-inline'>
                                                                    <input type='checkbox' id='checkboxUpdate-" . $val1->menu_id . "' menu-id='".$val1->menu_id."' role-id='0' menu-parent='".$val1->menu_parent."' role-type='update' class='checkbox'>
                                                                    <label for='checkboxUpdate-" . $val1->menu_id . "'>
                                                                    </label>
                                                                    </div>
                                                                </td>";
                                                        $row .= "<td>
                                                                    <div class='icheck-primary d-inline'>
                                                                    <input type='checkbox' id='checkboxDelete-" . $val1->menu_id . "' menu-id='".$val1->menu_id."' role-id='0' menu-parent='".$val1->menu_parent."' role-type='delete' class='checkbox'>
                                                                    <label for='checkboxDelete-" . $val1->menu_id . "'>
                                                                    </label>
                                                                    </div>
                                                                </td>";
                                                        $row .= "</tr>";
                                                        $parent = $val1->menu_id;
                                                        $row2 = "";
                                                        $menu_lv2 = array_filter($data->menu, function ($val2) use ($parent) { 
                                                            return $val2->menu_level == 2 && $val2->menu_parent == $parent; 
                                                        });

                                                        foreach ($menu_lv2 as $val2) {
                                                            $row2 .= "<tr>";
                                                            $row2 .= "<td>".$val2->menu_nama."</td>";
                                                            $row2 .= "<td>
                                                                        <div class='icheck-primary d-inline'>
                                                                        <input type='checkbox' id='checkboxCreate-" . $val2->menu_id . "' menu-id='".$val2->menu_id."' role-id='0' menu-parent='".$val2->menu_parent."' role-type='create' class='checkbox'>
                                                                        <label for='checkboxCreate-" . $val2->menu_id . "'>
                                                                        </label>
                                                                        </div>
                                                                    </td>";
                                                            $row2 .= "<td>
                                                                        <div class='icheck-primary d-inline'>
                                                                        <input type='checkbox' id='checkboxRead-" . $val2->menu_id . "' menu-id='".$val2->menu_id."' role-id='0' menu-parent='".$val2->menu_parent."' role-type='read' class='checkbox'>
                                                                        <label for='checkboxRead-" . $val2->menu_id . "'>
                                                                        </label>
                                                                        </div>
                                                                    </td>";
                                                            $row2 .= "<td>
                                                                        <div class='icheck-primary d-inline'>
                                                                        <input type='checkbox' id='checkboxUpdate-" . $val2->menu_id . "' menu-id='".$val2->menu_id."' role-id='0' menu-parent='".$val2->menu_parent."' role-type='update' class='checkbox'>
                                                                        <label for='checkboxUpdate-" . $val2->menu_id . "'>
                                                                        </label>
                                                                        </div>
                                                                    </td>";
                                                            $row2 .= "<td>
                                                                        <div class='icheck-primary d-inline'>
                                                                        <input type='checkbox' id='checkboxDelete-" . $val2->menu_id . "' menu-id='".$val2->menu_id."' role-id='0' menu-parent='".$val2->menu_parent."' role-type='delete' class='checkbox'>
                                                                        <label for='checkboxDelete-" . $val2->menu_id . "'>
                                                                        </label>
                                                                        </div>
                                                                    </td>";
                                                            $row2 .= "</tr>";
                                                        }
                                                        $row .= $row2;
                                                    }
                                                    echo $row;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function addUserGroup() {
        
    }

    function submitForm() {
        var dataForm = {
            usergroup_id : $("#usergroup_id").val(),
            usergroup_nama : $("#usergroup_nama").val(),
            usergroup_kode : $("#usergroup_kode").val(),
        };

        datacheck = [];
        menucheck = $(".checkbox").each(function () {
           var data = {
               role_id : $(this).attr("role-id"),
               menu_id : $(this).attr("menu-id"),
               role : this.checked ? $(this).attr("role-type") : '',
           };
           datacheck.push(data);
        });

        var groups = {};
        for (var i = 0; i < datacheck.length; i++) {
            var groupName = datacheck[i].menu_id;
            if (!groups[groupName]) {
                groups[groupName] = [];
            }
            var roletype = '';
            switch (datacheck[i].role) {
                case 'create':
                    roletype = 'c';
                    break;
                case 'read':
                    roletype = 'r';
                    break;
                case 'update':
                    roletype = 'u';
                    break;
                case 'delete':
                    roletype = 'd';
                    break;
                default:
                    roletype = '';
                    break;
            }
            groups[groupName].push(roletype);
        }
        datacheck = [];
        for (var groupName in groups) {
            role_id = $("#checkboxCreate-"+groupName).attr('role-id');
            datacheck.push({role_id : role_id, menu_id: groupName, role: groups[groupName]});
        }
        dataForm.datacheck = datacheck;
        
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_usergroup.php?action=createusergroup",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "User group " + $("#usergroup_nama").val() + " Berhasil disimpan", "success");
                    $("#ModalUserGroup").modal('toggle');
                } else {
                    swal("Info!", "User group " + $("#usergroup_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm () {
        $("#usergroup_id").val(0);
        $("#usergroup_kode").val('');
        $("#usergroup_nama").val('');
        $('.checkbox').prop('checked', false);
        $('.checkbox').attr('role-id', 0);
    }

    function getRole(usergroup_id) {
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_usergroup.php?action=getrole",
            type: "post",
            data: {usergroup_id : usergroup_id},
            success : function (res) {
                result = JSON.parse(res);
                for (let index = 0; index < result.length; index++) {
                    const element = result[index];
                    role_priviliges = element.role_priviliges;
                    role_priviliges = role_priviliges.split(",");
                    role_id = element.role_id;
                    menu_id = element.s_menu_id;
                    role_priviliges.forEach(function (elem) {
                        var roletype = "";
                        if (elem == 'c') {
                            roletype = 'Create';
                        } else if (elem == 'r') {
                            roletype = 'Read';
                        } else if (elem == 'u') {
                            roletype = 'Update';
                        } else if (elem == 'd') {
                            roletype = 'Delete';
                        }
                        $("#checkbox"+roletype+"-"+menu_id).prop('checked', true);
                        $("[menu-id="+menu_id+"]").attr('role-id', role_id);
                    })
                }
            }
        });
    }

    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }

    $(document).ready(function () {
        var datamenu = '';
        $(".checkbox").change(function () {
            var menuid =  $(this).attr("menu-id");
            var menuparent =  $(this).attr("menu-parent");
            var roletype =  $(this).attr("role-type");
            if (menuparent == 0) {
                $("[menu-parent="+menuid+"][role-type="+roletype+"]").prop('checked', this.checked);
            }
        });
    });
</script>