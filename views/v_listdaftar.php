<style>
    @media (min-width: 500px) {
        .buttondaftar {
            margin-top : 30px;
        }
    }

    @media (max-width: 480px) {
        .buttondaftar {
            margin-top : 5px;
        }
    }
</style>
<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />
<script src="<?php echo BASE_URL ?>/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" id="date1" class="form-control datepicker" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 buttondaftar">
                        &nbsp;<button type="button" id="btn-filter" class="btn btn-primary btn-sm"><i class="fas fa-search"></i>&nbsp;Cari</button>
                        &nbsp;<button type="button" id="btn-excel" class="btn btn-success btn-sm"><i class="far fa-file-excel"></i>&nbsp;Excel</button>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="listtarif" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Tanggal Daftar</th>
                            <th>No. Rekam Medik</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Pesan</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th>Email</th>
                            <th>Telp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    <!-- /.col -->
    </div>
      <!-- /.row -->
</section>
<div class="modal fade" id="ModalDaftar" tabindex="-1" role="dialog" aria-labelledby="ModalDaftarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDaftarLabel">Data Pendaftaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form role="form" id="quickForm">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <input type="hidden" id="bookinghosp_id" name="bookinghosp_id">
                            <label for="exampleInputEmail1">Nama Pasien</label>
                            <div style="display: inline-flex;">
                                <input type="text" id="pasien_norm" name="pasien_norm" class="form-control" 
                                style="width: 150px;" readonly>&nbsp;
                                <input type="text" id="pasien_nama" name="pasien_nama" class="form-control" 
                                style="width: 273px;" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dokter</label>
                            <input type="text" id="pegawai_nama" name="pegawai_nama" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Tanggal pesan</label>
                                    <input type="text" id="bookinghosp_tanggal" name="bookinghosp_tanggal" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label>Jam</label>
                                    <input type="text" id="bookinghosp_jam" name="bookinghosp_jam" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label>Jam</label>
                            <input type="text" id="bookinghosp_jam" name="bookinghosp_jam" class="form-control" readonly>
                        </div> -->
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="pasien_email" name="pasien_email" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Telepon</label>
                            <input type="text" id="pasien_telp" name="pasien_telp" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Sudah Bayar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var oTable = "";
    $(function () {
        $('#date1').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' });
        $('#date1').val(moment(new Date()).format("DD-MM-YYYY"));
        oTable = $("#listtarif").DataTable({
            "responsive": true,
            "autoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=getListAntrian",
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "datefilter", "value": moment(moment($('#date1').val(), "DD/MM/YYYY")).format("YYYY-MM-DD") } );
            },
            "columnDefs": [ 
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 8 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 9 ],
                    "visible": false
                },
                {
                    "targets": [ 10 ],
                    "visible": false
                },
                {
                "targets": -1,
                "data": null,
                "defaultContent": "<button type='button' class='btn btn-warning' onclick='editRow(this)'><i class='fas fa-pencil-alt'></i></button>&nbsp;<button type=button onclick='deleteRow(this)' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>"
                }
            ]
        });

        $("#btn-filter").on("click", function () {
            oTable.ajax.reload();
        });

        $(".datepicker").datepicker({
			format: "dd-mm-yyyy",
			autoclose: true,
			daysOfWeekDisabled: [0]
        }).attr("readonly", "readonly").css({"cursor":"pointer", "background":"white"});
        
        $("#btn-excel").on("click", function () {
            importToExcel();
        });
        
    });

    function editRow(elem) {
        var td = $(elem).parent().parent().children();
        var data = oTable.row( $(elem).parents('tr') ).data();
        $("#ModalDaftar").modal('toggle');
        // var kode_smf = $(td).html()
        // window.location.href = "<?php echo BASE_URL; ?>/controllers/C_antrianbooking.php?action=editrow";
        $("#bookinghosp_id").val(data[0]);
        $("#pasien_norm").val($(td[1]).html());
        $("#pasien_nama").val($(td[2]).html());
        $("#bookinghosp_tanggal").val($(td[3]).html());
        $("#bookinghosp_jam").val($(td[4]).html() + " - " + $(td[5]).html());
        $("#pasien_email").val(data[9]);
        $("#pasien_telp").val(data[10]);
        $("#pegawai_nama").val(data[11]);
    }

    function deleteRow(elem) {
        var data = oTable.row( $(elem).parents('tr') ).data();
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=delete",
            type: "post",
            data: {
                bookinghosp_id : data[0]
            },
            success : function (res) {
                if (res == 200) {
                    swal("Info!", "Pendaftaran pasien " + data[4] + " Berhasil dibayar", "success");
                } else {
                    swal("Info!", "Pendaftaran pasien " + data[4] + " gagal dibayar", "error");
                }

                oTable.ajax.reload();
            }
        });    
    }

    function submitForm() {
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=sudahbayar",
            type: "post",
            data: {
                bookinghosp_id : $("#bookinghosp_id").val()
            },
            success : function (res) {
                if (res == 200) {
                    swal("Info!", "Pendaftaran pasien " + $("#pasien_nama").val() + " Berhasil dihapus", "success");
                } else {
                    swal("Info!", "Pendaftaran pasien " + $("#pasien_nama").val() + " gagal dihapus", "error");
                }
                $("#ModalDaftar").modal('toggle');
                oTable.ajax.reload();
            }
        });    
    }

    function importToExcel(elem) {
        var date1 = moment($("#date1").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=exceldaftar",
            type: "post",
            data: {
                date1 : date1
            },
            success : function (res) {
                window.open(res);
            }
        });
    }
</script>