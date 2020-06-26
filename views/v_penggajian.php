<link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdata.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.filter.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.sort.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.pager.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.edit.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/scripts/demos.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        bsCustomFileInput.init();
        var filesize = 0;
        $('#exampleInputFile').bind('change', function() {
            filesize = this.files[0].size;
        });
        var url = "<?php echo BASE_URL ?>/controllers/C_penggajian.php?action=getdatapenggajian";
        // prepare the data
        var source = {
            datatype: "json",
            datafields: [
                {name: 'gaji_id', type: 'int'},
                {name: 'gaji_bulan', type: 'int'},
                {name: 'gaji_tahun', type: 'int'},
                {name: 'm_user_id', type: 'int'},
                {name: 'gaji_pokok', type: 'float'},
                {name: 'gaji_kehadiran', type: 'int'},
                {name: 'gaji_transport', type: 'float'},
                {name: 'gaji_operasional', type: 'float'},
                {name: 'gaji_rapel', type: 'float'},
                {name: 'gaji_jasalayanan', type: 'float'},
                {name : 'gaji_jasalayanan', type : 'float'},
                {name : 'gaji_shift', type : 'float'},
                {name : 'gaji_lembur', type : 'float'},
                {name : 'gaji_oncall', type : 'float'},
                {name : 'gaji_uangcuti', type : 'float'},
                {name : 'gaji_potsimwa', type : 'float'},
                {name : 'gaji_potkoperasi', type : 'float'},
                {name : 'gaji_potparkir', type : 'float'},
                {name : 'gaji_potsimpok', type : 'float'},
                {name : 'gaji_potkesehatan', type : 'float'},
                {name : 'gaji_potabsensi', type : 'float'},
                {name : 'gaji_potzis', type : 'float'},
                {name : 'gaji_potqurban', type : 'float'},
                {name : 'gaji_potinfaqmasjid', type : 'float'},
                {name : 'gaji_potbpjstk', type : 'float'},
                {name : 'gaji_potbpjspensiun', type : 'float'},
                {name : 'gaji_potpajak', type : 'float'},
                {name : 'gaji_potsekolah', type : 'float'},
                {name : 'gaji_potlain', type : 'float'},
                {name : 'gaji_potfkk', type : 'float'},
                {name : 'gaji_potsp', type : 'float'},
                {name : 'gaji_potibi', type : 'float'},
                {name : 'gaji_nilai', type : 'float'},
                {name : 'gaji_insentif', type : 'float'},
                {name : 'gaji_potongan', type : 'float'},
                {name : 'gaji_diterima', type : 'float'},
                {name : 'gaji_view_count', type : 'float'},
                {name : 'gaji_created_date', type : 'date'},
                {name : 'gaji_nama', type : 'string'},
                {name : 'gaji_nopeg', type : 'string'},
                {name : 'gaji_unitpeg', type : 'string'},
                {name : 'gaji_golpangkat_peg', type : 'string'},
                {name : 'gaji_norekening', type : 'string'},
            ],
            id: 'gaji_id',
            url: url,
            updaterow: function (rowid, rowdata, commit) {
                // synchronize with the server - send update command
                // call commit with parameter true if the synchronization with the server is successful 
                // and with parameter false if the synchronization failder.
                commit(true);
            },
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
            $("#grid").jqxGrid('addfilter', 'gaji_nama', filtergroup);
            $("#grid").jqxGrid('addfilter', 'gaji_unitpeg', filtergroup);
            // // apply the filters.
            $("#grid").jqxGrid('applyfilters');
        }
        var dataAdapter = new $.jqx.dataAdapter(source, {
            formatData: function (data) {
                $.extend(data, {
                    bulan: $("#bulan").val(),
                    tahun: $("#tahun").val()
                });
                return data;
            },
            ready: function () {
                addfilter();
            },
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        var bulan = ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
        $("#grid").jqxGrid({
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            autoheight: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            selectionmode: 'multiplecellsadvanced',
            filterable: true,
            columns: [
                { text: 'Gaji Bulan', datafield: 'gaji_bulan', width : '100',  filterable: false,
                cellsrenderer: function (rec, field, val) {
                    return "&nbsp;" + bulan[val];
                }},
                { text: 'Gaji Tahun', datafield: 'gaji_tahun', width : '100',  filterable: false},
                { text: 'No. Pegawai', datafield: 'gaji_nopeg', width : '100'},
                { text: 'Nama', datafield: 'gaji_nama', width : '200'},
                { text: 'Unit', datafield: 'gaji_unitpeg'},
                { text: 'Golongan', datafield: 'gaji_golpangkat_peg'},
                { text: 'Action', datafield: 'view', columntype: 'button', width:'60', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return 'View';
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        viewSlipGaji(dataRecord);
                    }
                }
            ]
        });

        $("#btn-filter").on("click", function () {
            $('#grid').jqxGrid('updatebounddata', 'cells');
        });

        $('#uploadFile').on('submit', function(event){
            event.preventDefault();
            move(filesize);
            $.ajax({  
                    url: "<?php echo BASE_URL ?>/controllers/C_penggajian.php?action=importgaji&bulan=" + $("#bulan").val() + "&tahun=" + $("#tahun").val(),
                    method:"POST",
                    data:new FormData(this),
                    contentType:false,
                    processData:false,
                    async: true,
                    success:function(data){
                        move(0)
                        $("#myProgress").css("display", "none");
                        if(data == 202){
                            swal({
                                title: "Sorry",
                                text: "Gaji bulan " + $("#bulan").val() + " " + $("#tahun").val() + ", sudah diimport. silahkan hubungi administrator !",
                                icon: "warning"
                            })
                        } else {
                            swal("Info!", "Bulan " + $("#bulan").val() + " " + $("#tahun").val() + " Berhasil diimport", "success");
                        }
                        $('#grid').jqxGrid('updatebounddata', 'cells');
                    }  
            });
        });

        $("#btn-template").on("click", function () {
            window.open("<?php echo BASE_URL ?>/assets/gaji_tmp.xlsx");
        });

    });

    var i = 0;
    function move(filesize) {
        $("#myProgress").css("display", "block");
        if (i == 0) {
            i = 1;
            var elem = document.getElementById("myBar");
            var width = 1;
            var id = setInterval(frame, (filesize/100));
            function frame() {
                if (width >= 100) {
                    clearInterval(id);
                    i = 0;
                } else {
                    width++;
                    elem.style.width = width + "%";
                }
            }
        }
    }

    function resetForm() {

    }

    function viewSlipGaji(dataRecord) {
        resetForm();
        $("#readgaji").css('display', 'block');
        $("#listgaji").css('display', 'none');
        $("#tglgajih").html(moment(dataRecord.gaji_created_date, 'YYYY-MM-DD hh:mm:ss').format('DD-MM-YYYY hh:mm:ss'));
        var gaji_pokok  = parseFloat(dataRecord.gaji_pokok||0);
        $("#gajirshaji").html(gaji_pokok > 0 ? Number(gaji_pokok).toLocaleString() : '-');
        
        var gaji_jasalayanan  = parseFloat(dataRecord.gaji_jasalayanan||0);
        $("#jasayan").html(gaji_jasalayanan > 0 ? Number(gaji_jasalayanan).toLocaleString() : '-');

        $("#kehadiran").html(dataRecord.gaji_kehadiran);

        var gaji_shift  = parseFloat(dataRecord.gaji_shift||0);
        $("#shift").html(gaji_shift > 0 ? Number(gaji_shift).toLocaleString() : '-');

        var gaji_transport  = parseFloat(dataRecord.gaji_transport||0);
        $("#transport").html(gaji_transport > 0 ? Number(gaji_transport).toLocaleString() : '-');

        var gaji_lembur  = parseFloat(dataRecord.gaji_lembur||0);
        $("#lembur").html(gaji_lembur > 0 ? Number(gaji_lembur).toLocaleString() : '-');

        var gaji_operasional  = parseFloat(dataRecord.gaji_operasional||0);
        $("#operasional").html(gaji_operasional > 0 ? Number(gaji_operasional).toLocaleString() : '-');

        var gaji_oncall  = parseFloat(dataRecord.gaji_oncall||0);
        $("#oncall").html(gaji_oncall > 0 ? Number(gaji_oncall).toLocaleString() : '-');

        var gaji_rapel  = parseFloat(dataRecord.gaji_rapel||0);
        $("#rapelmod").html(gaji_rapel > 0 ? Number(gaji_rapel).toLocaleString() : '-');

        var gaji_uangcuti  = parseFloat(dataRecord.gaji_uangcuti||0);
        $("#uangcuti").html(gaji_uangcuti > 0 ? Number(gaji_uangcuti).toLocaleString() : '-');
        
        var gaji_potsimwa  = parseFloat(dataRecord.gaji_potsimwa||0);
        $("#simwa").html(gaji_potsimwa > 0 ? Number(gaji_potsimwa).toLocaleString() : '-');

        var gaji_potbpjstk  = parseFloat(dataRecord.gaji_potbpjstk||0);
        $("#bpjstk").html(gaji_potbpjstk > 0 ? Number(gaji_potbpjstk).toLocaleString() : '-');

        var gaji_potkoperasi  = parseFloat(dataRecord.gaji_potkoperasi||0);
        $("#koperasi").html(gaji_potkoperasi > 0 ? Number(gaji_potkoperasi).toLocaleString() : '-');

        var gaji_potbpjspensiun  = parseFloat(dataRecord.gaji_potbpjspensiun||0);
        $("#Pensiun").html(gaji_potbpjspensiun > 0 ? Number(gaji_potbpjspensiun).toLocaleString() : '-');

        var gaji_potparkir  = parseFloat(dataRecord.gaji_potparkir||0);
        $("#parkir").html(gaji_potparkir > 0 ? Number(gaji_potparkir).toLocaleString() : '-');

        var gaji_potpajak = parseFloat(dataRecord.gaji_potpajak||0);
        $("#pajak").html(gaji_potpajak > 0 ? Number(gaji_potpajak).toLocaleString() : '-');

        var gaji_potsimpok = parseFloat(dataRecord.gaji_potsimpok||0);
        $("#simpok").html(gaji_potsimpok > 0 ? Number(gaji_potsimpok).toLocaleString() : '-');

        var gaji_potsekolah = parseFloat(dataRecord.gaji_potsekolah||0);
        $("#sekolah").html(gaji_potsekolah > 0 ? Number(gaji_potsekolah).toLocaleString() : '-');

        var gaji_potkesehatan = parseFloat(dataRecord.gaji_potkesehatan||0);
        $("#kesehatan").html(gaji_potkesehatan > 0 ? Number(gaji_potkesehatan).toLocaleString() : '-');

        var gaji_potlain = parseFloat(dataRecord.gaji_potlain||0);
        $("#lainlain").html(gaji_potlain > 0 ? Number(gaji_potlain).toLocaleString() : '-');

        var gaji_potabsensi = parseFloat(dataRecord.gaji_potabsensi||0);
        $("#absensi").html(gaji_potabsensi > 0 ? Number(gaji_potabsensi).toLocaleString() : '-');

        var gaji_potfkk = parseFloat(dataRecord.gaji_potfkk||0);
        $("#fkk").html(gaji_potfkk > 0 ? Number(gaji_potfkk).toLocaleString() : '-');

        var gaji_potzis = parseFloat(dataRecord.gaji_potzis||0);
        $("#zis").html(gaji_potzis > 0 ? Number(gaji_potzis).toLocaleString() : '-');

        var gaji_potibi = parseFloat(dataRecord.gaji_potibi||0);
        $("#ibi").html(gaji_potibi > 0 ? Number(gaji_potibi).toLocaleString() : '-');

        var gaji_potqurban = parseFloat(dataRecord.gaji_potqurban||0);
        $("#qurban").html(gaji_potqurban > 0 ? Number(gaji_potqurban).toLocaleString() : '-');

        var gaji_potsp = parseFloat(dataRecord.gaji_potsp||0);
        $("#sp").html(gaji_potsp > 0 ? Number(gaji_potsp).toLocaleString() : '-');

        var gaji_potinfaqmasjid = parseFloat(dataRecord.gaji_potinfaqmasjid||0);
        $("#masjid").html(gaji_potinfaqmasjid > 0 ? Number(gaji_potinfaqmasjid).toLocaleString() : '-');

        var totalgajitunj = gaji_pokok + gaji_transport + gaji_operasional + gaji_rapel;
        $("#totalgajitunj").html(totalgajitunj > 0 ? Number(totalgajitunj).toLocaleString() : '-');

        var totalinsentif = gaji_jasalayanan + gaji_shift + gaji_lembur + gaji_oncall + gaji_uangcuti;
        $("#totalinsentif").html(totalinsentif > 0 ? Number(totalinsentif).toLocaleString() : '-');

        var totalpotongan =  gaji_potsimwa + gaji_potbpjstk + gaji_potkoperasi + gaji_potbpjspensiun + gaji_potparkir + gaji_potpajak + gaji_potsimpok + gaji_potsekolah + gaji_potkesehatan + gaji_potlain + gaji_potabsensi + gaji_potfkk + gaji_potzis + gaji_potibi + gaji_potqurban + gaji_potsp + gaji_potinfaqmasjid;
        $("#totalpotongan").html(totalpotongan > 0 ? "<b><font color=red>" + Number(totalpotongan).toLocaleString() + "</font></b>" : '-');

        var totalterima = totalgajitunj + totalinsentif - totalpotongan;
        $("#totalterima").html(totalterima > 0 ? "<b><font color=green>" + Number(totalterima).toLocaleString() + "</font></b>" : '-');

        $("#title-gaji").html("Gaji Karyawan <b>" + dataRecord.gaji_nama +" ("+dataRecord.gaji_nopeg+")"+"</b>")

    }

    function listGaji(gaji_id) {
        $("#readgaji").css('display', 'none');
        $("#listgaji").css('display', 'block');
        $("#gajih_id").val(0);
        $('#grid').jqxGrid('updatebounddata', 'cells');
        resetForm();
    }
