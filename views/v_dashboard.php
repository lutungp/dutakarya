<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<style>
    .book-notif {
        width:500px;
    }
    @media only screen and (max-width: 500px) {
        .book-notif {
            width: 80%;
        }
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="terimabrg">0</h3>

                <p>Penerimaan Barang</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
              <h3 id="kirimbrg">0</h3>

                <p>Pengiriman Barang</p>
              </div>
              <div class="icon">
                <i class="ion ion-outlet"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id="returbrg">0</h3>

                <p>Retur Barang</p>
              </div>
              <div class="icon">
                <i class="ion ion-arrow-return-left"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id="tagihbrg">0</h3>

                <p>Belum Ditagih</p>
              </div>
              <div class="icon">
                <i class="ion ion-information-circled"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div id="jadwalGrid"></div>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-body">
                        <div id="jadwalGrid2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        var now = new Date();
        applyfilter(moment(now).format('YYYY-MM-DD'), [], []);
        applyfilter2(moment(now).format('YYYY-MM-DD'), [], []);
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
        var tahunArr = ['2020', '2021', '2022', '2023', '2024', '2025', '2027', '2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040'];
        getSummaryToday(moment(now).format('YYYY-MM-DD'));
    });
    
    function applyfilter(tanggal, rekanan, barang) {
        var rekananArr2 = [];
        var rekananindex1 = [];
        var barangArr2 = [];
        var barangindex1 = [];
        var param = {
            tanggal : tanggal,
            rekanan : JSON.stringify(rekananArr2),
            barang : JSON.stringify(barangArr2),
            rit : 1
        };
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
        var barangSource2 = {
            datatype: "json",
            datafields: [
                { name: 'barang_id' },
                { name: 'barang_nama' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getbarang",
        };
        $.post("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=getjadwal", param, function(data, status){
            var source = {
                localdata: JSON.parse(data),
                datafields: [
                    { name: 'barang_nama', type: 'string' },
                    { name: 'hari', type: 'string' },
                    { name: 'jadwal_qty', type: 'float' },
                    { name: 'm_barang_id', type: 'int' },
                    { name: 'm_pegdriver_id', type: 'int' },
                    { name: 'm_pegdriver_nama', type: 'string' },
                    { name: 'm_peghelper_id', type: 'int' },
                    { name: 'm_peghelper_nama', type: 'string' },
                    { name: 'm_rekanan_id', type: 'int' },
                    { name: 'rekanan_nama', type: 'string' },
                    { name: 'm_satuan_id', type: 'int' },
                    { name: 'satuan_nama', type: 'string' },
                    { name: 'minggu', type: 'float' },
                    { name: 'rit', type: 'int' },
                    { name: 'sudahkirim', type: 'float' },
                ],
                datatype: "array"
            };
            $("#jadwalGrid").jqxGrid({
                width: '100%',
                source: source,
                showtoolbar: true,
                rendertoolbar: function (toolbar) {
                    var me = this;
                    var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                    toolbar.append(container);
                    container.append('<div id="datefilter" style="margin: 2px;"></div>');
                    container.append('<div id="rekananfilter" style="margin: 2px;"></div>');
                    container.append('<div id="barangfilter" style="margin: 2px;"></div>');
                    container.append('<div style="margin: 2px;"><input type="button" id="applyfilter" value="FILTER RIT 1" /></div>');
                    $("#datefilter").jqxDateTimeInput({ width: '150px', height: '25px', formatString: 'dd-MM-yyyy'});
                    $('#datefilter').jqxDateTimeInput('val', new Date(tanggal));
                    
                    var rekananAdapter2 = new $.jqx.dataAdapter(rekananSource2);
                    $("#rekananfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter2, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                    /* set rekanan yang dipilih sebelumnya */
                    rekananindex1.forEach(element => {
                        $("#rekananfilter").jqxDropDownList('checkIndex', element); 
                    });

                    var barangAdapter2 = new $.jqx.dataAdapter(barangSource2);
                    $("#barangfilter").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter2, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 150, height: 28,});
                    barangindex1.forEach(element => {
                        $("#barangfilter").jqxDropDownList('checkIndex', element); 
                    });

                    $("#applyfilter").jqxButton({ template: "primary", width: 120, height: 28 });
                    $("#applyfilter").on('click', function() {
                        var tanggal = $("#datefilter").val();
                        tanggal = moment(tanggal, 'DD-MM-YYYY').format('YYYY-MM-DD');
                        var rekanan = $("#rekananfilter").jqxDropDownList('getCheckedItems');
                        var barang = $("#barangfilter").jqxDropDownList('getCheckedItems');

                        var rekananArr2 = [];
                        rekananindex1 = [];
                        rekanan.forEach(function (elem) {
                            rekananArr2.push(elem.value)
                            rekananindex1.push(elem.index)
                        });
                        var barangArr2 = [];
                        barangindex1 = [];
                        barang.forEach(function (elem) {
                            barangArr2.push(elem.value)
                            barangindex1.push(elem.index)
                        });

                        var param = {
                            tanggal : tanggal,
                            rekanan : JSON.stringify(rekananArr2),
                            barang : JSON.stringify(barangArr2),
                            rit : 1
                        };

                        $.post("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=getjadwal", param, function(data, status){
                            source.localdata = JSON.parse(data);
                            $("#jadwalGrid").jqxGrid('updatebounddata', 'cells');
                        });
                    });
                },
                columns: [
                    {
                        text: 'No.', sortable: false, filterable: false, editable: false,
                        groupable: false, draggable: false, resizable: false,
                        datafield: '', columntype: 'number', width: 40,
                        cellsrenderer: function (row, column, value) {
                            return "<div style='margin:4px;text-align: center;'>" + (value + 1) + "</div>";
                        }
                    },
                    { text: 'Rekanan', datafield: 'rekanan_nama', width: 250 },
                    { text: 'Hari', datafield: 'hari', width: 60, cellsalign : 'center' },
                    { text: 'Minggu', datafield: 'minggu', cellsalign : 'center' },
                    { text: 'Barang', datafield: 'barang_nama', width: 140 },
                    { text: 'Rit', datafield: 'rit', width: 50, cellsalign : 'center' },
                    { text: 'Driver', datafield: 'm_pegdriver_nama', width: 180 },
                    { text: 'Helper', datafield: 'm_peghelper_nama', width: 180 },
                    { text: 'Satuan', datafield: 'satuan_nama', width: 80 },
                    { text: 'Qty', datafield: 'jadwal_qty', width: 50, cellsalign : 'right' },
                    { text: 'Kirim', datafield: 'sudahkirim', width: 50, cellsalign : 'right' },
                ]
            });
        });
    }

    function applyfilter2(tanggal, rekanan, barang) {
        var rekananArr2 = [];
        var rekananindex1 = [];
        var barangArr2 = [];
        var barangindex1 = [];
        var param = {
            tanggal : tanggal,
            rekanan : JSON.stringify(rekananArr2),
            barang : JSON.stringify(barangArr2),
            rit : 2
        };
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
        var barangSource2 = {
            datatype: "json",
            datafields: [
                { name: 'barang_id' },
                { name: 'barang_nama' }
            ],
            url: "<?php echo BASE_URL ?>/controllers/C_infopengiriman.php?action=getbarang",
        };
        $.post("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=getjadwal", param, function(data, status){
            var source = {
                localdata: JSON.parse(data),
                datafields: [
                    { name: 'barang_nama', type: 'string' },
                    { name: 'hari', type: 'string' },
                    { name: 'jadwal_qty', type: 'float' },
                    { name: 'm_barang_id', type: 'int' },
                    { name: 'm_pegdriver_id', type: 'int' },
                    { name: 'm_pegdriver_nama', type: 'string' },
                    { name: 'm_peghelper_id', type: 'int' },
                    { name: 'm_peghelper_nama', type: 'string' },
                    { name: 'm_rekanan_id', type: 'int' },
                    { name: 'rekanan_nama', type: 'string' },
                    { name: 'm_satuan_id', type: 'int' },
                    { name: 'satuan_nama', type: 'string' },
                    { name: 'minggu', type: 'float' },
                    { name: 'rit', type: 'int' },
                    { name: 'sudahkirim', type: 'float' },
                ],
                datatype: "array"
            };
            $("#jadwalGrid2").jqxGrid({
                width: '100%',
                source: source,
                showtoolbar: true,
                rendertoolbar: function (toolbar) {
                    var me = this;
                    var container = $("<div style='overflow: hidden; position: relative; margin: 2px;' class='row'></div>");
                    toolbar.append(container);
                    container.append('<div id="datefilter2" style="margin: 2px;"></div>');
                    container.append('<div id="rekananfilter2" style="margin: 2px;"></div>');
                    container.append('<div id="barangfilter2" style="margin: 2px;"></div>');
                    container.append('<div style="margin: 2px;"><input type="button" id="applyfilter2" value="FILTER RIT 2" /></div>');
                    $("#datefilter2").jqxDateTimeInput({ width: '150px', height: '25px', formatString: 'dd-MM-yyyy'});
                    $('#datefilter2').jqxDateTimeInput('val', new Date(tanggal));
                    
                    var rekananAdapter2 = new $.jqx.dataAdapter(rekananSource2);
                    $("#rekananfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: rekananAdapter2, checkboxes: true, displayMember: "rekanan_nama", valueMember: "rekanan_id", width: 200, height: 28,});
                    /* set rekanan yang dipilih sebelumnya */
                    rekananindex1.forEach(element => {
                        $("#rekananfilter2").jqxDropDownList('checkIndex', element); 
                    });

                    var barangAdapter2 = new $.jqx.dataAdapter(barangSource2);
                    $("#barangfilter2").jqxDropDownList({ selectedIndex: 0, autoOpen: true, source: barangAdapter2, checkboxes: true, displayMember: "barang_nama", valueMember: "barang_id", width: 150, height: 28,});
                    barangindex1.forEach(element => {
                        $("#barangfilter2").jqxDropDownList('checkIndex', element); 
                    });

                    $("#applyfilter2").jqxButton({ template: "primary", width: 120, height: 28 });
                    $("#applyfilter2").on('click', function() {
                        var tanggal = $("#datefilter2").val();
                        tanggal = moment(tanggal, 'DD-MM-YYYY').format('YYYY-MM-DD');
                        var rekanan = $("#rekananfilter2").jqxDropDownList('getCheckedItems');
                        var barang = $("#barangfilter2").jqxDropDownList('getCheckedItems');

                        var rekananArr2 = [];
                        rekananindex1 = [];
                        rekanan.forEach(function (elem) {
                            rekananArr2.push(elem.value)
                            rekananindex1.push(elem.index)
                        });
                        var barangArr2 = [];
                        barangindex1 = [];
                        barang.forEach(function (elem) {
                            barangArr2.push(elem.value)
                            barangindex1.push(elem.index)
                        });

                        var param = {
                            tanggal : tanggal,
                            rekanan : JSON.stringify(rekananArr2),
                            barang : JSON.stringify(barangArr2),
                            rit : 2
                        };

                        $.post("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=getjadwal", param, function(data, status){
                            source.localdata = JSON.parse(data);
                            $("#jadwalGrid2").jqxGrid('updatebounddata', 'cells');
                        });
                    });
                },
                columns: [
                    {
                        text: 'No.', sortable: false, filterable: false, editable: false,
                        groupable: false, draggable: false, resizable: false,
                        datafield: '', columntype: 'number', width: 40,
                        cellsrenderer: function (row, column, value) {
                            return "<div style='margin:4px;text-align: center;'>" + (value + 1) + "</div>";
                        }
                    },
                    { text: 'Rekanan', datafield: 'rekanan_nama', width: 250 },
                    { text: 'Hari', datafield: 'hari', width: 60, cellsalign : 'center' },
                    { text: 'Minggu', datafield: 'minggu', cellsalign : 'center' },
                    { text: 'Barang', datafield: 'barang_nama', width: 140 },
                    { text: 'Rit', datafield: 'rit', width: 50, cellsalign : 'center' },
                    { text: 'Driver', datafield: 'm_pegdriver_nama', width: 180 },
                    { text: 'Helper', datafield: 'm_peghelper_nama', width: 180 },
                    { text: 'Satuan', datafield: 'satuan_nama', width: 80 },
                    { text: 'Qty', datafield: 'jadwal_qty', width: 50, cellsalign : 'right' },
                    { text: 'Kirim', datafield: 'sudahkirim', width: 50, cellsalign : 'right' },
                ]
            });
        });
    }

    function getSummaryToday(tanggal) {
        $.get("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=getsummary&tanggal="+tanggal, function(data, status){
            var res = JSON.parse(data);
            $('#terimabrg').html(res.totalkirim);
            $('#kirimbrg').html(res.totalterima);
            $('#returbrg').html(res.totalretur);
        });
    }
</script>