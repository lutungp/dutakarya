<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="listdaftar" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>KODE SMF</th>
                                <th>NAMA SM</th>
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
<script>
    $(function () {
        $("#listdaftar").DataTable({
            "responsive": true,
            "autoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=getListDaftar",
            "columnDefs": [ {
                "targets": -1,
                "data": null,
                "defaultContent": "<button type='button' class='btn btn-warning' onclick='editRow(this)'><i class='fas fa-pencil-alt'></i></button>&nbsp;<button type=button ' onclick='deleteRow()' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>"
            }]
        });
    });

    function editRow(elem) {
        // var td = $(elem).parent().parent().children();
        // var kode_smf = $(td).html()
        // window.location.href = "<?php // echo BASE_URL; ?>/controllers/C_antrianbooking.php?action=editrow";
    }

    function deleteRow() {
        
    }
</script>