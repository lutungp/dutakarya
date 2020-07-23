<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>
<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=getpegawai";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'pegawai_id', type: 'int' },
                { name: 'pegawai_nama', type: 'string' },
                { name: 'pegawai_notelp', type: 'string' },
                { name: 'pegawai_bagian', type: 'string' },
                { name: 'pegawai_alamat', type: 'string' },
                { name: 'pegawaiaktif', type: 'string' }
            ],
            id: 'pegawai_id',
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
            $("#grid").jqxGrid('addfilter', 'pegawai_nama', filtergroup);
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
                { text: 'Pegawai Nama', datafield: 'pegawai_nama'},
                { text: 'Pegawai Bagian', datafield: 'pegawai_bagian'},
                { text: 'No. Telp', datafield: 'pegawai_notelp'},
                { text: 'Pegawai Aktif', datafield: 'pegawaiaktif', filterable: false},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#Modalpegawai").modal('toggle');
                        $("#pegawai_id").val(dataRecord.pegawai_id);
                        $("#pegawai_nama").val(dataRecord.pegawai_nama);
                        $("#pegawai_bagian").val(dataRecord.pegawai_bagian);
                        $("#pegawai_notelp").val(dataRecord.pegawai_notelp);
                        $("#pegawai_alamat").val(dataRecord.pegawai_alamat);
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        swal({
                            title: "Hapus pegawai " + dataRecord.pegawai_nama,
                            text: "Alasan dihapus :",
                            type: "input",
                            showCancelButton: true,
                            closeOnConfirm: false,
                        }, function (inputValue) {
                            if (inputValue === false) return false;
                            if (inputValue === "") {
                                swal.showInputError("Tuliskan alasan anda !");
                                return false
                            }
                            $.ajax({
                                url: "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=deletepegawai",
                                type: "post",
                                data: { pegawai_id : dataRecord.pegawai_id,  alasan : inputValue},
                                success : function (res) {
                                    $("#grid").jqxGrid('updatebounddata');
                                    if (res == 200) {
                                        swal("Info!", "Pegawai " + $("#pegawai_nama").val() + " Berhasil dihapus", "success");
                                    } else {
                                        swal("Info!", "Pegawai " + $("#pegawai_nama").val() + " Gagal dihapus", "error");
                                    }
                                }
                            });
                        });
                    }
                }
            ]
        });

        $("#m_pegawai_id").select2({
          ajax: {
            url: '<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=getpegawai',
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
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addpegawai()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
                <div class="modal fade" id="Modalpegawai" tabindex="-1" role="dialog" aria-labelledby="ModalpegawaiLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalpegawaiLabel">Data pegawai</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="pegawai_id" name="pegawai_id">
                                            <label for="pegawai_nama">Nama</label>
                                            <input type="text" id="pegawai_nama" name="pegawai_nama" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_bagian">Bagian</label>
                                            <select id="pegawai_bagian" class="select2" style="width: 100%;">
                                                <option value="admin">Admin</option>
                                                <option value="driver">Driver</option>
                                                <option value="helper">Helper</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_notelp">No. Telp</label>
                                            <input type="text" id="pegawai_notelp" name="pegawai_notelp" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_alamat">Alamat</label>
                                            <textarea class="form-control" id="pegawai_alamat" name="pegawai_alamat" rows="3" placeholder="Enter ..."></textarea>
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
    $(document).ready(function () {
        $('.select2').select2();    
    });

    function submitForm() {
        var dataForm = {
            pegawai_id : $("#pegawai_id").val(),
            pegawai_nama : $("#pegawai_nama").val(),
            pegawai_bagian : $("#pegawai_bagian").val(),
            pegawai_alamat : $("#pegawai_alamat").val(),
            pegawai_notelp : $("#pegawai_notelp").val(),
        };

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=createpegawai",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "pegawai " + $("#pegawai_nama").val() + " Berhasil disimpan", "success");
                    $("#Modalpegawai").modal('toggle');
                } else {
                    swal("Info!", "pegawai " + $("#pegawai_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#pegawai_id").val(0);
        $("#pegawai_nama").val('');
    }

    function addpegawai() {
        resetForm();
        $("#Modalpegawai").modal('toggle');
    }
</script>