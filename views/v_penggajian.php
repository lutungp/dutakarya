<link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdata.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.js"></script>
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
                {name : 'gaji_created_date', type : 'float'},
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

        var dataAdapter = new $.jqx.dataAdapter(source, {
            formatData: function (data) {
                $.extend(data, {
                    bulan: $("#bulan").val(),
                    tahun: $("#tahun").val()
                });
                return data;
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
            editable: true,
            selectionmode: 'multiplecellsadvanced',
            columns: [
                { text: 'Gaji Bulan', datafield: 'gaji_bulan', width : '150',
                cellsrenderer: function (rec, field, val) {
                    return "&nbsp;" + bulan[val];
                }},
                { text: 'Gaji Tahun', datafield: 'gaji_tahun', width : '150'},
                { text: 'No. Pegawai', datafield: 'gaji_nopeg'},
                { text: 'Nama', datafield: 'gaji_nama', width : '200'},
                { text: 'Unit', datafield: 'gaji_unitpeg'},
                { text: 'Golongan', datafield: 'gaji_golpangkat_peg'},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        
                    }
                }
            ]
        });

        $("#btn-filter").on("click", function () {
            $('#grid').jqxGrid('updatebounddata', 'cells');
        });

        $('#uploadFile').on('submit', function(event){  
           event.preventDefault();  
           $.ajax({  
                url: "<?php echo BASE_URL ?>/controllers/C_penggajian.php?action=importgaji",
                method:"POST",  
                data:new FormData(this),  
                contentType:false,  
                processData:false,  
                success:function(data){  
                     
                }  
           });  
      });  

    });
</script>
<section class="content">
    <div class="container-fluid">
    <!-- SELECT2 EXAMPLE -->
    <div class="card card-default">
        <!-- /.card-header -->
        <form id="uploadFile">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-xs-6">
                        <div class="form-group">
                            <label>Bulan</label>
                            <?php 
                                $bulan = ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
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
</section>
<script src="<?php echo BASE_URL ?>/assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
