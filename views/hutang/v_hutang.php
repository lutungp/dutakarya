<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>
<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_hutang.php?action=gethutang";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'hutang_id', type: 'int' },
                { name: 'hutang_no', type: 'string' },
                { name: 'hutang_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'user_nama', type: 'string' },
                { name: 'hutang_created_date', type: 'date' },
            ],
            id: 'hutang_id',
            url: url
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
            $("#grid").jqxGrid('addfilter', 'rekanan_nama', filtergroup);
        }
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
            ready: function () {
                addfilter();
            },
            autoshowfiltericon: true,
            columns: [
                { text: 'No. Hutang', datafield: 'hutang_no', width : 180, cellsalign: 'center'},
                { text: 'Tgl. Transaksi', datafield: 'hutang_tgl', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 150},
                { text: 'Rekanan', datafield: 'rekanan_nama'},
                { text: 'Dibuat Oleh', datafield: 'user_nama', width : 150},
                { text: 'Dibuat Tanggal', datafield: 'hutang_created_date', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 150},
                <?php if ($read <> '' || $update <> '') {?>
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_hutang.php?action=formtransaksi&id='+dataRecord.hutang_id;
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
        window.location.href='<?php echo BASE_URL ?>/controllers/C_hutang.php?action=formtransaksi';
    }
</script>