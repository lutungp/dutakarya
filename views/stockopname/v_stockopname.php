<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=getstockopname";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'stockopname_id', type: 'int' },
                { name: 'stockopname_no', type: 'string' },
                { name: 'stockopname_tgl', type: 'date' },
                { name: 'user_name', type: 'string' },
                { name: 'stockopname_created_date', type: 'date' },
                { name: 'stockopname_aktif', type: 'string' },
            ],
            id: 'stockopname_id',
            url: url,
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });

        // initialize jqxGrid
        $("#grid").jqxGrid({
            height : '100%',
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            selectionmode: 'multiplecellsadvanced',
            filterable: true,
            autoshowfiltericon: true,
            columns: [
                { text: 'No. Transaksi', datafield: 'stockopname_no', width : 200, cellsalign: 'center'},
                { text: 'Tanggal', datafield: 'stockopname_tgl', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 200 },
                // { text: 'Keterangan', datafield: 'stockopname_keterangan', cellsalign: 'left'},
                { text: 'Dibuat Oleh', datafield: 'stockopname_created_by', width : 200 },
                { text: 'Dibuat Tanggal', datafield: 'stockopname_created_date', cellsformat: 'dd-MM-yyyy', width : 200 },
                <?php if ($read <> '' || $update <> '') {?>
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=formtransaksi&id='+dataRecord.stockopname_id;
                    }
                },
                <?php } ?>
            ]
        });
    });
</script>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addtransaksi()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>

    function addtransaksi() {
        window.location.href='<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=formtransaksi';
    }
</script>