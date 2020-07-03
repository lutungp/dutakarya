<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_satuan.php?action=getsatuan";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'satuan_id', type: 'int' },
                { name: 'satuan_kode', type: 'string' },
                { name: 'satuan_nama', type: 'string' },
                { name: 'satuan_aktif', type: 'string' },
            ],
            id: 'satuan_id',
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
            $("#grid").jqxGrid('addfilter', 'satuan_nama', filtergroup);
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
                { text: 'Kode Satuan', datafield: 'satuan_kode'},
                { text: 'Nama Satuan', datafield: 'satuan_nama'},
                { text: 'Satuan Aktif', datafield: 'satuan_aktif', filterable: false},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#ModalSatuan").modal('toggle');
                        $("#satuan_id").val(dataRecord.satuan_id);
                        $("#satuan_kode").val(dataRecord.satuan_kode);
                        $("#satuan_nama").val(dataRecord.satuan_nama);
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        swal({
                            title: "Hapus satuan " + dataRecord.satuan_nama,
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
                                url: "<?php echo BASE_URL ?>/controllers/C_satuan.php?action=deletesatuan",
                                type: "post",
                                data: { satuan_id : dataRecord.satuan_id,  alasan : inputValue},
                                success : function (res) {
                                    $("#grid").jqxGrid('updatebounddata');
                                    if (res == 200) {
                                        swal("Info!", "Satuan " + $("#satuan_nama").val() + " Berhasil dihapus", "success");
                                    } else {
                                        swal("Info!", "Satuan " + $("#satuan_nama").val() + " Gagal dihapus", "error");
                                    }
                                }
                            });
                        });
                    }
                }
            ]
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
                <div class="modal fade" id="ModalSatuan" tabindex="-1" role="dialog" aria-labelledby="ModalUserLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalUserLabel">Data Satuan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="satuan_id" name="satuan_id">
                                            <label for="satuan_kode">Satuan Kode</label>
                                            <input type="text" id="satuan_kode" name="satuan_kode" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="satuan_nama">Satuan Nama</label>
                                            <input type="text" id="satuan_nama" name="satuan_nama" class="form-control">
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
    function submitForm() {
        var dataForm = {
            satuan_id : $("#satuan_id").val(),
            satuan_kode : $("#satuan_kode").val(),
            satuan_nama : $("#satuan_nama").val(),
        };

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_satuan.php?action=createsatuan",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "Satuan " + $("#satuan_nama").val() + " Berhasil disimpan", "success");
                    $("#ModalSatuan").modal('toggle');
                } else {
                    swal("Info!", "Satuan " + $("#satuan_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#satuan_id").val(0);
        $("#satuan_kode").val('');
        $("#satuan_nama").val('');
    }

    function adduser() {
        resetForm();
        $("#ModalSatuan").modal('toggle');
    }
</script>