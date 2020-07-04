<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
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
            row["m_satuan_id"] = '';
            row["satkonv_nilai"] = 1;
            row["pengirimandet_qtyold"] = 0;
            row["pengirimandet_qty"] = 0;
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
                    m_satuan_id : element.m_satuan_id,
                    satkonv_nilai : element.satkonv_nilai,
                    pengirimandet_qtyold : element.pengirimandet_qty,
                    pengirimandet_qty : element.pengirimandet_qty,
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
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'satkonv_nilai'},
                    { name: 'pengirimandet_qtyold', type: 'float'},
                    { name: 'pengirimandet_qty', type: 'float'}
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
            $("#pengirimanGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: pengirimanAdapter,
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
                                var datasatkonv = data['satuan_konversi'];
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
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "m_satuan_id", "");
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            
                            var satuanSource = {
                                    datatype: "array",
                                    datafields: [
                                        { name: 'label', type: 'string' },
                                        { name: 'value', type: 'int' }
                                    ],
                                    localdata: JSON.parse(recorddata.satuankonv)
                            };
                            satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                                autoBind: true
                            });
                            editor.jqxDropDownList({
                                source: satuanAdapter,
                                displayMember: 'label',
                                valueMember: 'value'
                            });
                        },
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            var satuanSource = {
                                    datatype: "array",
                                    datafields: [
                                        { name: 'label', type: 'string' },
                                        { name: 'value', type: 'int' }
                                    ],
                                    localdata: JSON.parse(recorddata.satuankonv)
                            };
                            satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                                autoBind: true
                            });
                            editor.jqxDropDownList({
                                source: satuanAdapter,
                                displayMember: 'label',
                                valueMember: 'value'
                            });
                        }, 
                        width : 300,
                    },
                    { text: 'Qty', datafield: 'pengirimandet_qty', cellsalign: 'right',
                        
                    },
                ]
            });
        });
    });
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_pengiriman_brg'">Kembali</button>
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
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->pengiriman_id) == 0) || ($update <> '' && $data->pengiriman_id > 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="submit" class="btn btn-default btn-sm float-right" style="margin-right: 5px;">Cetak</button>
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
                    'pengirimandet_qty' : rec.pengirimandet_qty,
                    'pengirimandet_qtyold' : rec.pengirimandet_qtyold,
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
                    if (res == 200) {
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
            $('#batal').removeAttr('disabled')
        }
    });

    function resetForm() {
        var now = new Date();
        $('#pengiriman_id').val(0);
        $('#pengiriman_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#pengiriman_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#pengirimanGrid").jqxGrid('clear');
    }
</script>