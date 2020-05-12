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
                                <input type="text" id="date1" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>
                                &nbsp;<button type="button" id="btn-filter" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="listtarif" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <th>No. Rekam Medik</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Pesan</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Urutan</th>
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
<script>
    $(function () {
        var oTable = $("#listtarif").DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=getListAntrian",
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "datefilter", "value": new Date() } );
            },
            // "ajax" : {
            //     url : "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=getListAntrian",
            //     type : "POST",
            //     data : {
            //         date1 : new Date($("#date1"))
            //     }
            // },
            "columnDefs": [ {
                "targets": -1,
                "data": null,
                "defaultContent": "<button type='button' class='btn btn-warning' onclick='editRow(this)'><i class='fas fa-pencil-alt'></i></button>&nbsp;<button type=button ' onclick='deleteRow()' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>"
            }]
        });

        $("#btn-filter").on("click", function () {
            oTable.ajax.reload();
        });

        $('#date1').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
        $('#date1').val(new Date());
    });

    function editRow(elem) {
        var td = $(elem).parent().parent().children();
        console.log(td);
        // var kode_smf = $(td).html()
        // window.location.href = "<?php echo BASE_URL; ?>/controllers/C_antrianbooking.php?action=editrow";
    }

    function deleteRow() {
        
    }
</script>