</script>
<style>
    #myProgress {
        display: none;
        width: 100%;
        background-color: grey;
    }

    #myBar {
        width: 1%;
        height: 30px;
        background-color: green;
    }

    #readgaji {
        display : none;
    }
  .gaji-table {
    width: 100%;
    border : 1px solid #00a5a5;
    color : #00a5a5;
    margin-bottom: 10px !important;
  }
  .gaji-table > tbody > tr > td{
    padding : 5px 10px;
    border : 1px solid #00a5a5;
    color : #0b4848;
    font-size: 14px;
  }
  .title-column {
    text-align: left;
    border: none !important;
  }
  .price-column {
    text-align: right;
    border-left: none !important;
    border-top: none !important;
    border-bottom: none !important;
  }

  .mailbox {
    padding: 2px;
  }

  .terima {
    border-top : 1px solid #00a5a5;
    background-color: #e9e1f5;
  }

  @media only screen and (max-width: 500px) {
    .gaji-table > tbody > tr > td{
      font-size: 12px;
      padding : 5px 10px;
      border : none;
      color : #0b4848;
      vertical-align: top;
    }

    .card-body.p-0 .table tbody>tr>td:first-of-type, .card-body.p-0 .table tbody>tr>th:first-of-type, .card-body.p-0 .table thead>tr>td:first-of-type, .card-body.p-0 .table thead>tr>th:first-of-type {
      padding: 12px;
    }

    .mailbox-name {
      font-size: 12px;
      padding: 4px !important;
    }
    .mailbox-date {
      font-size: 12px;
      padding: 4px !important;
    }

    .card-body.p-0 .table tbody>tr>td:last-of-type, .card-body.p-0 .table tbody>tr>th:last-of-type, .card-body.p-0 .table thead>tr>td:last-of-type, .card-body.p-0 .table thead>tr>th:last-of-type {
        padding: 5px;
    }
  }

  @media only screen and (min-width: 400px) {
      .gaji-table-center {
        text-align: center;
      }
      .gaji-table.total {
        width : 50%;
        display: inline-table;
      }
  }
