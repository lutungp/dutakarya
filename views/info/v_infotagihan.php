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
        var applyfilter = function (tanggal, rekanan) {
            var rekananArr = [];
            rekanan.forEach(function (elem) {
                rekananArr.push(elem.value)
            });
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getpenagihan",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr : rekananArr
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#grid").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            penagihan_id : element.penagihan_id,
                            penagihan_no : element.penagihan_no,
                            penagihan_tgl : element.penagihan_tgl,
                            m_rekanan_id : element.m_rekanan_id,
                            rekanan_nama : element.rekanan_nama,
                            penagihandet_ppn : element.penagihandet_ppn,
                            penagihandet_subtotal : element.penagihandet_subtotal,
                            penagihandet_potongan : element.penagihandet_potongan,
                            penagihandet_total : element.penagihandet_total,
                            t_pelunasandet_bayar : element.t_pelunasandet_bayar
                        };
                        $("#grid").jqxGrid('addrow', null, datarow);
                    });
                }
            });
        };
        // prepare the data
        var gridSource ={
            datatype: "json",
            datafields: [
                { name: 'penagihan_id', type: 'int' },
                { name: 'penagihan_no', type: 'string' },
                { name: 'penagihan_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'penagihandet_ppn', type: 'float' },
                { name: 'penagihandet_subtotal', type: 'float' },
                { name: 'penagihandet_potongan', type: 'float' },
                { name: 'penagihandet_total', type: 'float' },
                { name: 't_pelunasandet_bayar', type: 'float' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getpenagihan&tanggal=" + tanggal
        };

        var gridAdapter = new $.jqx.dataAdapter(gridSource);
        $("#grid").jqxGrid({
            width: '100%',
            source: gridAdapter,
            altrows: true,
            showtoolbar: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="datefilter" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter" value="FILTER" /></div>');
                container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport" /></div>');
                $("#datefilter").jqxDateTimeInput({ width: '200px', height: '28px', formatString: 'dd-MM-yyyy',  selectionMode: 'range'});
                $('#datefilter').on('change', function (event) {  
                    var jsDate = event.args.date; 
                    var type = event.args.type; // keyboard, mouse or null depending on how the date was selected.
                    var dateselect = moment(jsDate).format('YYYY-MM-DD');
                });
                var rekananSource = {
                    datatype: "json",
                    datafields: [
                        { name: 'rekanan_id' },
                        { name: 'rekanan_nama' }
                    ],
                    id: 'id',
                    url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getrekanan",
                    async: false
                };
                var rekananAdapter = new $.jqx.dataAdapter(rekananSource);
                $("#rekananfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                $("#applyfilter").jqxButton({ template: "primary", width: 120, height: 28 });
                $("#applyfilter").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter").jqxDropDownList('getCheckedItems');
                    applyfilter(tanggal, rekanan);
                });

                $("#excelExport").jqxButton({ width: 120, height: 28 });
                $("#excelExport").click(function () {
                    $("#grid").jqxGrid('exportdata', 'xlsx', 'jqxGrid');           
                });
            },
            columns: [
                { text: 'No. penagihan', datafield: 'penagihan_no', columntype: 'textbox', width : 110, cellsalign : 'center' },
                { text: 'Tanggal', datafield: 'penagihan_tgl',  cellsalign: 'center',  cellsformat: 'dd-MM-yyyy', width : 110 },
                { text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left' },
                { text: 'Subtotal', datafield: 'penagihandet_subtotal',  cellsalign: 'right', cellsformat : 'F', width : 100 },
                { text: 'Subtotal', datafield: 'penagihandet_potongan',  cellsalign: 'right', cellsformat : 'F', width : 100 },
                { text: 'Total', datafield: 'penagihandet_total',  cellsalign: 'right', cellsformat : 'F', width : 100 },
                { text: 'Dibayar', datafield: 't_pelunasandet_bayar',  cellsalign: 'right', cellsformat : 'F', width : 100 },
            ]
        });
    }
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