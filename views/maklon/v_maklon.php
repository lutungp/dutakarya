<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_maklon.php?action=getmaklon";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'maklon_id', type: 'int' },
                { name: 'maklon_no', type: 'string' },
                { name: 'maklon_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'maklon_aktif', type: 'string' },
                { name: 'user_nama', type: 'string' },
                { name: 'maklon_created_date', type: 'date' },
            ],
            id: 'maklon_id',
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
            height : '100%',
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            // autoheight: true,
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
                { text: 'No. Transaksi', datafield: 'maklon_no', width : 180, cellsalign: 'center'},
                { text: 'Rekanan', datafield: 'rekanan_nama', width : 400},
                { text: 'Tanggal', datafield: 'maklon_tgl', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 200},
                { text: 'Dibuat Oleh', datafield: 'user_nama', cellsalign: 'center', width : 180},
                { text: 'Dibuat Tanggal', datafield: 'maklon_created_date', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 180},
                <?php if ($read <> '' || $update <> '') {?>
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_maklon.php?action=formtransaksi&id='+dataRecord.maklon_id;
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
        window.location.href='<?php echo BASE_URL ?>/controllers/C_maklon.php?action=formtransaksi';
    }
</script>