</style>
<section class="content">
    <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div id="listgaji" class="card card-default">
            <!-- /.card-header -->
            <form id="uploadFile">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-xs-6">
                            <div class="form-group">
                                <label>Bulan</label>
                                <?php 
                                    $bulan = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER', 'JANUARI'];
                                ?>
                                <select id="bulan" class="form-control select2" style="width: 100%;">
                                    <?php
                                        $bulannow = (integer)date('m');
                                        $selection = "";
                                        foreach ($bulan as $key => $value) {
                                            $selection .= "<option";
                                            if ($value==$bulan[$bulannow]) { 
                                                $selection .= " selected=selected "; 
                                            }
                                            $selection .= ">$value</option>";
                                        }
                                        echo $selection;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <div class="form-group">
                                <label>Tahun</label>
                                <?php 
                                    $tahunnow = date("Y");
                                    $tahun = [];
                                    for ($i=6; $i > 1; $i--) { 
                                        $futureDate = date('Y', strtotime('-'.$i.' year', strtotime(date("Y-m-d"))));
                                        array_push($tahun, $futureDate);
                                    }

                                    for ($i=0; $i < 6; $i++) { 
                                        $futureDate = date('Y', strtotime('+'.$i.' year', strtotime(date("Y-m-d"))));
                                        array_push($tahun, $futureDate);
                                    }

                                ?>
                                <select id="tahun" class="form-control select2" style="width: 100%;">
                                    <?php
                                        $selection = "";
                                        foreach ($tahun as $key => $value) {
                                            $selection .= "<option";
                                            if ($value==$tahunnow) { 
                                                $selection .= " selected=selected "; 
                                            }
                                            $selection .= ">$value</option>";
                                        }
                                        echo $selection;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <div style="padding-top: 30px;">
                                <button type="button" id="btn-filter" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i>&nbsp;&nbsp;Filter</button>
                                <button type="button" class="btn btn-success btn-sm" id="btn-template"><i class="fas fa-file-excel"></i>&nbsp;&nbsp;Template</button type="submit">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-6" style="margin: 4px 0px;">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="exampleInputFile" name="importexcel">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text" id=""><i class="fas fa-file-excel"></i>&nbsp;&nbsp;Import</button type="submit">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6" style="margin: 0;padding: 10px;">
                            <div id="myProgress">
                                <div id="myBar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id='jqxWidget' style="margin-top: 5px;">
                                <div id="grid"></div>
                                <div id="cellbegineditevent"></div>
                                <div style="margin-top: 10px;" id="cellendeditevent"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="readgaji" class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Delete"
                    onclick="listGaji()">
                        <i class="fas fa-list-ol"></i>
                    </button>&nbsp;&nbsp;&nbsp;List Gaji
                </h3>

                <!-- <div class="card-tools">
                    <a href="#" class="btn btn-tool" data-toggle="tooltip" title="Previous"><i class="fas fa-chevron-left"></i></a>
                    <a href="#" class="btn btn-tool" data-toggle="tooltip" title="Next"><i class="fas fa-chevron-right"></i></a>
                </div> -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-read-info">
                <input type="hidden" id="gajih_id"/>
                <h5 id="title-gaji">Gaji Karyawan </h5>
                <h6>Dari: Sdm Rumah Sakit Haji Jakarta
                  <span id="tglgajih" class="mailbox-read-time float-right"></span></h6>
              </div>
              <!-- /.mailbox-read-info -->
              <!-- <div class="mailbox-controls with-border text-center">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" title="pdf"
                  onclick="exportPdf2()">
                  <i class="far fa-file-pdf"></i>&nbsp;&nbsp;&nbsp;Export PDF
                </button>
              </div> -->
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <table class="gaji-table">
                  <tr style="background-color: #e9e1f5;">
                    <td align="center" width="50%" colspan="2">GAJI DAN TUNJANGAN</td>
                    <td align="center" width="50%" colspan="2">INSENTIF</td>
                  </tr>
                  <tr>
                    <td class="title-column">GAJI RSHJ</td>
                    <td id="gajirshaji" class="price-column">-</td>
                    <td class="title-column">Jasa Yan</td>
                    <td id="jasayan" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Kehadiran</td>
                    <td id="kehadiran" class="price-column">-</td>
                    <td class="title-column">Shift</td>
                    <td id="shift" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Transport</td>
                    <td id="transport" class="price-column">-</td>
                    <td class="title-column">Lembur</td>
                    <td id="lembur" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Operasional</td>
                    <td id="operasional" class="price-column">-</td>
                    <td class="title-column">On Call</td>
                    <td id="oncall" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Rapel/MOD</td>
                    <td id="rapelmod" class="price-column">-</td>
                    <td class="title-column">Uang Cuti</td>
                    <td id="uangcuti" class="price-column">-</td>
                  </tr>
                </table>
                <table class="gaji-table">
                  <tr style="background-color: #e9e1f5;">
                    <td align="center" width="50%" colspan="4">POTONGAN KARYAWAN</td>
                  </tr>
                  <tr>
                    <td class="title-column">Simwa</td>
                    <td id="simwa" class="price-column">-</td>
                    <td class="title-column">BPJS TK</td>
                    <td id="bpjstk" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Koperasi</td>
                    <td id="koperasi" class="price-column">-</td>
                    <td class="title-column">BPJS Pensiun</td>
                    <td id="Pensiun" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Parkir</td>
                    <td id="parkir" class="price-column">-</td>
                    <td class="title-column">Pajak</td>
                    <td id="pajak" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">SIMPOK</td>
                    <td id="simpok" class="price-column">-</td>
                    <td class="title-column">Sekolah</td>
                    <td id="sekolah" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Kesehatan</td>
                    <td id="kesehatan" class="price-column">-</td>
                    <td class="title-column">Lain-lain</td>
                    <td id="lainlain" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Absensi</td>
                    <td id="absensi" class="price-column">-</td>
                    <td class="title-column">FKK</td>
                    <td id="fkk" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">ZIS</td>
                    <td id="zis" class="price-column">-</td>
                    <td class="title-column">IBI</td>
                    <td id="ibi" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Qurban</td>
                    <td id="qurban" class="price-column">-</td>
                    <td class="title-column">SP</td>
                    <td id="sp" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Infaq Masjid</td>
                    <td id="masjid" class="price-column">-</td>
                    <td class="title-column"></td>
                    <td class="price-column"></td>
                  </tr>
                </table>
                <div class="gaji-table-center">
                  <table class="gaji-table total">
                    <tr style="background-color: #e9e1f5;">
                      <td align="center" width="50%" colspan="2">TOTAL</td>
                    </tr>
                    <tr>
                      <td class="title-column">Gaji & Tunjangan </td>
                      <td id="totalgajitunj" class="price-column">-</td>
                    </tr>
                    <tr>
                      <td class="title-column">Insentif </td>
                      <td id="totalinsentif" class="price-column">-</td>
                    </tr>
                    <tr>
                      <td class="title-column">Potongan (-)</td>
                      <td id="totalpotongan" class="price-column">-</td>
                    </tr>
                    <tr class="terima">
                      <td class="title-column">Terima </td>
                      <td id="totalterima" class="price-column">-</td>
                    </tr>
                  </table>
                </div>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.card-body -->
            <!-- /.card-footer -->
            <div class="card-footer">
              
            </div>
            <!-- /.card-footer -->
          </div>
    </div>
</section>
<script src="<?php echo BASE_URL ?>/assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
