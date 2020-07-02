<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_user.php?action=getUser";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'user_id', type: 'int' },
                { name: 'user_nama', type: 'string' },
                { name: 'user_pegawai', type: 'string' },
                { name: 'm_usergroup_id', type: 'int' },
                { name: 'usergroup_nama', type: 'string' },
                { name: 'useraktif', type: 'string' },
                { name: 'm_pegawai_id', type: 'string' },
                { name: 'pegawai_nama', type: 'string' },
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
        var addfilter = function () {
            var filtergroup = new $.jqx.filter();
            var filter_or_operator = 1;
            var filtervalue = '';
            var filtercondition = 'contains';
            var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            filtervalue = '';
            filtercondition = 'starts_with';
            var filter2 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);

            filtergroup.addfilter(filter_or_operator, filter1);
            filtergroup.addfilter(filter_or_operator, filter2);
            // add the filters.
            $("#grid").jqxGrid('addfilter', 'user_nama', filtergroup);
            // // apply the filters.
            // $("#grid").jqxGrid('applyfilters');
        }
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        $("#grid").jqxGrid({
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            autoheight: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            selectionmode: 'multiplecellsadvanced',
            filterable: true,
            ready: function () {
                addfilter();
            },
            autoshowfiltericon: true,
            columns: [
                { text: 'User Nama', datafield: 'user_nama'},
                // { text: 'Nama Pegawai', datafield: 'user_pegawai'},
                { text: 'User Group', datafield: 'usergroup_nama'},
                { text: 'User Aktif', datafield: 'useraktif', filterable: false},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#ModalUser").modal('toggle');
                        $("#user_id").val(dataRecord.user_id);
                        $("#user_nama").val(dataRecord.user_nama);
                        $("#m_usergroup_id").val(dataRecord.m_usergroup_id);
                        $("#user_password").val('');
                        $("#m_pegawai_id").data('select2').trigger('select', {
                            data: {"id": dataRecord.m_pegawai_id, "text": dataRecord.pegawai_nama }
                        });
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        
                    }
                }
            ]
        });

        $("#m_pegawai_id").select2({
          ajax: {
            url: '<?php echo BASE_URL ?>/controllers/C_user.php?action=getpegawai',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                searchTerm: params.term // search term
              };
            },
            processResults: function (response) {
              return {
                results: response
              };
            },
          cache: true
          }
        });
    });
</script>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="adduser()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
                <div class="modal fade" id="ModalUser" tabindex="-1" role="dialog" aria-labelledby="ModalUserLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalUserLabel">Data User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="user_id" name="user_id">
                                            <label for="user_nama">User Nama</label>
                                            <input type="text" id="user_nama" name="user_nama" class="form-control">
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="user_nama">Pegawai</label>
                                            <select id="m_pegawai_id" name="m_pegawai_id" style="width: 100%;"></select>
                                        </div> -->
                                        <div class="form-group">
                                            <label for="user_nama">User Group</label>
                                            <select id="m_usergroup_id" class="form-control" style="width: 100%;">
                                                <?php
                                                    foreach($dataparse["usergroup"] as $val){
                                                        echo "<option value='" . $val["usergroup_id"] . "'>" . $val["usergroup_nama"] . "</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="user_password">Password</label>
                                            <input type="password" id="user_password" name="user_password" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="user_password">Password Konfirmasi</label>
                                            <input type="password" id="user_password2" name="user_password2" class="form-control">
                                        </div>
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
    $(document).ready(function (params) {
        $("#user_password2").on("change", function (params) {
            var password1 = $("#user_password").val();
            var password2 = $("#user_password2").val();

            if (password1 !== password2) {
                $("#user_password").addClass("is-invalid");
                $("#user_password").removeClass("is-valid");
                $("#user_password2").addClass("is-invalid");
                $("#user_password2").removeClass("is-valid");
            } else {
                $("#user_password").removeClass("is-invalid");
                $("#user_password").addClass("is-valid");
                $("#user_password2").removeClass("is-invalid");
                $("#user_password2").addClass("is-valid");
            }
        })
    });

    function submitForm() {
        var password1 = $("#user_password").val();
        var password2 = $("#user_password2").val();

        if (password1 == '' || password2 == '') {
            $("#user_password").addClass("is-invalid");
            $("#user_password").removeClass("is-valid");
            $("#user_password2").addClass("is-invalid");
            $("#user_password2").removeClass("is-valid");

            return false;
        } else if (password1 !== password2) {
            $("#user_password").addClass("is-invalid");
            $("#user_password").removeClass("is-valid");
            $("#user_password2").addClass("is-invalid");
            $("#user_password2").removeClass("is-valid");

            return false;
        } else {
            $("#user_password").removeClass("is-invalid");
            $("#user_password").addClass("is-valid");
            $("#user_password2").removeClass("is-invalid");
            $("#user_password2").addClass("is-valid");
        }

        
        
        var dataForm = {
            user_id : $("#user_id").val(),
            user_nama : $("#user_nama").val(),
            m_usergroup_id : $("#m_usergroup_id").val(),
            user_password : $("#user_password").val(),
            user_password2 : $("#user_password2").val(),
            m_pegawai_id : $("#m_pegawai_id").val(), 
        };

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_user.php?action=createuser",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "User " + $("#user_nama").val() + " Berhasil disimpan", "success");
                    $("#ModalUser").modal('toggle');
                    $("#user_password").removeClass("is-valid");
                    $("#user_password2").removeClass("is-valid");
                } else {
                    swal("Info!", "User " + $("#user_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#user_id").val(0);
        $("#user_nama").val('');
        $("#m_usergroup_id").val(0);
        $("#user_password").val('');
        $("#user_password2").val('');
    }

    function adduser() {
        resetForm();
        $("#ModalUser").modal('toggle');
    }
</script>