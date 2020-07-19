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
        var applyfilter = function (tanggal, rekanan, barang) {
            var rekananArr = [];
            rekanan.forEach(function (elem) {
                rekananArr.push(elem.value)
            });
            var barangArr = [];
            barang.forEach(function (elem) {
                barangArr.push(elem.value)
            });
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_infopenerimaan.php?action=getpenerimaan",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr : rekananArr,
                    barangArr : barangArr
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#grid").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            penerimaan_id : element.penerimaan_id,
                            penerimaan_no : element.penerimaan_no,
                            penerimaan_tgl : element.penerimaan_tgl,
                            m_rekanan_id : element.m_rekanan_id,
                            rekanan_nama : element.rekanan_nama,
                            m_barang_id : element.m_barang_id,
                            barang_nama : element.barang_nama,
                            satuan_nama : element.satuan_nama,
                            penerimaandet_qty : element.penerimaandet_qty,
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
                { name: 'penerimaan_id', type: 'int' },
                { name: 'penerimaan_no', type: 'string' },
                { name: 'penerimaan_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'm_barang_id', type: 'int' },
                { name: 'barang_nama', type: 'string' },
                { name: 'satuan_nama', type: 'string' },
                { name: 'penerimaandet_qty', type: 'float' },
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopenerimaan.php?action=getpenerimaan&tanggal=" + tanggal
        };

        var gridAdapter = new $.jqx.dataAdapter(gridSource);
        $("#grid").jqxGrid({
            width: '100%',
            source: gridAdapter,
            showtoolbar: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="datefilter" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter" style="margin: 2px;"></div>');
                container.append('<div id="barangfilter" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter" value="FILTER" /></div>');
                $("#datefilter").jqxDateTimeInput({ width: '200px', height: '30px', formatString: 'dd-MM-yyyy',  selectionMode: 'range'});
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infopenerimaan.php?action=getrekanan",
                    async: false
                };
                var rekananAdapter = new $.jqx.dataAdapter(rekananSource);
                $("#rekananfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 30,});
                var barangSource = {
                    datatype: "json",
                    datafields: [
                        { name: 'barang_id' },
                        { name: 'barang_nama' }
                    ],
                    url: "<?php echo BASE_URL ?>/controllers/C_infopenerimaan.php?action=getbarang",
                };
                var barangAdapter = new $.jqx.dataAdapter(barangSource);
                $("#barangfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 30,});

                $("#applyfilter").jqxButton({ template: "primary", width: 120, height: 30 });
                $("#applyfilter").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter").jqxDropDownList('getCheckedItems');
                    applyfilter(tanggal, rekanan, barang);
                })
            },
            columns: [
                { text: 'No. Transaksi', datafield: 'penerimaan_no', columntype: 'textbox', width : 170, cellsalign : 'center' },
                { text: 'Tanggal', datafield: 'penerimaan_tgl',  cellsalign: 'center',  cellsformat: 'dd-MM-yyyy', width : 170 },
                { text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left' },
                { text: 'Nama Barang', datafield: 'barang_nama',  cellsalign: 'left' },
                { text: 'Satuan', datafield: 'satuan_nama',  cellsalign: 'left', width : 120 },
                { text: 'Qty', datafield: 'penerimaandet_qty',  cellsalign: 'right', width : 120 },
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