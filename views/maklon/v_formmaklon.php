<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<style>
    #maklon_total {
        font-size : 18px;
        font-weight : bold;
    }
</style>
<script>
    var barangAdapter = false;
    var satuanAdapter = false;
    var barang = [];
    var satuan = [];
    var satKonv = [];
    var hapusdetail = [];
    $(document).ready(function(){
        datamaklon = '<?php echo $dataparse ?>';
        var datamaklon = datamaklon.replace(/\\n/g, "\\n").replace(/\\'/g, "\\'").replace(/\\"/g, '\\"').replace(/\\&/g, "\\&").replace(/\\r/g, "\\r").replace(/\\t/g, "\\t").replace(/\\b/g, "\\b").replace(/\\f/g, "\\f");
        datamaklon = JSON.parse(datamaklon.replace(/[\u0000-\u0019]+/g,""));
        var datamaklondetail = [];
        if(datamaklon!==null) {
            datamaklondetail = datamaklon.datamaklondetail;
        }

        var generaterow = function (i) {
            var row = {};
            row["maklondet_id"] = 0;
            row["t_maklon_id"] = 0;
            row["satuankonv"] = '';
            row["m_barang_id"] = 0;
            row["m_barang_nama"] = '';
            row["m_barangsatuan_id"] = 0,
            row["m_bahanbakubrg_id"] = 0,
            row["hargakontrak"] = 0;
            row['maklondet_harga'] = 0;
            row['hargakontrakdet_ppn'] = 'N';
            row['maklondet_ppn'] = 0;
            row["m_satuan_id"] = 0;
            row["m_satuan_nama"] = '';
            row["satkonv_nilai"] = 1;
            row["maklondet_qtyold"] = 0;
            row["maklondet_qty"] = 0;
            row['maklondet_subtotal'] = 0;
            row['maklondet_total'] = 0;
            row['t_returdet_qty'] = 0;
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_maklon.php?action=getbarang", function(data, status){
            data = JSON.parse(data);
            databarang = data['barang'];
            satKonv = data['satuan_konversi'];
            for (let index = 0; index < databarang.length; index++) {
                const element = databarang[index];
                barang.push({ value: element.barang_id, label: element.barang_nama, satuan_id : element.m_satuan_id, satuan_nama : element.satuan_nama });
            }
            datasatuan = data['satuan'];
            for (let index = 0; index < datasatuan.length; index++) {
                const element = datasatuan[index];
                satuan.push({ value: element.satuan_id, label: element.satuan_nama });
            }
            
            var barangSource = {
                    datatype: "array",
                    datafields: [
                        { name: 'label', type: 'string' },
                        { name: 'value', type: 'int' }
                    ],
                    localdata: barang
            };
            
            barangAdapter = new $.jqx.dataAdapter(barangSource, {
                autoBind: true
            });
            
            var satuanSource = {
                    datatype: "array",
                    datafields: [
                        { name: 'label', type: 'string' },
                        { name: 'value', type: 'int' }
                    ],
                    localdata: satuan
            };
            satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                autoBind: true
            });
            // prepare the data
            var datamaklondet = [];
            var retur = 0;
            for (let index = 0; index < datamaklondetail.length; index++) {
                const element = datamaklondetail[index];
                let datdet = {
                    maklondet_id : element.maklondet_id,
                    t_maklon_id : element.t_maklon_id,
                    m_barang_id : element.m_barang_id,
                    m_barang_nama : element.m_barang_nama,
                    m_barangsatuan_id : element.m_barangsatuan_id,
                    m_bahanbakubrg_id : element.m_bahanbakubrg_id,
                    m_satuan_id : element.m_satuan_id,
                    m_satuan_nama : element.m_satuan_nama,
                    hargakontrak : element.hargakontrak,
                    satkonv_nilai : element.satkonv_nilai,
                    maklondet_harga : element.maklondet_harga,
                    hargakontrakdet_ppn : element.hargakontrakdet_ppn,
                    maklondet_ppn : element.maklondet_ppn,
                    maklondet_qtyold : element.maklondet_qty,
                    maklondet_qty : element.maklondet_qty,
                    maklondet_subtotal : element.maklondet_subtotal,
                    maklondet_total : element.maklondet_total,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id)),
                    t_returdet_qty : element.t_returdet_qty
                };

                retur = retur + parseFloat(element.t_returdet_qty);
                datamaklondet.push(datdet);
            }

            if (retur > 0) {
                swal("Info!", "Transaksi sudah terdapat retur, tidak dapat diedit dan batal", "error");
                $("#simpan").prop("disabled", true);
                $("#batal").prop("disabled", true);
            }
            
            var maklonGridSource = {
                datatype: "array",
                localdata:  datamaklondet,
                datafields: [
                    { name: 'maklondet_id', type: 'int'},
                    { name: 't_maklon_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_nama', value: 'm_barang_nama', values: { source: barangAdapter.records, value: 'barang_id', name: 'barang_nama' } },
                    { name: 'm_barang_id', type: 'int'},
                    { name: 'm_barangsatuan_id', type: 'int'},
                    { name: 'm_bahanbakubrg_id', type: 'int'},
                    { name: 'hargakontrak', type: 'float'},
                    { name: 'maklondet_harga', type: 'float'},
                    { name: 'hargakontrakdet_ppn', type: 'string'},
                    { name: 'maklondet_ppn', type: 'float'},
                    { name: 'm_satuan_nama', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'satuan_id', name: 'satuan_nama' } },
                    { name: 'm_satuan_id', type: 'int'},
                    { name: 'satkonv_nilai'},
                    { name: 'maklondet_qtyold', type: 'float'},
                    { name: 'maklondet_qty', type: 'float'},
                    { name: 'maklondet_subtotal', type: 'float'},
                    { name: 'maklondet_total', type: 'float'},
                    { name: 't_returdet_qty', type: 'float'}
                ],
                addrow: function (rowid, rowdata, position, commit) {
                    // synchronize with the server - send insert command
                    // call commit with parameter true if the synchronization with the server is successful 
                    //and with parameter false if the synchronization failed.
                    // you can pass additional argument to the commit callback which represents the new ID if it is generated from a DB.
                    commit(true);
                },
                deleterow: function (rowid, commit) {
                    // synchronize with the server - send delete command
                    // call commit with parameter true if the synchronization with the server is successful 
                    //and with parameter false if the synchronization failed.
                    commit(true);
                }
            };

            var maklonAdapter = new $.jqx.dataAdapter(maklonGridSource);
            var tooltiprenderer = function (element) {
                $(element).jqxTooltip({position: 'mouse', content: 'Tekan tombol F9 pada keyboard disaat focus pada column potongan' });
            }
            $("#maklonGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: maklonAdapter,
                editable: true,
                showtoolbar: true,
                rendertoolbar: function (toolbar) {
                    var me = this;
                    var container = $("<div style='margin: 5px;'></div>");
                    toolbar.append(container);
                    container.append('<input id="addrowbutton" type="button" value="Tambah" />');
                    container.append('<input style="margin-left: 5px;" id="deleterowbutton" type="button" value="Hapus" />');
                    $("#addrowbutton").jqxButton();
                    $("#deleterowbutton").jqxButton();
                    // create new row.
                    $("#addrowbutton").on('click', function () {
                        var datarow = generaterow();
                        var commit = $("#maklonGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#maklonGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#maklonGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#maklonGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({maklondet_id : rechapus.maklondet_id});

                            var id = $("#maklonGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#maklonGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    {
                        text: 'Barang', datafield: 'm_barang_id', displayfield: 'm_barang_nama', columntype: 'combobox',
                        initeditor: function (row, value, editor) {
                            editor.jqxComboBox({
                                source: barangAdapter,
                                valueMember: 'label',
                                displayMember: 'value',
                            });
                            editor.on('change', function (event) {
                                var recorddata = $('#maklonGrid').jqxGrid('getrenderedrowdata', row);
                                var datasatkonv = satKonv;
                                if (event.args) {
                                    var val = event.args.item.value;
                                    dtsatkonv = datasatkonv.filter(p=>parseInt(p.m_barang_id)==val);
                                    var satkonv = [];
                                    for (let index = 0; index < dtsatkonv.length; index++) {
                                        const element = dtsatkonv[index];
                                        satkonv.push({ value: parseInt(element.satkonv_id), label: element.satuan_nama, satkonv_nilai : parseFloat(element.satkonv_nilai) });
                                    }

                                    let brg = barang.filter(p=>parseInt(p.value)==val);
                                    satkonv.push({ value: brg[0].satuan_id, label: brg[0].satuan_nama, satkonv_nilai : 1 });
                                    recorddata.satuankonv = JSON.stringify(satkonv);
                                    var maklon_tgl = $('#maklon_tgl').val();
                                    var data = {
                                        barang_id : val,
                                        tanggal : moment(maklon_tgl, "DD-MM-YYYY").format("YYYY-MM-DD"),
                                        m_rekanan_id : $("#m_rekanan_id").val()
                                    }
                                    let brg0 = brg[0];
                                    $("#maklonGrid").jqxGrid('setcellvalue', row, "m_satuan_id", brg0.satuan_id);
                                    $("#maklonGrid").jqxGrid('setcellvalue', row, "m_satuan_nama", brg0.satuan_nama);
                                    recorddata.m_barangsatuan_id = parseInt(brg0.satuan_id);
                                    
                                    $.post("<?php echo BASE_URL ?>/controllers/C_maklon.php?action=gethargakontrak", data, function(result){
                                        let res = JSON.parse(result);
                                        $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_harga", res.hargakontrakdet_harga);
                                        recorddata.hargakontrakdet_ppn = res.hargakontrakdet_ppn;
                                        recorddata.hargakontrak = res.hargakontrakdet_harga;
                                        let maklondet_ppn = res.hargakontrakdet_ppn == 'Y' ? res.hargakontrakdet_harga*10/100 : 0;
                                        $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_ppn", maklondet_ppn);
                                        recorddata.m_barang_nama = brg0.label;
                                        recorddata.m_barang_id = parseInt(brg0.value);
                                        $.post("<?php echo BASE_URL ?>/controllers/C_maklon.php?action=getbahanbaku", data, function(result){
                                            let res = JSON.parse(result);
                                            res.forEach(element => {
                                                var dataw = {
                                                    hargakontrak: 0,
                                                    hargakontrakdet_ppn: "N",
                                                    m_barang_id: parseInt(element.m_barang_id),
                                                    m_barang_nama: element.m_barang_nama,
                                                    m_barangsatuan_id: parseInt(element.m_satuan_id),
                                                    m_bahanbakubrg_id: parseInt(brg0.value),
                                                    m_satuan_id: parseInt(element.m_satuan_id),
                                                    m_satuan_nama: element.satuan_nama,
                                                    maklondet_harga: 0,
                                                    maklondet_id: 0,
                                                    maklondet_ppn: 0,
                                                    maklondet_qty: 0,
                                                    maklondet_qtyold: 0,
                                                    maklondet_subtotal: 0,
                                                    maklondet_total: 0,
                                                    satkonv_nilai: 1*element.bahanbrg_qty,
                                                    satuankonv: [],
                                                    t_maklon_id: "",
                                                    t_returdet_qty: 0,
                                                }
                                                $("#maklonGrid").jqxGrid('addrow', null, dataw);
                                                recorddata.m_barang_nama = brg0.label;
                                                recorddata.m_barang_id = parseInt(brg0.value); 
                                            });
                                        });
                                    });
                                    
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_nama', columntype: 'combobox',
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#maklonGrid').jqxGrid('getrenderedrowdata', row);
                            /* lintang */
                            var recorddata = $('#maklonGrid').jqxGrid('getrenderedrowdata', row);
                            var sat = JSON.parse(recorddata.satuankonv);
                            var satkonv = [];
                            var m_barangsatuan_nama = datasatuan.filter(p=>p.satuan_id==recorddata.m_barangsatuan_id);
                            console.log(m_barangsatuan_nama)
                            satkonv.push(m_barangsatuan_nama[0].satuan_nama);
                            for (let index = 0; index < sat.length; index++) {
                                const element = sat[index];
                                satkonv.push(element.satuan_nama)
                            }
                            
                            editor.jqxComboBox({
                                source: satkonv,
                                autoDropDownHeight : true
                            });
                            
                            var index = satkonv.indexOf(recorddata.m_satuanmaklon_nama);
                            editor.jqxComboBox('selectIndex', index);
                            
                            editor.on('change', function (event) {
                                var datasatkonv = satKonv;
                                if (event.args) {
                                    var val = event.args.item.value;
                                    var satuankonv = JSON.parse(recorddata.satuankonv);
                                    var dtsatkonv = satuankonv.filter(p=>p.satuan_nama==val);
                                    if (dtsatkonv.length > 0) {
                                        recorddata.satkonv_nilai = dtsatkonv[0].satkonv_nilai;
                                    } else {
                                        recorddata.satkonv_nilai = 1;
                                    }
                                    var ppn = recorddata.hargakontrakdet_ppn == 'Y' ? (recorddata.hargakontrak * 10/100) : 0;
                                    $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_qty", 1);
                                    $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_ppn", parseFloat(ppn*val));
                                    $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_harga", (parseFloat(recorddata.hargakontrak*val) + parseFloat(ppn*val)));
                                    settotal();
                                }
                            });
                        }, 
                    },
                    { text: 'Harga', datafield: 'maklondet_harga', cellsalign: 'right', editable : false, width : 100 },
                    { text: 'Qty', datafield: 'maklondet_qty', cellsalign: 'right', columntype: 'numberinput',
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            let gridrecords = maklonAdapter.records;
                            let currentrecord = maklonAdapter.records[row];
                            editor.on('keyup', function (event) {
                                var recorddata = $('#maklonGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                var ppn = recorddata.hargakontrakdet_ppn == 'Y' ? (recorddata.hargakontrak * 10/100) : 0;
                                var maklondet_subtotal = val * recorddata.hargakontrak * recorddata.satkonv_nilai;
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_ppn", (maklondet_subtotal*10/100));
                                maklondet_total = maklondet_subtotal + parseFloat(maklondet_subtotal*10/100)
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_subtotal", maklondet_subtotal);
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_total", parseFloat(maklondet_total));
                                settotal();

                                /* mengisi qty bahan baku */
                                
                                let bbaku = gridrecords.filter(x => x.m_bahanbakubrg_id === currentrecord.m_barang_id);
                                if (bbaku.length > 0) {
                                    let idx = [];
                                    bbaku.forEach(element => {
                                        let varindex = gridrecords.findIndex(x => x.m_barang_id === element.m_barang_id);
                                        let bbkurec = $('#maklonGrid').jqxGrid('getrenderedrowdata', varindex);
                                        var qtybbaku = bbkurec.satkonv_nilai * val;
                                        $("#maklonGrid").jqxGrid('setcellvalue', varindex, "maklondet_qty", qtybbaku);
                                    });
                                }
                            });
                            editor.on('change', function (event) {
                                var recorddata = $('#maklonGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                var ppn = recorddata.hargakontrakdet_ppn == 'Y' ? (recorddata.hargakontrak * 10/100) : 0;
                                var maklondet_subtotal = val * recorddata.hargakontrak * recorddata.satkonv_nilai;
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_ppn", (maklondet_subtotal*10/100));
                                maklondet_total = maklondet_subtotal + parseFloat(maklondet_subtotal*10/100)
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_subtotal", maklondet_subtotal);
                                $("#maklonGrid").jqxGrid('setcellvalue', row, "maklondet_total", parseFloat(maklondet_total));
                                settotal();

                                /* mengisi qty bahan baku */
                                
                                let bbaku = gridrecords.filter(x => x.m_bahanbakubrg_id === currentrecord.m_barang_id);
                                if (bbaku.length > 0) {
                                    let idx = [];
                                    bbaku.forEach(element => {
                                        let varindex = gridrecords.findIndex(x => x.m_barang_id === element.m_barang_id);
                                        let bbkurec = $('#maklonGrid').jqxGrid('getrenderedrowdata', varindex);
                                        var qtybbaku = bbkurec.satkonv_nilai * val;
                                        $("#maklonGrid").jqxGrid('setcellvalue', varindex, "maklondet_qty", qtybbaku);
                                    });
                                }
                            });
                        }
                    },
                    { text: 'PPN', datafield: 'maklondet_ppn', cellsalign: 'right', editable : false, width : 100 },
                    { text: 'Subtotal', datafield: 'maklondet_subtotal', cellsalign: 'right', editable : false, cellsformat: 'F',},
                    { text: 'Subtotal', datafield: 'maklondet_total', cellsalign: 'right', editable : false, cellsformat: 'F' },
                ]
            });

            settotal();
        });

    });

    function settotal() {
        var griddata = $('#maklonGrid').jqxGrid('getdatainformation');
        var rows = [];
        var total = 0;
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#maklonGrid').jqxGrid('getrenderedrowdata', i);
            total = total + parseFloat(rec.maklondet_total);
        }
        total = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        $('#maklon_total').val(total);
    }
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_maklon'">Kembali</button>
                </div>
                <form id="formmaklon">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maklon_no">No. maklon</label>
                                    <input type="hidden" id="maklon_id" name="maklon_id">
                                    <input type="text" class="form-control" id="maklon_no" name="maklon_no" readonly>
                                </div>
                                <?php 
                                $t_hutang_no = isset($data->datamaklon->t_hutang_no) ? $data->datamaklon->t_hutang_no : '';
                                if ($t_hutang_no <> '') {
                                    ?>
                                    <div class="form-group">
                                        <label for="hutang_no">No. hutang</label>
                                        <input type="hidden" id="hutang_id" name="hutang_id">
                                        <input type="text" class="form-control" id="hutang_no" name="hutang_no" value="<?php echo $data->datamaklon->t_hutang_no ?>" readonly>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maklon_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="maklon_tgl" name="maklon_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask require>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;" require></select>
                                </div>
                                <div class="form-group">
                                    <label for="maklon_total">TOTAL</label>
                                    <input type="text" class="form-control" style="text-align: right;" id="maklon_total" name="maklon_total" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-4">
                                <button type="button" id="tambah" class="btn btn-primary btn-sm">Tambah</button>
                                <button type="button" id="hapus" class="btn btn-danger btn-sm">Hapus</button>
                            </div>
                        </div> -->
                        <div class="row">
                            <div id="maklonGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php
                        $t_hutang_no = isset($data->datamaklon->t_hutang_no) ? $data->datamaklon->t_hutang_no : '';
                        if ($delete <> '' &&  $t_hutang_no == '') { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php 
                        if ((($create <> '' && isset($data->maklon_id) == 0) || ($update <> '' && $data->maklon_id > 0) && $t_hutang_no == '')) { ?>
                        <button type="submit" id="simpan" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <!-- <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;" onclick="cetak()">Cetak</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('[data-mask]').inputmask();
        var now = new Date();
        $('#maklon_tgl').val(moment(now).format('DD-MM-YYYY'));

        $("#m_rekanan_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_maklon.php?action=getrekanan&jenis=pabrik',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });

        $('#formmaklon').submit(function (event) {
            event.preventDefault();
            var griddata = $('#maklonGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#maklonGrid').jqxGrid('getrenderedrowdata', i);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(rec.m_barang_id)&&parseInt(p.m_satuan_id)==parseInt(rec.m_satuan_id));
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                if (rec.maklondet_qty > 0) {
                    rows.push({
                        'maklondet_id' : rec.maklondet_id,
                        't_maklon_id' : $('#maklon_id').val(),
                        'm_barang_id' : rec.m_barang_id,
                        'm_barang_nama' : rec.m_barang_nama,
                        'm_barangsatuan_id' : rec.m_barangsatuan_id,
                        'm_bahanbakubrg_id' : rec.m_bahanbakubrg_id,
                        'm_satuan_id' : rec.m_satuan_id,
                        'm_satuan_nama' : rec.m_satuan_nama,
                        'satkonv_nilai' : parseFloat(satkonv_nilai),
                        'hargakontrak' : parseFloat(rec.hargakontrak),
                        'maklondet_harga' : parseFloat(rec.maklondet_harga),
                        'maklondet_ppn' : parseFloat(rec.maklondet_ppn),
                        'maklondet_qty' : rec.maklondet_qty,
                        'maklondet_qtyold' : rec.maklondet_qtyold,
                        'maklondet_subtotal' : parseFloat(rec.maklondet_subtotal),
                        'maklondet_total' : parseFloat(rec.maklondet_total),
                    });
                } else if (rec.maklondet_qty == 0 && rec.m_satuan_id > 0) {
                    swal("Info!", "maklon Gagal disimpan, terdapat isian dengan qty kosong ", "error");
                    return false;
                } else if (rec.m_satuan_id < 1) {
                    swal("Info!", "maklon Gagal disimpan, terdapat isian dengan satuan kosong ", "error");
                    return false;
                }
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_maklon.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    maklon_id : $('#maklon_id').val(),
                    maklon_no : $('#maklon_no').val(),
                    maklon_tgl : moment($('#maklon_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    m_pegdriver_id : $('#m_pegdriver_id').val(),
                    m_peghelper_id : $('#m_peghelper_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        // window.open('<?php // echo BASE_URL;?>/controllers/C_maklon.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "maklon Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "maklon Gagal disimpan", "error");
                    }
                }
            });
        });

        datamaklon = '<?php echo $dataparse ?>';
        var datamaklon = datamaklon.replace(/\\n/g, "\\n").replace(/\\'/g, "\\'").replace(/\\"/g, '\\"').replace(/\\&/g, "\\&").replace(/\\r/g, "\\r").replace(/\\t/g, "\\t").replace(/\\b/g, "\\b").replace(/\\f/g, "\\f");
        datamaklon = JSON.parse(datamaklon.replace(/[\u0000-\u0019]+/g,""));
        if(datamaklon!==null) {
            var dat = datamaklon.datamaklon;
            $('#maklon_id').val(dat.maklon_id);
            $('#maklon_no').val(dat.maklon_no);
            $('#maklon_tgl').val(moment(dat.maklon_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            // $("#m_pegdriver_id").data('select2').trigger('select', {
            //     data: {"id":dat.pegdriver_id, "text": dat.pegdriver_nama }
            // });
            // $("#m_peghelper_id").data('select2').trigger('select', {
            //     data: {"id":dat.peghelper_id, "text": dat.peghelper_nama }
            // });
            $("#m_rekanan_id").prop("disabled", true);
            $('#batal').removeAttr('disabled');
            if (dat.t_hutang_no !== '') {
                var hutangstr = dat.t_hutang_no == null || dat.t_hutang_no == '' ? '' : ", sudah dibuat hutang dengan No. hutang " + dat.t_hutang_no;
                swal("Info!", "No. Maklon " + dat.maklon_no + hutangstr, "warning");
            }
        }

        $('#batal').on('click', function () {
            var griddata = $('#maklonGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#maklonGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                rows.push({'maklondet_id' : rec.maklondet_id});
            }

            swal({
                title: "Batalkan maklon " + $('#maklon_no').val(),
                text: "Alasan dihapus :",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Tuliskan alasan anda !");
                    return false
                }
                $.ajax({
                    url: "<?php echo BASE_URL ?>/controllers/C_maklon.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        maklon_id : $('#maklon_id').val(),
                        maklon_no : $('#maklon_no').val(),
                        maklon_tgl : moment($('#maklon_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "maklon Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "maklon Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function cetak() {
        var maklon_id = $('#maklon_id').val();
        window.open('<?php echo BASE_URL;?>/controllers/C_maklon.php?action=exportpdf&id=' + maklon_id);
    }

    function resetForm() {
        var now = new Date();
        $('#maklon_id').val(0);
        $('#maklon_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#maklon_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#maklonGrid").jqxGrid('clear');
    }
</script>