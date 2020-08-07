<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=getbarangrusak";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'barangrusak_id', type: 'int' },
                { name: 'barangrusak_no', type: 'string' },
                { name: 'barangrusak_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'penerimaan_aktif', type: 'string' },
                { name: 't_penagihan_no', type: 'string' },
            ],
            id: 'barangrusak_id',
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
            // add the filters.
            $("#grid").jqxGrid('addfilter', 'rekanan_nama', filtergroup);
            // // apply the filters.
            $("#grid").jqxGrid('applyfilters');
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
            sortable: true,
            enabletooltips: true,
            selectionmode: 'multiplecellsadvanced',
            filterable: true,
            autoheight : true,
            autorowheight : true,
            ready: function () {
                addfilter();
            },
            autoshowfiltericon: true,
            columns: [
                { text: 'No. Transaksi', datafield: 'barangrusak_no', width : 200, cellsalign: 'center'},
                { 
                    text: 'Rekanan', datafield: 'rekanan_nama',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#grid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += value + '<br>Alamat : ' + recorddata.rekanan_alamat;
                        html += "</div>";
                        return html;
                    },
                },
                { text: 'No. Penagihan', datafield: 't_penagihan_no', cellsalign: 'center', width : 150},
                { text: 'Tanggal', datafield: 'barangrusak_tgl', cellsformat: 'dd-MM-yyyy', cellsalign: 'center', width : 150},
                // { text: 'Penerimaan Aktif', datafield: 'penerimaan_aktif', filterable: false},
                <?php if ($read <> '' || $update <> '') {?>
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=formtransaksi&id='+dataRecord.barangrusak_id;
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
        window.location.href='<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=formtransaksi';
    }
</script>