<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<style>
    #pengiriman_total {
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
        datapengiriman = JSON.parse('<?php echo $dataparse ?>');
        var datapengirimandetail = [];
        if(datapengiriman!==null) {
            datapengirimandetail = datapengiriman.datapengirimandetail;
        }

        var generaterow = function (i) {
            var row = {};
            row["pengirimandet_id"] = 0;
            row["t_pengiriman_id"] = 0;
            row["satuankonv"] = '';
            row["m_barang_id"] = '';
            row["m_barangsatuan_id"] = 0,
            row["baranghnadet_harga"] = 0;
            row['pengirimandet_harga'] = 0;
            row["m_satuan_id"] = '';
            row["satkonv_nilai"] = 1;
            row["pengirimandet_qtyold"] = 0;
            row["pengirimandet_qty"] = 0;
            row['pengirimandet_subtotal'] = 0;
            row['pengirimandet_potongan'] = 0;
            row['pengirimandet_total'] = 0;
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getbarang", function(data, status){
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
            var datapengirimandet = [];
            
            for (let index = 0; index < datapengirimandetail.length; index++) {
                const element = datapengirimandetail[index];
                let datdet = {
                    pengirimandet_id : element.pengirimandet_id,
                    t_pengiriman_id : element.t_pengiriman_id,
                    m_barang_id : element.m_barang_id,
                    m_barangsatuan_id : element.m_barangsatuan_id,
                    m_satuan_id : element.m_satuan_id,
                    baranghnadet_harga : element.baranghnadet_harga,
                    satkonv_nilai : element.satkonv_nilai,
                    pengirimandet_harga : element.pengirimandet_harga,
                    pengirimandet_qtyold : element.pengirimandet_qty,
                    pengirimandet_qty : element.pengirimandet_qty,
                    pengirimandet_subtotal : element.pengirimandet_subtotal,
                    pengirimandet_potongan : element.pengirimandet_potongan,
                    pengirimandet_total : element.pengirimandet_total,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id))
                };
                datapengirimandet.push(datdet);
            }
            var pengirimanGridSource = {
                datatype: "array",
                localdata:  datapengirimandet,
                datafields: [
                    { name: 'pengirimandet_id', type: 'int'},
                    { name: 't_pengiriman_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_barangsatuan_id', type: 'int'},
                    { name: 'baranghnadet_harga', type: 'float'},
                    { name: 'pengirimandet_harga', type: 'float'},
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'satkonv_nilai'},
                    { name: 'pengirimandet_qtyold', type: 'float'},
                    { name: 'pengirimandet_qty', type: 'float'},
                    { name: 'pengirimandet_subtotal', type: 'float'},
                    { name: 'pengirimandet_potongan', type: 'float'},
                    { name: 'pengirimandet_total', type: 'float'},
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

            var pengirimanAdapter = new $.jqx.dataAdapter(pengirimanGridSource);
            var tooltiprenderer = function (element) {
                $(element).jqxTooltip({position: 'mouse', content: 'Tekan tombol F9 pada keyboard disaat focus pada column potongan' });
            }
            $("#pengirimanGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: pengirimanAdapter,
                editable: true,
                showtoolbar: true,
                selectionmode: 'singlecell',
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
                        var commit = $("#pengirimanGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#pengirimanGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#pengirimanGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({pengirimandet_id : rechapus.pengirimandet_id});

                            var id = $("#pengirimanGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#pengirimanGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    {
                        text: 'Barang', datafield: 'm_barang_id', displayfield: 'm_barang_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({
                                source: barangAdapter,
                                displayMember: 'label',
                                valueMember: 'value'
                            });
                            editor.on('select', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var datasatkonv = satKonv;
                                if (event.args) {
                                    var val = event.args.item.value;
                                    dtsatkonv = datasatkonv.filter(p=>parseInt(p.m_barang_id)==val);
                                    var satkonv = [];
                                    for (let index = 0; index < dtsatkonv.length; index++) {
                                        const element = dtsatkonv[index];
                                        satkonv.push({ value: parseInt(element.satkonv_id), label: element.satuan_nama, satkonv_nilai : parseFloat(element.satkonv_nilai) });
                                    }

                                    brg = barang.filter(p=>parseInt(p.value)==val);
                                    satkonv.push({ value: brg[0].satuan_id, label: brg[0].satuan_nama, satkonv_nilai : 1 });
                                    recorddata.satuankonv = JSON.stringify(satkonv);
                                    var pengiriman_tgl = $('#pengiriman_tgl').val();
                                    var data = {
                                        barang_id : val,
                                        baranghna_tglawal : moment(pengiriman_tgl, "DD-MM-YYYY").format("YYYY-MM-DD")
                                    }
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "m_satuan_id", brg[0].satuan_nama);
                                    $.post("<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=getlasthna", data, function(result){
                                        var lasthna = JSON.parse(result);
                                        recorddata.baranghnadet_harga = lasthna.baranghnadet_harga;
                                        $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_harga", lasthna.baranghnadet_harga);
                                    });
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'combobox',
                        // createeditor: function (row, value, editor) {
                        //     var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            
                        //     var satuanSource = {
                        //             datatype: "array",
                        //             datafields: [
                        //                 { name: 'label', type: 'string' },
                        //                 { name: 'value', type: 'int' }
                        //             ],
                        //             localdata: JSON.parse(recorddata.satuankonv)
                        //     };
                        //     satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                        //         autoBind: true
                        //     });
                        //     editor.jqxDropDownList({
                        //         source: satuanAdapter,
                        //         displayMember: 'label',
                        //         valueMember: 'value'
                        //     });
                        // },
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            // console.log(recorddata)
                            // var satuanSource = {
                            //         datatype: "array",
                            //         datafields: [
                            //             { name: 'label', type: 'string' },
                            //             { name: 'value', type: 'int' }
                            //         ],
                            //         localdata: JSON.parse(recorddata.satuankonv)
                            // };
                            // satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                            //     autoBind: true
                            // });
                            // editor.jqxDropDownList({
                            //     source: satuanAdapter,
                            //     displayMember: 'label',
                            //     valueMember: 'value'
                            // });

                            // editor.on('select', function (event) {
                            //     var datasatkonv = satKonv;
                            //     if (event.args) {
                            //         var val = event.args.item.value;
                            //         var satuankonv = JSON.parse(recorddata.satuankonv);
                            //         var dtsatkonv = satuankonv.filter(p=>parseInt(p.value)==parseInt(val));
                            //         recorddata.satkonv_nilai = dtsatkonv[0].satkonv_nilai;
                            //         $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_harga", (parseFloat(recorddata.baranghnadet_harga) * parseFloat(dtsatkonv[0].satkonv_nilai)));
                            //     }
                            // });


                            /* lintang */
                            // var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            var sat = JSON.parse(recorddata.satuankonv);
                            var satkonv = [];
                            var m_barangsatuan_nama = datasatuan.filter(p=>p.satuan_id==recorddata.m_barangsatuan_id);
                            satkonv.push(m_barangsatuan_nama[0].satuan_nama);
                            for (let index = 0; index < sat.length; index++) {
                                const element = sat[index];
                                satkonv.push(element.satuan_nama)
                            }
                            
                            editor.jqxComboBox({
                                source: satkonv,
                                autoDropDownHeight : true
                            });
                            
                            var index = satkonv.indexOf(recorddata.m_satuanpengiriman_nama);
                            editor.jqxComboBox('selectIndex', index);
                            
                            editor.on('select', function (event) {
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
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_harga", (parseFloat(recorddata.baranghnadet_harga) * parseFloat(recorddata.satkonv_nilai)));
                                }
                            });

                        }, 
                    },
                    { text: 'Harga', datafield: 'pengirimandet_harga', cellsalign: 'right', editable : false},
                    { text: 'Qty', datafield: 'pengirimandet_qty', cellsalign: 'right', columntype: 'numberinput',
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            editor.on('keyup', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_subtotal", (parseFloat(recorddata.pengirimandet_harga) * parseFloat(val)));
                                $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(recorddata.pengirimandet_subtotal) - parseFloat(val)));
                                settotal();
                            });
                        }
                    },
                    { text: 'Subtotal', datafield: 'pengirimandet_subtotal', cellsalign: 'right', editable : false },
                    { text: 'Potongan', datafield: 'pengirimandet_potongan', cellsalign: 'right', columntype: 'numberinput', rendered: tooltiprenderer,
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            editor.on('keyup', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                if (event.keyCode == '120') {
                                    /* jika tekan tombol F9 */
                                    if (parseFloat(val)<=100) {
                                        editor.val(val/100*parseFloat(recorddata.pengirimandet_subtotal))
                                        $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(recorddata.pengirimandet_subtotal) - parseFloat(val/100*parseFloat(recorddata.pengirimandet_subtotal))));
                                    }
                                } else {
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(recorddata.pengirimandet_subtotal) - parseFloat(val)));
                                }
                                settotal();
                            });
                        } 
                    },
                    { text: 'Subtotal', datafield: 'pengirimandet_total', cellsalign: 'right', editable : false },
                ]
            });

            settotal();
        });

    });

    function settotal() {
        var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
        var rows = [];
        var total = 0;
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
            total = total + parseFloat(rec.pengirimandet_total);
        }
        total = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
        $('#pengiriman_total').val(total);
    }

    function renderJadwal(datajadwaldet) {
        var jadwalSource = {
            datatype: "array",
            localdata:  datajadwaldet,
            datafields: [
                { name: 'jadwal_id', type: 'int'},
                { name: 'm_rekanan_id', type: 'int'},
                { name: 'rekanan_nama', type: 'string'},
                { name: 'm_barang_id', type: 'int'},
                { name: 'm_satuan_id', type: 'int'},
                { name: 'satuan_nama', type: 'string'},
                { name: 'barang_nama', type: 'string'},
                { name: 'baranghnadet_harga', type: 'double'},
                { name: 'minggu', type: 'int'},
                { name: 'hari', type: 'string'},
                { name: 'jadwal_qty', type: 'int'},
                { name: 'sudahkirim', type: 'float'},
            ],
            id: 'jadwal_id',
        };
        var dataAdapter = new $.jqx.dataAdapter(jadwalSource, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        $("#jadwalGrid").jqxGrid({
            width: '100%',
            height: '100%',
            source: dataAdapter,                
            pageable: false,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            filterable: true,
            autoshowfiltericon: true,
            columns: [
                { text: 'Hari', datafield: 'hari', cellsalign: 'left', width : 50 },
                { text: 'Rekanan', datafield: 'rekanan_nama', cellsalign: 'left', width : 120 },
                { text: 'Minggu', datafield: 'minggu', cellsalign: 'center' , width : 50},
                { text: 'Nama Barang', datafield: 'barang_nama', cellsalign: 'left' },
                { text: 'Jml', datafield: 'jadwal_qty', cellsalign: 'right' , width : 80},
                { text: 'Sudah Kirim', datafield: 'sudahkirim', cellsalign: 'right', width : 80 },
            ]
        });

        $("#jadwalGrid").on('rowselect', function (event) {
            $("#pengirimanGrid").jqxGrid('clear');
            var record = event.args.row;
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":record.m_rekanan_id, "text": record.rekanan_nama }
            });
            var qty = record.jadwal_qty - record.sudahkirim;
            qty = qty <= 0 ? record.jadwal_qty : qty;
            var datarow = {
                pengirimandet_id : 0,
                t_pengiriman_id : 0,
                satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==record.m_barang_id)),
                m_barang_id : record.barang_nama,
                m_barangsatuan_id : record.m_satuan_id,
                baranghnadet_harga : record.baranghnadet_harga,
                pengirimandet_harga : record.baranghnadet_harga,
                m_satuan_id : record.satuan_nama,
                satkonv_nilai : 1,
                pengirimandet_qtyold : 0,
                pengirimandet_qty : qty,
                pengirimandet_subtotal : qty * record.baranghnadet_harga,
                pengirimandet_potongan : 0,
                pengirimandet_total : qty * record.baranghnadet_harga,
            };
            
            $("#pengirimanGrid").jqxGrid('addrow', null, datarow);
            $("#ModalJadwal").modal('toggle');
            settotal();
        });
    }
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_pengiriman_brg'">Kembali</button>
                    <button type="button" class="btn btn-default btn-sm" onclick="lihatJadwal()">Lihat Jadwal</button>
                </div>
                <form id="formpengiriman">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pengiriman_no">No. Pengiriman</label>
                                    <input type="hidden" id="pengiriman_id" name="pengiriman_id">
                                    <input type="text" class="form-control" id="pengiriman_no" name="pengiriman_no" readonly>
                                </div>
                                <?php 
                                $t_penagihan_no = isset($data->datapengiriman->t_penagihan_no) ? $data->datapengiriman->t_penagihan_no : '';
                                if ($t_penagihan_no <> '') {
                                    ?>
                                    <div class="form-group">
                                        <label for="penagihan_no">No. Penagihan</label>
                                        <input type="hidden" id="penagihan_id" name="penagihan_id">
                                        <input type="text" class="form-control" id="penagihan_no" name="penagihan_no" value="<?php echo $data->datapengiriman->t_penagihan_no ?>" readonly>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pengiriman_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="pengiriman_tgl" name="pengiriman_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask require>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;" require></select>
                                </div>
                                <div class="form-group">
                                    <label for="pengiriman_total">TOTAL</label>
                                    <input type="text" class="form-control" style="text-align: right;" id="pengiriman_total" name="pengiriman_total" value="0" readonly>
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
                            <div id="pengirimanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php 
                        $t_penagihan_no = isset($data->datapengiriman->t_penagihan_no) ? $data->datapengiriman->t_penagihan_no : '';
                        if ($delete <> '' &&  $t_penagihan_no == '') { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php 
                        if ((($create <> '' && isset($data->pengiriman_id) == 0) || ($update <> '' && $data->pengiriman_id > 0) && $t_penagihan_no == '')) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;" onclick="cetak()">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="ModalJadwal" tabindex="-1" role="dialog" aria-labelledby="ModalJadwalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card-body">
                            <div id="jadwalGrid" style="margin: 5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('[data-mask]').inputmask();
        var now = new Date();
        $('#pengiriman_tgl').val(moment(now).format('DD-MM-YYYY'));

        $("#m_rekanan_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getrekanan',
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

        $('#formpengiriman').submit(function (event) {
            event.preventDefault();
            var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                rows.push({
                    'pengirimandet_id' : rec.pengirimandet_id,
                    't_pengiriman_id' : $('#pengiriman_id').val(),
                    'm_barang_id' : parseInt(m_barang_id[0].value||0),
                    'm_barangsatuan_id' : parseInt(m_barang_id[0].satuan_id||0),
                    'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                    'satkonv_nilai' : parseFloat(satkonv_nilai),
                    'baranghnadet_harga' : parseFloat(rec.baranghnadet_harga),
                    'pengirimandet_harga' : parseFloat(rec.pengirimandet_harga),
                    'pengirimandet_qty' : rec.pengirimandet_qty,
                    'pengirimandet_qtyold' : rec.pengirimandet_qtyold,
                    'pengirimandet_subtotal' : parseFloat(rec.pengirimandet_subtotal),
                    'pengirimandet_potongan' : parseFloat(rec.pengirimandet_potongan),
                    'pengirimandet_total' : parseFloat(rec.pengirimandet_total),
                }); 
            }
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    pengiriman_id : $('#pengiriman_id').val(),
                    pengiriman_no : $('#pengiriman_no').val(),
                    pengiriman_tgl : moment($('#pengiriman_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        window.open('<?php echo BASE_URL;?>/controllers/C_pengiriman_brg.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "Pengiriman Berhasil disimpan", "success");
                        $("#ModalSatuan").modal('toggle');
                    } else {
                        swal("Info!", "Pengiriman Gagal disimpan", "error");
                    }
                }
            });
        })

        datapengiriman = JSON.parse('<?php echo $dataparse ?>');
        if(datapengiriman!==null) {
            var dat = datapengiriman.datapengiriman;
            $('#pengiriman_id').val(dat.pengiriman_id);
            $('#pengiriman_no').val(dat.pengiriman_no);
            $('#pengiriman_tgl').val(moment(dat.pengiriman_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            $("#m_rekanan_id").prop("disabled", true);
            $('#batal').removeAttr('disabled');
            console.log(datapengiriman)
            if (dat.t_penagihan_no !== '') {
                swal("Info!", "No. pengiriman " + dat.pengiriman_no + ", sudah dibuat penagihan dengan No. Penagihan " + dat.t_penagihan_no, "warning");
            }
        }

        $('#batal').on('click', function () {
            var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                rows.push({
                    'pengirimandet_id' : rec.pengirimandet_id,
                    't_pengiriman_id' : $('#pengiriman_id').val(),
                    'm_barang_id' : parseInt(m_barang_id[0].value||0),
                    'm_barangsatuan_id' : parseInt(m_barang_id[0].satuan_id||0),
                    'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                    'satkonv_nilai' : parseFloat(satkonv_nilai),
                    'baranghnadet_harga' : parseFloat(rec.baranghnadet_harga),
                    'pengirimandet_harga' : parseFloat(rec.pengirimandet_harga),
                    'pengirimandet_qty' : rec.pengirimandet_qty,
                    'pengirimandet_qtyold' : rec.pengirimandet_qtyold,
                    'pengirimandet_subtotal' : parseFloat(rec.pengirimandet_subtotal),
                    'pengirimandet_potongan' : parseFloat(rec.pengirimandet_potongan),
                    'pengirimandet_total' : parseFloat(rec.pengirimandet_total),
                });
            }

            swal({
                title: "Batalkan pengiriman " + $('#pengiriman_no').val(),
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
                    url: "<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        pengiriman_id : $('#pengiriman_id').val(),
                        pengiriman_no : $('#pengiriman_no').val(),
                        pengiriman_tgl : moment($('#pengiriman_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "Pengiriman Berhasil dibatalkan", "success");
                            $("#ModalSatuan").modal('toggle');
                        } else {
                            swal("Info!", "Pengiriman Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function cetak() {
        var pengiriman_id = $('#pengiriman_id').val();
        window.open('<?php echo BASE_URL;?>/controllers/C_pengiriman_brg.php?action=exportpdf&id=' + pengiriman_id);
    }

    function resetForm() {
        var now = new Date();
        $('#pengiriman_id').val(0);
        $('#pengiriman_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#pengiriman_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#pengirimanGrid").jqxGrid('clear');
    }

    function lihatJadwal() {
        var pengiriman_tgl = $('#pengiriman_tgl').val();
        var data = {
            tanggal : moment(pengiriman_tgl, "DD-MM-YYYY").format("YYYY-MM-DD")
        };
        $.post("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getjadwal", data, function(result){
            $('#ModalJadwal').modal('toggle');
            renderJadwal(JSON.parse(result));
        });
    }
</script>