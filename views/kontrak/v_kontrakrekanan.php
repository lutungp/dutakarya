<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_kontrakrekanan.php?action=gethargakontrak";
        // prepare the data
        var source = {
            datatype: "json",
            datafields: [
                { name: 'hargakontrak_id', type: 'int' },
                { name: 'hargakontrak_no', type: 'string' },
                { name: 'hargakontrak_tgl', type: 'date' },
                { name: 'hargakontrak_aktif', type: 'string' },
                { name: 'rekanan_kode', type: 'string' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'user_nama', type: 'string' },
                { name: 'hargakontrak_created_date', type: 'date' },
            ],
            id: 'hargakontrak_id',
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
                { text: 'No. Kontrak', datafield: 'hargakontrak_no', cellsalign: 'center',  width : 140 },
                { text: 'Berlaku Mulai', datafield: 'hargakontrak_tgl', cellsformat: 'dd-MM-yyyy', cellsalign: 'center',  width : 140 },
                { text: 'Kode Rekanan', datafield: 'rekanan_kode', cellsalign: 'center', width : 100 },
                { text: 'Rekanan', datafield: 'rekanan_nama', cellsalign: 'left' },
                { text: 'Dibuat Oleh', datafield: 'user_nama', cellsalign: 'left', width : 100 },
                { text: 'Dibuat Tgl', datafield: 'hargakontrak_created_date', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 100 },
                <?php if ($read <> '' || $update <> '') {?>
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_kontrakrekanan.php?action=formtransaksi&id='+dataRecord.hargakontrak_id;
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
        window.location.href='<?php echo BASE_URL ?>/controllers/C_kontrakrekanan.php?action=formtransaksi';
    }
</script>