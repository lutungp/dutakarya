<style>
    .form-control:disabled, .form-control[readonly] {
        background-color: #fff;
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
                                <input type="text" id="datefilter" class="form-control float-right">
                                &nbsp;<button type="button" id="btn-filter" class="btn btn-primary"><i class="fas fa-search"></i>&nbsp;Cari</button>
                                <!-- &nbsp;<button type="button" id="btn-excel" class="btn btn-success"><i class="far fa-file-excel"></i>&nbsp;Excel</button> -->
                            </div>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="listperlayanan" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <!-- <th>No.</th> -->
                            <th>Layanan</th>
                            <th>Baru</th>
                            <th>Lama</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                </table>
                <input type="hidden" id="instalasi"/>
                <input type="hidden" id="layanan">
                <input type="hidden" id="groupjaminan">
                <input type="hidden" id="tipejaminan">
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
    var oTable = "";
    $(function () {
        $('#datefilter').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        var datefilter = $('#datefilter').val();
        datefilter = datefilter.split(" - ");
        console.log(datefilter)
        oTable = $("#listperlayanan").DataTable({
            "responsive": true,
            "autoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "sAjaxSource": "<?php echo BASE_URL ?>/controllers/C_lapkunjunganpasien.php?action=getPerLayanan",
            "fnServerParams": function ( aoData ) {
                aoData.push( 
                    { 
                        "name": "tglawal", 
                        "value": moment(datefilter[0], 'DD/MM/YYYY').format("YYYY-MM-DD")
                    },
                    { 
                        "name": "tglakhir", 
                        "value": moment(datefilter[1], 'DD/MM/YYYY').format("YYYY-MM-DD")
                    },
                    {
                        "name": "instalasi", 
                        "value": $('#instalasi').val()
                    },
                    {
                        "name": "layanan", 
                        "value": $('#layanan').val()
                    },
                    {
                        "name": "groupjaminan", 
                        "value": $('#groupjaminan').val()
                    },
                    {
                        "name": "tipejaminan", 
                        "value": $('#tipejaminan').val()
                    }
                );
            }
        });

        $("#btn-filter").on("click", function () {
            oTable.ajax.reload();
        });

        $("#btn-excel").on("click", function () {
            importToExcel();
        });
        
    });

    function editRow(elem) {

    }

    function deleteRow(elem) {

    }

    function submitForm() {
 
    }

    function importToExcel(elem) {

    }
</script>