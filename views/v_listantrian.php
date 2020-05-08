<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
<style>
    .list-antrian {
        padding : 20px;
    }

    .list-header {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .rumahsakit {
        font-weight: bold;
        font-size : 22px;
        color: #346d00;
    }
</style>
<div class="list-antrian">
    <div class="list-header rumahsakit">RUMAH SAKIT HAJI JAKARTA</div>
    <div class="list-header">JADWAL DOKTER POLI UMUM</div>
    <div class="container table-responsive">
        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Urutan</th>
                </tr>
            </thead>
        </table>
    </div>
    <div style="text-align: center;">
        <button type="button" onclick="window.location.href='<?php echo BASE_URL ?>'" class="btn btn-danger">KEMBALI</button>
    </div>
</div>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
<script>
    
    $(document).ready(function() {
        $('#example').dataTable( {
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl + "/controllers/C_antrianbooking.php?action=getListAntrian",
        });
    });
</script>