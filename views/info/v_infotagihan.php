<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var now = new Date();
        renderGrid(moment(now).format('YYYY-MM-DD'));
        renderGrid2(moment(now).format('YYYY-MM-DD'));
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
                            rekanan_alamat : element.rekanan_alamat,
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
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'penagihandet_ppn', type: 'float' },
                { name: 'penagihandet_subtotal', type: 'number' },
                { name: 'penagihandet_potongan', type: 'number' },
                { name: 'penagihandet_total', type: 'number' },
                { name: 't_pelunasandet_bayar', type: 'number' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getpenagihan&tanggal=" + tanggal
        };

        var gridAdapter = new $.jqx.dataAdapter(gridSource);
        $("#grid").jqxGrid({
            width: '100%',
            source: gridAdapter,
            altrows: true,
            showtoolbar: true,
            showstatusbar: true,
            showaggregates: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="datefilter" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter" value="FILTER" /></div>');
                // container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport" /></div>');
                $("#datefilter").jqxDateTimeInput({ width: '170px', height: '28px', formatString: 'dd-MM-yyyy',  selectionMode: 'range'});
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
                $("#applyfilter").jqxButton({ template: "primary", width: 80, height: 28 });
                $("#applyfilter").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter").jqxDropDownList('getCheckedItems');
                    applyfilter(tanggal, rekanan);
                });

                // $("#excelExport").jqxButton({ width: 120, height: 28 });
                // $("#excelExport").click(function () {
                //     $("#grid").jqxGrid('exportdata', 'xlsx', 'jqxGrid');           
                // });
            },
            columns: [
                { text: 'No. Penagihan', datafield: 'penagihan_no', columntype: 'textbox', width : 110, cellsalign : 'center' },
                { text: 'Tanggal', datafield: 'penagihan_tgl',  cellsalign: 'center',  cellsformat: 'dd-MM-yyyy', width : 110 },
                { text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left' },
                { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left' },
                { 
                    text: 'Subtotal', datafield: 'penagihandet_subtotal',  cellsalign: 'right', cellsformat : 'F', width : 100,
                    aggregates: ['sum'],
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                        var subtotal = 0;
                        $.each(aggregates, function (key, value) {
                            subtotal = parseFloat(subtotal) + parseFloat(value);
                            // var name = key == 'sum' ? 'Sum' : 'Avg';
                            renderstring += '<div style="padding:5px;font-size:16px;"><b>' + value + '</b></div>';
                        });
                        renderstring += "</div>";
                        return renderstring;
                    }
                },
                { text: 'Potongan', datafield: 'penagihandet_potongan',  cellsalign: 'right', cellsformat : 'F', width : 100 },
                { 
                    text: 'Total', datafield: 'penagihandet_total',  cellsalign: 'right', cellsformat : 'F', width : 100,
                    aggregates: ['sum'],
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                        var subtotal = 0;
                        $.each(aggregates, function (key, value) {
                            subtotal = parseFloat(subtotal) + parseFloat(value);
                            // var name = key == 'sum' ? 'Sum' : 'Avg';
                            renderstring += '<div style="padding:5px;font-size:16px;"><b>' + value + '</b></div>';
                        });
                        renderstring += "</div>";
                        return renderstring;
                    }
                },
                { 
                    text: 'Dibayar', datafield: 't_pelunasandet_bayar',  cellsalign: 'right', cellsformat : 'F', width : 100,
                    aggregates: ['sum'],
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                        var subtotal = 0;
                        $.each(aggregates, function (key, value) {
                            subtotal = parseFloat(subtotal) + parseFloat(value);
                            // var name = key == 'sum' ? 'Sum' : 'Avg';
                            renderstring += '<div style="padding:5px;font-size:16px;"><b>' + value + '</b></div>';
                        });
                        renderstring += "</div>";
                        return renderstring;
                    }
                },
            ]
        });
    }

    function renderGrid2(tanggal) {
        var applyfilter2 = function (tanggal, rekanan, barang, tagih) {
            var rekananArr2 = [];
            rekanan.forEach(function (elem) {
                rekananArr2.push(elem.value)
            });
            var barangArr2 = [];
            barang.forEach(function (elem) {
                barangArr2.push(elem.value)
            });
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getpengiriman",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr2 : rekananArr2,
                    barangArr2 : barangArr2,
                    tagih : tagih
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#jadwalkirimgrid").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            pengiriman_id : element.pengiriman_id,
                            pengiriman_no : element.pengiriman_no,
                            pengiriman_tgl : element.pengiriman_tgl,
                            penagihan_no : element.penagihan_no,
                            penagihan_tgl : element.penagihan_tgl,
                            m_rekanan_id : element.m_rekanan_id,
                            rekanan_nama : element.rekanan_nama,
                            rekanan_alamat : element.rekanan_alamat,
                            m_barang_id : element.m_barang_id,
                            barang_nama : element.barang_nama,
                            satuan_nama : element.satuan_nama,
                            pengirimandet_qty : element.pengirimandet_qty,
                        };
                        
                        $("#jadwalkirimgrid").jqxGrid('addrow', null, datarow);
                    });
                }
            });
        };
        // prepare the data
        var gridSource2 ={
            datatype: "json",
            datafields: [
                { name: 'pengiriman_id', type: 'int' },
                { name: 'pengiriman_no', type: 'string' },
                { name: 'pengiriman_tgl', type: 'date' },
                { name: 'penagihan_no', type: 'string' },
                { name: 'penagihan_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'm_barang_id', type: 'int' },
                { name: 'barang_nama', type: 'string' },
                { name: 'satuan_nama', type: 'string' },
                { name: 'pengirimandet_qty', type: 'float' },
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getpengiriman&tanggal=" + tanggal
        };

        var gridAdapter = new $.jqx.dataAdapter(gridSource2);
        var tagih = [
            { 'penagihan' : '', 'penagihan_text' : 'Semua'},
            { 'penagihan' : 'N', 'penagihan_text' : 'Belum ditagih'},
            { 'penagihan' : 'Y', 'penagihan_text' : 'Sudah ditagih'}
        ];
        $("#jadwalkirimgrid").jqxGrid({
            width: '100%',
            source: gridAdapter,
            autoheight : true,
            altrows: true,
            autorowheight : true,
            showtoolbar: true,
            showstatusbar: true,
            showaggregates: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="datefilter2" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter2" style="margin: 2px;"></div>');
                container.append('<div id="barangfilter2" style="margin: 2px;"></div>');
                container.append('<div id="tagih2" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter2" value="FILTER" /></div>');
                // container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport" /></div>');
                $("#datefilter2").jqxDateTimeInput({ width: '170px', height: '28px', formatString: 'dd-MM-yyyy',  selectionMode: 'range'});
                $('#datefilter2').on('change', function (event) {  
                    var jsDate = event.args.date; 
                    var type = event.args.type; // keyboard, mouse or null depending on how the date was selected.
                    var dateselect = moment(jsDate).format('YYYY-MM-DD');
                });
                var rekananSource2 = {
                    datatype: "json",
                    datafields: [
                        { name: 'rekanan_id' },
                        { name: 'rekanan_nama' }
                    ],
                    id: 'id',
                    url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getrekanan",
                    async: false
                };
                var rekananAdapter2 = new $.jqx.dataAdapter(rekananSource2);
                $("#rekananfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter2, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                var barangSource2 = {
                    datatype: "json",
                    datafields: [
                        { name: 'barang_id' },
                        { name: 'barang_nama' }
                    ],
                    url: "<?php echo BASE_URL ?>/controllers/C_infotagihan.php?action=getbarang",
                };
                var barangAdapter = new $.jqx.dataAdapter(barangSource2);
                $("#barangfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 28,});
                $("#tagih2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: tagih, displayMember: "penagihan_text", valueMember: "penagihan", width: 120, height: 28,});

                $("#applyfilter2").jqxButton({ template: "primary", width: 80, height: 28 });
                $("#applyfilter2").on('click', function() {
                    var tanggal = $("#datefilter2").val();
                    var rekanan = $("#rekananfilter2").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter2").jqxDropDownList('getCheckedItems');
                    var tagih = $("#tagih2").jqxDropDownList('val');
                    applyfilter2(tanggal, rekanan, barang, tagih);
                });

                // $("#excelExport").jqxButton({ width: 120, height: 28 });
                // $("#excelExport").click(function () {
                //     $("#grid").jqxGrid('exportdata', 'xlsx', 'jqxGrid');
                // });
            },
            columns: [
                { 
                    text: 'No. Pengiriman', datafield: 'pengiriman_no', columntype: 'textbox', width : 170, cellsalign : 'center',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalkirimgrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.pengiriman_no + "</br>";
                        html += moment(recorddata.pengiriman_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        html += "</div>";
                        return html;
                    },
                },
                { 
                    text: 'No. Penagihan', datafield: 'penagihan_no', columntype: 'textbox', width : 170, cellsalign : 'center',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalkirimgrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.penagihan_no + "</br>";
                        if (recorddata.penagihan_tgl !== '') {
                            html += moment(recorddata.penagihan_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');    
                        }
                        html += "</div>";
                        return html;
                    },
                },
                { 
                    text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalkirimgrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.rekanan_nama + "</br>";
                        html += 'Alamat : ' + recorddata.rekanan_alamat + "</br>";
                        html += "</div>";
                        return html;
                    },
                },
                // { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left' },
                { text: 'Nama Barang', datafield: 'barang_nama',  cellsalign: 'left' },
                { text: 'Satuan', datafield: 'satuan_nama',  cellsalign: 'left', width : 120 },
                { text: 'Qty', datafield: 'pengirimandet_qty',  cellsalign: 'right', width : 120 },
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <div></div>
                    <div id="jadwalkirimgrid"></div>
                </div>
            </div>
        </div>
    </div>
</section>