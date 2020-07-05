<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var now = new Date();
        renderGrid(moment(now).format('YYYY-MM-DD'));
    });

    function renderGrid(tanggal) {
        var barangStockSource = {
                datatype: "json",
                datafields: [
                    { name: 'barang_id', type: 'int' },
                    { name: 'barang_nama', type: 'string' },
                ],
                // localdata: countries
                datatype: "json",
                id: 'barang_id',
                url: "<?php echo BASE_URL ?>/controllers/C_infostock.php?action=getbarang",
        };
        // prepare the data
        var gridSource ={
            datatype: "json",
            datafields: [
                { name: 'barang_id', type: 'int' },
                { name: 'barang_nama', type: 'string' },
                { name: 'barangtrans_tgl', type: 'date' },
                { name: 'satuan_nama', type: 'float' },
                { name: 'barangtrans_akhir', type: 'float' },
            ],
            id: 'barang_id',
            url: "<?php echo BASE_URL ?>/controllers/C_infostock.php?action=getstock&tanggal=" + tanggal
        };

        var gridAdapter = new $.jqx.dataAdapter(gridSource);
        $("#grid").jqxGrid({
            width: '100%',
            source: gridAdapter,
            filterable: true,
            showfilterrow: true,
            showtoolbar: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='margin: 5px;'></div>");
                toolbar.append(container);
                container.append('<div id="datefilter"></div>');
                $("#datefilter").jqxDateTimeInput({ width: '150px', height: '25px', formatString: 'dd-MM-yyyy'});
                $('#datefilter').on('change', function (event) {  
                    var jsDate = event.args.date; 
                    var type = event.args.type; // keyboard, mouse or null depending on how the date was selected.
                    var dateselect = moment(jsDate).format('YYYY-MM-DD');
                    renderGrid(dateselect);
                }); 
            },
            columns: [
                {
                    text: 'Nama Barang', filtertype: 'checkedlist', filteritems: new $.jqx.dataAdapter(barangStockSource), datafield: 'barang_id', displayfield: 'barang_nama',
                    createfilterwidget: function (column, htmlElement, editor) {
                        editor.jqxDropDownList({ displayMember: "barang_nama", valueMember: "barang_id" });
                    }
                },
                { text: 'Qty.', datafield: 'barangtrans_akhir', filtertype: 'number',  cellsalign: 'right' },
                { text: 'Satuan', datafield: 'satuan_nama', filterable : false }                
            ]
        });
    }
</script>
</script>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <div></div>
                    <div id="grid"></div>
                </div>
            </div>
        </div>
    </div>
</section>