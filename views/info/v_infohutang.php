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
                url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=gethutanglunas",
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
                            hutang_no : element.hutang_no,
                            hutang_tgl : element.hutang_tgl,
                            rekanan_nama : element.rekanan_nama,
                            rekanan_alamat : element.rekanan_alamat,
                            hutangdet_tagihan : element.hutangdet_tagihan,
                            hutangdet_bayar : element.hutangdet_bayar,
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
                { name: 'hutang_no', type: 'string' },
                { name: 'hutang_tgl', type: 'date' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'hutangdet_tagihan', type: 'number' },
                { name: 'hutangdet_bayar', type: 'number' },
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=gethutanglunas&tanggal=" + tanggal
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=getrekanan",
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
            },
            columns: [
                { text: 'No. Hutang', datafield: 'hutang_no', columntype: 'textbox', width : 110, cellsalign : 'center', },
                { text: 'Tanggal', datafield: 'hutang_tgl',  cellsalign: 'center',  cellsformat: 'dd-MM-yyyy', width : 110 },
                { text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left' },
                { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left' },
                { text: 'Subtotal', datafield: 'hutangdet_tagihan',  cellsalign: 'right', cellsformat : 'F', width : 100 },
                { 
                    text: 'Dibayar', datafield: 'hutangdet_bayar',  cellsalign: 'right', cellsformat : 'F', width : 100, 
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
                url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=getmaklon",
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
                    $("#jadwalmaklongrid").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            maklon_no : element.maklon_no,
                            maklon_tgl : element.maklon_tgl,
                            hutang_no : element.hutang_no,
                            hutang_tgl : element.hutang_tgl,
                            maklon_no : element.maklon_no,
                            maklon_tgl : element.maklon_tgl,
                            rekanan_nama : element.rekanan_nama,
                            rekanan_alamat : element.rekanan_alamat,
                            barang_nama : element.barang_nama,
                            satuan_nama : element.satuan_nama,
                            maklondet_qty : element.maklondet_qty,
                            maklondet_total : element.maklondet_total,
                            t_hutanglunasdet_bayar : element.t_hutanglunasdet_bayar
                        };
                        $("#jadwalmaklongrid").jqxGrid('addrow', null, datarow);
                    });
                }
            });
        };
        // prepare the data
        var gridSource2 ={
            datatype: "json",
            datafields: [
                { name: 'maklon_no', type: 'string' },
                { name: 'maklon_tgl', type: 'date' },
                { name: 'maklon_no', type: 'string' },
                { name: 'maklon_tgl', type: 'date' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'barang_nama', type: 'string' },
                { name: 'satuan_nama', type: 'string' },
                { name: 'maklondet_qty', type: 'float' },
                { name: 'hutang_no', type: 'string' },
                { name: 'hutang_tgl', type: 'date' },
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=getmaklon&tanggal=" + tanggal
        };

        var gridAdapter2 = new $.jqx.dataAdapter(gridSource2);
        var tagih = [
            { 'penagihan' : '', 'maklon_text' : 'Semua'},
            { 'penagihan' : 'N', 'maklon_text' : 'Belum dibayar'},
            { 'penagihan' : 'Y', 'maklon_text' : 'Sudah dibayar'}
        ];
        $("#jadwalmaklongrid").jqxGrid({
            width: '100%',
            source: gridAdapter2,
            altrows: true,
            autoheight : true,
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=getrekanan",
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infohutang.php?action=getbarang",
                };
                var barangAdapter = new $.jqx.dataAdapter(barangSource2);
                $("#barangfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 28,});
                $("#tagih2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: tagih, displayMember: "maklon_text", valueMember: "penagihan", width: 120, height: 28,});

                $("#applyfilter2").jqxButton({ template: "primary", width: 80, height: 28 });
                $("#applyfilter2").on('click', function() {
                    var tanggal = $("#datefilter2").val();
                    var rekanan = $("#rekananfilter2").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter2").jqxDropDownList('getCheckedItems');
                    var tagih = $("#tagih2").jqxDropDownList('val');
                    applyfilter2(tanggal, rekanan, barang, tagih);
                });

            },
            columns: [
                { 
                    text: 'No. Maklon', datafield: 'maklon_no', columntype: 'textbox', width : 170, cellsalign : 'center',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalmaklongrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.maklon_no + "</br>";
                        html += moment(recorddata.maklon_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        html += "</div>";
                        return html;
                    },
                },
                { 
                    text: 'No. Pelunasan', datafield: 'hutang_no', columntype: 'textbox', width : 170, cellsalign : 'center',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalmaklongrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.hutang_no + "</br>";
                        if (recorddata.hutang_tgl !== '') {
                            html += moment(recorddata.hutang_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');    
                        }
                        html += "</div>";
                        return html;
                    },
                },
                { 
                    text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalmaklongrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.rekanan_nama + "</br>";
                        html += 'Alamat : ' + recorddata.rekanan_alamat + "</br>";
                        html += "</div>";
                        return html;
                    },
                },
                // { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left' },
                { 
                    text: 'Nama Barang', datafield: 'barang_nama',  cellsalign: 'left',
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#jadwalmaklongrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.barang_nama + "</br>";
                        html += "</div>";
                        return html;
                    },
                },
                { text: 'Satuan', datafield: 'satuan_nama',  cellsalign: 'left', width : 120 },
                { text: 'Qty', datafield: 'maklondet_qty',  cellsalign: 'right', width : 120 },
                { 
                    text: 'Tagihan', datafield: 'maklondet_total',  cellsalign: 'right', width : 120, cellformat : 'F',
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
                    text: 'Terbayar', datafield: 't_hutanglunasdet_bayar',  cellsalign: 'right', width : 120, cellformat : 'F',
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
                    <div id="jadwalmaklongrid"></div>
                </div>
            </div>
        </div>
    </div>
</section>