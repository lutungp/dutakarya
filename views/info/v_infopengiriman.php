<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>

<script type="text/javascript">
    $(document).ready(function () {
        var now = new Date();
        renderGrid(moment(now).format('YYYY-MM-DD'));
        renderGrid2();
        renderGrid3();
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
                url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getpengiriman",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr : rekananArr,
                    barangArr : barangArr
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#jadwalkirimgrid2").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            pengiriman_id : element.pengiriman_id,
                            pengiriman_no : element.pengiriman_no,
                            pengiriman_tgl : element.pengiriman_tgl,
                            m_rekanan_id : element.m_rekanan_id,
                            rekanan_nama : element.rekanan_nama,
                            m_barang_id : element.m_barang_id,
                            barang_nama : element.barang_nama,
                            satuan_nama : element.satuan_nama,
                            pengirimandet_qty : element.pengirimandet_qty,
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
                { name: 'pengiriman_id', type: 'int' },
                { name: 'pengiriman_no', type: 'string' },
                { name: 'pengiriman_tgl', type: 'date' },
                { name: 'm_rekanan_id', type: 'int' },
                { name: 'rekanan_nama', type: 'string' },
                { name: 'm_barang_id', type: 'int' },
                { name: 'barang_nama', type: 'string' },
                { name: 'satuan_nama', type: 'string' },
                { name: 'pengirimandet_qty', type: 'float' },
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getpengiriman&tanggal=" + tanggal
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
                container.append('<div id="barangfilter" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter" value="FILTER" /></div>');
                // container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport" /></div>');
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getrekanan",
                    async: false
                };
                var rekananAdapter = new $.jqx.dataAdapter(rekananSource);
                $("#rekananfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                var barangSource = {
                    datatype: "json",
                    datafields: [
                        { name: 'barang_id' },
                        { name: 'barang_nama' }
                    ],
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getbarang",
                };
                var barangAdapter = new $.jqx.dataAdapter(barangSource);
                $("#barangfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 28,});

                $("#applyfilter").jqxButton({ template: "primary", width: 120, height: 28 });
                $("#applyfilter").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter").jqxDropDownList('getCheckedItems');
                    applyfilter(tanggal, rekanan, barang);
                });

                $("#excelExport").jqxButton({ width: 120, height: 28 });
                $("#excelExport").click(function () {
                    $("#grid").jqxGrid('exportdata', 'xlsx', 'jqxGrid');
                });
            },
            columns: [
                { text: 'No. Pengiriman', datafield: 'pengiriman_no', columntype: 'textbox', width : 170, cellsalign : 'center' },
                { text: 'Tanggal', datafield: 'pengiriman_tgl',  cellsalign: 'center',  cellsformat: 'dd-MM-yyyy', width : 170 },
                { text: 'Rekanan', datafield: 'rekanan_nama',  cellsalign: 'left' },
                { text: 'Nama Barang', datafield: 'barang_nama',  cellsalign: 'left' },
                { text: 'Satuan', datafield: 'satuan_nama',  cellsalign: 'left', width : 120 },
                { text: 'Qty', datafield: 'pengirimandet_qty',  cellsalign: 'right', width : 120 },
            ]
        });
    }

    function renderGrid2() {
        var applyfilter2 = function (tanggal, rekanan, barang, hari, bulan, tahun) {
            var rekananArr2 = [];
            rekanan.forEach(function (elem) {
                rekananArr2.push(elem.value)
            });
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getjadwalkirim",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr2 : rekananArr2,
                    barang_id : barang,
                    rit : 1,
                    hari : hari,
                    bulan : bulan,
                    tahun : tahun
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#jadwalkirimgrid2").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            rekanan_nama : element.rekanan_nama,
                            rekanan_alamat : element.rekanan_alamat,
                            DO_minggu1 : element.DO_minggu1,
                            Isi_minggu1 : element.Isi_minggu1,
                            Kosong_minggu1 : element.Kosong_minggu1,
                            Sisa_minggu1 : element.Sisa_minggu1,
                            DO_minggu2 : element.DO_minggu2,
                            Isi_minggu2 : element.Isi_minggu2,
                            Kosong_minggu2 : element.Kosong_minggu2,
                            Sisa_minggu2 : element.Sisa_minggu2,
                            DO_minggu3 : element.DO_minggu3,
                            Isi_minggu3 : element.Isi_minggu3,
                            Kosong_minggu3 : element.Kosong_minggu3,
                            Sisa_minggu3 : element.Sisa_minggu3,
                            DO_minggu4 : element.DO_minggu4,
                            Isi_minggu4 : element.Isi_minggu4,
                            Kosong_minggu4 : element.Kosong_minggu4,
                            Sisa_minggu4 : element.Sisa_minggu4,
                            DO_minggu5 : element.DO_minggu5,
                            Isi_minggu5 : element.Isi_minggu5,
                            Kosong_minggu5 : element.Kosong_minggu5,
                            Sisa_minggu5 : element.Sisa_minggu5,
                        };
                        $("#jadwalkirimgrid2").jqxGrid('addrow', null, datarow);
                    });
                }
            });
        };
        // prepare the data
        var gridSource2 ={
            datatype: "json",
            datafields: [
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'DO_minggu1', type: 'float' },
                { name: 'Isi_minggu1', type: 'float' },
                { name: 'Kosong_minggu1', type: 'float' },
                { name: 'Sisa_minggu1', type: 'float' },
                { name: 'DO_minggu2', type: 'float' },
                { name: 'Isi_minggu2', type: 'float' },
                { name: 'Kosong_minggu2', type: 'float' },
                { name: 'Sisa_minggu2', type: 'float' },
                { name: 'DO_minggu3', type: 'float' },
                { name: 'Isi_minggu3', type: 'float' },
                { name: 'Kosong_minggu3', type: 'float' },
                { name: 'Sisa_minggu3', type: 'float' },
                { name: 'DO_minggu4', type: 'float' },
                { name: 'Isi_minggu4', type: 'float' },
                { name: 'Kosong_minggu4', type: 'float' },
                { name: 'Sisa_minggu4', type: 'float' },
                { name: 'DO_minggu5', type: 'float' },
                { name: 'Isi_minggu5', type: 'float' },
                { name: 'Kosong_minggu5', type: 'float' },
                { name: 'Sisa_minggu5', type: 'float' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getjadwalkirim"
        };
        var bulan = [
            { 'bulan_id' : 1, 'bulan_nama' : 'Januari'},
            { 'bulan_id' : 2, 'bulan_nama' : 'Februari'},
            { 'bulan_id' : 3, 'bulan_nama' : 'Maret'},
            { 'bulan_id' : 4, 'bulan_nama' : 'April'},
            { 'bulan_id' : 5, 'bulan_nama' : 'Mei'},
            { 'bulan_id' : 6, 'bulan_nama' : 'Juni'},
            { 'bulan_id' : 7, 'bulan_nama' : 'Juli'},
            { 'bulan_id' : 8, 'bulan_nama' : 'Agustus'},
            { 'bulan_id' : 9, 'bulan_nama' : 'September'},
            { 'bulan_id' : 10, 'bulan_nama' : 'Oktober'},
            { 'bulan_id' : 11, 'bulan_nama' : 'November'},
            { 'bulan_id' : 12, 'bulan_nama' : 'Desember'},
        ];
        var hari = [
            { 'hari_id' : 1, 'hari_nama' : 'Senin'},
            { 'hari_id' : 2, 'hari_nama' : 'Selasa'},
            { 'hari_id' : 3, 'hari_nama' : 'Rabu'},
            { 'hari_id' : 4, 'hari_nama' : 'Kamis'},
            { 'hari_id' : 5, 'hari_nama' : 'Jumat'},
            { 'hari_id' : 6, 'hari_nama' : 'Sabtu'},
            { 'hari_id' : 7, 'hari_nama' : 'Minggu'},
        ];
        var tahun = ['2020', '2021', '2022', '2023', '2024', '2025', '2027', '2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040'];
        var gridAdapter2 = new $.jqx.dataAdapter(gridSource2);
        $("#jadwalkirimgrid2").jqxGrid({
            width: '100%',
            source: gridAdapter2,
            altrows: true,
            showtoolbar: true,
            columnsresize: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="harifilter2" style="margin: 2px;"></div>');
                container.append('<div id="bulanfilter2" style="margin: 2px;"></div>');
                container.append('<div id="tahunfilter2" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter2" style="margin: 2px;"></div>');
                container.append('<div id="barangfilter2" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter2" value="FILTER RIT 1" /></div>');
                // container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport2" /></div>');
                // var bulanAdapter2 = new $.jqx.dataAdapter(bulanSource2);
                $("#harifilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: hari, displayMember: "hari_nama", valueMember: "hari_id", width: 150, height: 28,});
                $("#bulanfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: bulan, displayMember: "bulan_nama", valueMember: "bulan_id", width: 150, height: 28,});
                $("#tahunfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: tahun, width: 200, height: 28,});
                var rekananSource2 = {
                    datatype: "json",
                    datafields: [
                        { name: 'rekanan_id' },
                        { name: 'rekanan_nama' }
                    ],
                    id: 'id',
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getrekanan",
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
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getbarang",
                };
                var barangAdapter2 = new $.jqx.dataAdapter(barangSource2);
                $("#barangfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter2, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 28,});

                $("#applyfilter2").jqxButton({ template: "primary", width: 120, height: 28 });
                $("#applyfilter2").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter2").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter2").jqxDropDownList('val');
                    var hari = $("#harifilter2").jqxDropDownList('val');
                    var bulan = $("#bulanfilter2").jqxDropDownList('val');
                    var tahun = $("#tahunfilter2").jqxDropDownList('val');
                    applyfilter2(tanggal, rekanan, barang, hari, bulan, tahun);
                });

                // $("#excelExport2").jqxButton({ width: 120, height: 28 });
                // $("#excelExport2").click(function () {
                //     $("#jadwalkirimgrid2").jqxGrid('exportdata', 'xlsx', 'jqxGrid');           
                // });
            },
            columns: [
                { text: 'Rekanan', datafield: 'rekanan_nama', cellsalign : 'left', width : 150 },
                { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left', width : 180 },
                { text: 'DO', datafield : 'DO_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'DO', datafield : 'DO_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'DO', datafield : 'DO_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'DO', datafield : 'DO_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'DO', datafield : 'DO_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu5', columngroup: 'minggu5', width : 52 },
            ],
            columngroups: [
                { text: 'Minggu 1', align : 'center', name: 'minggu1' },
                { text: 'Minggu 2', align : 'center', name: 'minggu2' },
                { text: 'Minggu 3', align : 'center', name: 'minggu3' },
                { text: 'Minggu 4', align : 'center', name: 'minggu4' },
                { text: 'Minggu 5', align : 'center', name: 'minggu5' },
            ]
        });
    }

    function renderGrid3() {
        var applyfilter3 = function (tanggal, rekanan, barang, hari, bulan, tahun) {
            var rekananArr3 = [];
            rekanan.forEach(function (elem) {
                rekananArr3.push(elem.value)
            });
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getjadwalkirim",
                type: "post",
                datatype : 'json',
                data: {
                    tanggal : tanggal,
                    rekananArr3 : rekananArr3,
                    barang_id : barang,
                    rit : 2,
                    hari : hari,
                    bulan : bulan,
                    tahun : tahun
                },
                success : function (res) {
                    res = JSON.parse(res);
                    $("#jadwalkirimgrid3").jqxGrid('clear');
                    res.forEach(element => {
                        let datarow = {
                            rekanan_nama : element.rekanan_nama,
                            rekanan_alamat : element.rekanan_alamat,
                            DO_minggu1 : element.DO_minggu1,
                            Isi_minggu1 : element.Isi_minggu1,
                            Kosong_minggu1 : element.Kosong_minggu1,
                            Sisa_minggu1 : element.Sisa_minggu1,
                            DO_minggu2 : element.DO_minggu2,
                            Isi_minggu2 : element.Isi_minggu2,
                            Kosong_minggu2 : element.Kosong_minggu2,
                            Sisa_minggu2 : element.Sisa_minggu2,
                            DO_minggu3 : element.DO_minggu3,
                            Isi_minggu3 : element.Isi_minggu3,
                            Kosong_minggu3 : element.Kosong_minggu3,
                            Sisa_minggu3 : element.Sisa_minggu3,
                            DO_minggu4 : element.DO_minggu4,
                            Isi_minggu4 : element.Isi_minggu4,
                            Kosong_minggu4 : element.Kosong_minggu4,
                            Sisa_minggu4 : element.Sisa_minggu4,
                            DO_minggu5 : element.DO_minggu5,
                            Isi_minggu5 : element.Isi_minggu5,
                            Kosong_minggu5 : element.Kosong_minggu5,
                            Sisa_minggu5 : element.Sisa_minggu5,
                        };
                        $("#jadwalkirimgrid3").jqxGrid('addrow', null, datarow);
                    });
                }
            });
        };
        // prepare the data
        var gridSource2 ={
            datatype: "json",
            datafields: [
                { name: 'rekanan_nama', type: 'string' },
                { name: 'rekanan_alamat', type: 'string' },
                { name: 'DO_minggu1', type: 'float' },
                { name: 'Isi_minggu1', type: 'float' },
                { name: 'Kosong_minggu1', type: 'float' },
                { name: 'Sisa_minggu1', type: 'float' },
                { name: 'DO_minggu2', type: 'float' },
                { name: 'Isi_minggu2', type: 'float' },
                { name: 'Kosong_minggu2', type: 'float' },
                { name: 'Sisa_minggu2', type: 'float' },
                { name: 'DO_minggu3', type: 'float' },
                { name: 'Isi_minggu3', type: 'float' },
                { name: 'Kosong_minggu3', type: 'float' },
                { name: 'Sisa_minggu3', type: 'float' },
                { name: 'DO_minggu4', type: 'float' },
                { name: 'Isi_minggu4', type: 'float' },
                { name: 'Kosong_minggu4', type: 'float' },
                { name: 'Sisa_minggu4', type: 'float' },
                { name: 'DO_minggu5', type: 'float' },
                { name: 'Isi_minggu5', type: 'float' },
                { name: 'Kosong_minggu5', type: 'float' },
                { name: 'Sisa_minggu5', type: 'float' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getjadwalkirim"
        };
        var bulan = [
            { 'bulan_id' : 1, 'bulan_nama' : 'Januari'},
            { 'bulan_id' : 2, 'bulan_nama' : 'Februari'},
            { 'bulan_id' : 3, 'bulan_nama' : 'Maret'},
            { 'bulan_id' : 4, 'bulan_nama' : 'April'},
            { 'bulan_id' : 5, 'bulan_nama' : 'Mei'},
            { 'bulan_id' : 6, 'bulan_nama' : 'Juni'},
            { 'bulan_id' : 7, 'bulan_nama' : 'Juli'},
            { 'bulan_id' : 8, 'bulan_nama' : 'Agustus'},
            { 'bulan_id' : 9, 'bulan_nama' : 'September'},
            { 'bulan_id' : 10, 'bulan_nama' : 'Oktober'},
            { 'bulan_id' : 11, 'bulan_nama' : 'November'},
            { 'bulan_id' : 12, 'bulan_nama' : 'Desember'},
        ];
        var hari = [
            { 'hari_id' : 1, 'hari_nama' : 'Senin'},
            { 'hari_id' : 2, 'hari_nama' : 'Selasa'},
            { 'hari_id' : 3, 'hari_nama' : 'Rabu'},
            { 'hari_id' : 4, 'hari_nama' : 'Kamis'},
            { 'hari_id' : 5, 'hari_nama' : 'Jumat'},
            { 'hari_id' : 6, 'hari_nama' : 'Sabtu'},
            { 'hari_id' : 7, 'hari_nama' : 'Minggu'},
        ];
        var tahun = ['2020', '2021', '2022', '2023', '2024', '2025', '2027', '2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040'];
        var gridAdapter2 = new $.jqx.dataAdapter(gridSource2);
        $("#jadwalkirimgrid3").jqxGrid({
            width: '100%',
            source: gridAdapter2,
            altrows: true,
            showtoolbar: true,
            columnsresize: true,
            rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                toolbar.append(container);
                container.append('<div id="harifilter3" style="margin: 2px;"></div>');
                container.append('<div id="bulanfilter3" style="margin: 2px;"></div>');
                container.append('<div id="tahunfilter3" style="margin: 2px;"></div>');
                container.append('<div id="rekananfilter3" style="margin: 2px;"></div>');
                container.append('<div id="barangfilter3" style="margin: 2px;"></div>');
                container.append('<div style="margin: 2px;"><input type="button" id="applyfilter3" value="FILTER RIT 2" /></div>');
                // container.append('<div style="margin: 2px;"><input type="button" value="EXCEL" id="excelExport2" /></div>');
                // var bulanAdapter2 = new $.jqx.dataAdapter(bulanSource2);
                $("#harifilter3").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: hari, displayMember: "hari_nama", valueMember: "hari_id", width: 150, height: 28,});
                $("#bulanfilter3").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: bulan, displayMember: "bulan_nama", valueMember: "bulan_id", width: 150, height: 28,});
                $("#tahunfilter3").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: tahun, width: 200, height: 28,});
                var rekananSource2 = {
                    datatype: "json",
                    datafields: [
                        { name: 'rekanan_id' },
                        { name: 'rekanan_nama' }
                    ],
                    id: 'id',
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getrekanan",
                    async: false
                };
                var rekananAdapter2 = new $.jqx.dataAdapter(rekananSource2);
                $("#rekananfilter3").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter2, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                var barangSource2 = {
                    datatype: "json",
                    datafields: [
                        { name: 'barang_id' },
                        { name: 'barang_nama' }
                    ],
                    url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getbarang",
                };
                var barangAdapter2 = new $.jqx.dataAdapter(barangSource2);
                $("#barangfilter3").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter2, displayMember: "barang_nama", valueMember: "barang_id", width: 200, height: 28,});

                $("#applyfilter3").jqxButton({ template: "primary", width: 120, height: 28 });
                $("#applyfilter3").on('click', function() {
                    var tanggal = $("#datefilter").val();
                    var rekanan = $("#rekananfilter3").jqxDropDownList('getCheckedItems');
                    var barang = $("#barangfilter3").jqxDropDownList('val');
                    var hari = $("#harifilter3").jqxDropDownList('val');
                    var bulan = $("#bulanfilter3").jqxDropDownList('val');
                    var tahun = $("#tahunfilter3").jqxDropDownList('val');
                    applyfilter3(tanggal, rekanan, barang, hari, bulan, tahun);
                });

                // $("#excelExport2").jqxButton({ width: 120, height: 28 });
                // $("#excelExport2").click(function () {
                //     $("#jadwalkirimgrid3").jqxGrid('exportdata', 'xlsx', 'jqxGrid');           
                // });
            },
            columns: [
                { text: 'Rekanan', datafield: 'rekanan_nama', cellsalign : 'left', width : 150 },
                { text: 'Alamat', datafield: 'rekanan_alamat',  cellsalign: 'left', width : 180 },
                { text: 'DO', datafield : 'DO_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu1', columngroup: 'minggu1', width : 52 },
                { text: 'DO', datafield : 'DO_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu2', columngroup: 'minggu2', width : 52 },
                { text: 'DO', datafield : 'DO_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu3', columngroup: 'minggu3', width : 52 },
                { text: 'DO', datafield : 'DO_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu4', columngroup: 'minggu4', width : 52 },
                { text: 'DO', datafield : 'DO_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Isi', datafield : 'Isi_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Kosong', datafield : 'Kosong_minggu5', columngroup: 'minggu5', width : 52 },
                { text: 'Sisa', datafield : 'Sisa_minggu5', columngroup: 'minggu5', width : 52 },
            ],
            columngroups: [
                { text: 'Minggu 1', align : 'center', name: 'minggu1' },
                { text: 'Minggu 2', align : 'center', name: 'minggu2' },
                { text: 'Minggu 3', align : 'center', name: 'minggu3' },
                { text: 'Minggu 4', align : 'center', name: 'minggu4' },
                { text: 'Minggu 5', align : 'center', name: 'minggu5' },
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
                    <div id="jadwalkirimgrid2"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <div></div>
                    <div id="jadwalkirimgrid3"></div>
                </div>
            </div>
        </div>
    </div>
</section>