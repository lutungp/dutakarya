<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_rekanan.php?action=getrekanan";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'rekanan_id', type: 'int' },
                { name: 'rekanan_kode', type: 'string' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_telp', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'rekanan_email', type: 'string' },
                { name: 'rekanan_aktif', type: 'string' },
            ],
            id: 'rekanan_id',
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
            $("#grid").jqxGrid('addfilter', 'rekanan_nama', filtergroup);
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
                { text: 'Kode Rekanan', datafield: 'rekanan_kode'},
                { text: 'Nama Rekanan', datafield: 'rekanan_nama'},
                { text: 'Rekanan Aktif', datafield: 'rekanan_aktif', filterable: false},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#ModalRekanan").modal('toggle');
                        $("#rekanan_id").val(dataRecord.rekanan_id);
                        $("#rekanan_kode").val(dataRecord.rekanan_kode);
                        $("#rekanan_nama").val(dataRecord.rekanan_nama);
                        $("#rekanan_telp").val(dataRecord.rekanan_telp);
                        $("#rekanan_email").val(dataRecord.rekanan_email);
                        $("#rekanan_alamat").val(dataRecord.rekanan_alamat);
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        swal({
                            title: "Hapus rekanan " + dataRecord.rekanan_nama,
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
                                url: "<?php echo BASE_URL ?>/controllers/C_rekanan.php?action=deleterekanan",
                                type: "post",
                                data: { rekanan_id : dataRecord.rekanan_id,  alasan : inputValue},
                                success : function (res) {
                                    $("#grid").jqxGrid('updatebounddata');
                                    if (res == 200) {
                                        swal("Info!", "Rekanan " + $("#rekanan_nama").val() + " Berhasil dihapus", "success");
                                    } else {
                                        swal("Info!", "Rekanan " + $("#rekanan_nama").val() + " Gagal dihapus", "error");
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
                <div class="modal fade" id="ModalRekanan" tabindex="-1" role="dialog" aria-labelledby="ModalUserLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalUserLabel">Data Rekanan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="rekanan_id" name="rekanan_id">
                                            <label for="rekanan_kode">Kode</label>
                                            <input type="text" id="rekanan_kode" name="rekanan_kode" class="form-control" require>
                                        </div>
                                        <div class="form-group">
                                            <label for="rekanan_nama">Nama</label>
                                            <input type="text" id="rekanan_nama" name="rekanan_nama" class="form-control" require>
                                        </div>
                                        <div class="form-group">
                                            <label for="rekanan_telp">Telp</label>
                                            <input type="phone" id="rekanan_telp" name="rekanan_telp" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="rekanan_email">email</label>
                                            <input type="phone" id="rekanan_email" name="rekanan_email" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="rekanan_alamat">Alamat</label>
                                            <textarea class="form-control" id="rekanan_alamat" name="rekanan_alamat" rows="3" placeholder="Enter ..."></textarea>
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
            rekanan_id : $("#rekanan_id").val(),
            rekanan_kode : $("#rekanan_kode").val(),
            rekanan_nama : $("#rekanan_nama").val(),
            rekanan_telp : $("#rekanan_telp").val(),
            rekanan_email : $("#rekanan_email").val(),
            rekanan_alamat : $("#rekanan_alamat").val(),
        };

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_rekanan.php?action=createrekanan",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "Rekanan " + $("#rekanan_nama").val() + " Berhasil disimpan", "success");
                    $("#ModalRekanan").modal('toggle');
                } else {
                    swal("Info!", "Rekanan " + $("#rekanan_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#rekanan_id").val(0);
        $("#rekanan_kode").val('');
        $("#rekanan_nama").val('');
        $("#rekanan_telp").val('');
        $("#rekanan_alamat").val('');
        $("#rekanan_email").val('');
    }

    function adduser() {
        resetForm();
        $("#ModalRekanan").modal('toggle');
    }
</script>