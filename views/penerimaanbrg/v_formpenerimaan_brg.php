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
        datapenerimaan = JSON.parse('<?php echo $dataparse ?>');
        var datapenerimaandetail = [];
        if(datapenerimaan!==null) {
            datapenerimaandetail = datapenerimaan.datapenerimaandetail;
        }

        var generaterow = function (i) {
            var row = {};
            row["penerimaandet_id"] = 0;
            row["t_penerimaan_id"] = 0;
            row["satuankonv"] = '';
            row["m_barang_id"] = '';
            row["m_satuan_id"] = '';
            row["satkonv_nilai"] = 1;
            row["penerimaandet_qtyold"] = 0;
            row["penerimaandet_qty"] = 0;
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=getbarang", function(data, status){
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
            var datapenerimaandet = [];
            
            for (let index = 0; index < datapenerimaandetail.length; index++) {
                const element = datapenerimaandetail[index];
                let datdet = {
                    penerimaandet_id : element.penerimaandet_id,
                    t_penerimaan_id : element.t_penerimaan_id,
                    m_barang_id : element.m_barang_id,
                    m_satuan_id : element.m_satuan_id,
                    satkonv_nilai : element.satkonv_nilai,
                    penerimaandet_qtyold : element.penerimaandet_qty,
                    penerimaandet_qty : element.penerimaandet_qty,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id))
                };
                datapenerimaandet.push(datdet);
            }
            var penerimaanGridSource = {
                datatype: "array",
                localdata:  datapenerimaandet,
                datafields: [
                    { name: 'penerimaandet_id', type: 'int'},
                    { name: 't_penerimaan_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'satkonv_nilai'},
                    { name: 'penerimaandet_qtyold', type: 'float'},
                    { name: 'penerimaandet_qty', type: 'float'}
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

            var penerimaanAdapter = new $.jqx.dataAdapter(penerimaanGridSource);
            $("#penerimaanGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: penerimaanAdapter,
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
                        var commit = $("#penerimaanGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#penerimaanGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#penerimaanGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({penerimaandet_id : rechapus.penerimaandet_id});

                            var id = $("#penerimaanGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#penerimaanGrid").jqxGrid('deleterow', id);
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
                                var recorddata = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', row);
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
                                    $("#penerimaanGrid").jqxGrid('setcellvalue', row, "m_satuan_id", "");
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            var recorddata = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', row);
                            
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
                            var recorddata = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', row);
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
                    { text: 'Qty', datafield: 'penerimaandet_qty', cellsalign: 'right',
                        
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_penerimaan_brg'">Kembali</button>
                </div>
                <form id="formpenerimaan">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penerimaan_no">No. Penerimaan</label>
                                    <input type="hidden" id="penerimaan_id" name="penerimaan_id">
                                    <input type="text" class="form-control" id="penerimaan_no" name="penerimaan_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penerimaan_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="penerimaan_tgl" name="penerimaan_tgl"
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
                            <div id="penerimaanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->penerimaan_id) == 0) || ($update <> '' && $data->penerimaan_id > 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;">Cetak</button>
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
        $('#penerimaan_tgl').val(moment(now).format('DD-MM-YYYY'));

        $("#m_rekanan_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=getrekanan',
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

        $('#formpenerimaan').submit(function (event) {
            event.preventDefault();
            var griddata = $('#penerimaanGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "Penerimaan Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                if (m_barang_id.length > 0 && rec.penerimaandet_qty > 0 && m_satuan_id.length > 0) {
                    rows.push({
                        'penerimaandet_id' : rec.penerimaandet_id,
                        't_penerimaan_id' : $('#penerimaan_id').val(),
                        'm_barang_id' : parseInt(m_barang_id[0].value||0),
                        'm_barangsatuan_id' : parseInt(m_barang_id[0].satuan_id||0),
                        'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                        'satkonv_nilai' : parseFloat(satkonv_nilai),
                        'penerimaandet_qty' : rec.penerimaandet_qty,
                        'penerimaandet_qtyold' : rec.penerimaandet_qtyold,
                    });    
                } else if (rec.penerimaandet_qty == 0 && m_satuan_id.length > 0) {
                    swal("Info!", "Penerimaan Gagal disimpan, terdapat isian dengan qty kosong ", "error");
                    return false;
                } else if (m_satuan_id.length < 1) {
                    swal("Info!", "Penerimaan Gagal disimpan, terdapat isian dengan satuan kosong ", "error");
                    return false;
                }
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    penerimaan_id : $('#penerimaan_id').val(),
                    penerimaan_no : $('#penerimaan_no').val(),
                    penerimaan_tgl : moment($('#penerimaan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    if (res == 200) {
                        resetForm();
                        swal("Info!", "Penerimaan Berhasil disimpan", "success");
                        
                    } else {
                        swal("Info!", "Penerimaan Gagal disimpan", "error");
                    }
                }
            });
        })

        datapenerimaan = JSON.parse('<?php echo $dataparse ?>');
        if(datapenerimaan!==null) {
            var dat = datapenerimaan.datapenerimaan;
            $('#penerimaan_id').val(dat.penerimaan_id);
            $('#penerimaan_no').val(dat.penerimaan_no);
            $('#penerimaan_tgl').val(moment(dat.penerimaan_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            $('#batal').removeAttr('disabled')
        }

        $('#batal').on('click', function() {
            var griddata = $('#penerimaanGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "Penerimaan Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            swal({
                title: "Batalkan penerimaan " + $('#penerimaan_no').val(),
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
                var rows = [];
                for (var i = 0; i < griddata.rowscount; i++){
                    var rec = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', i);
                    m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                    m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                    dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                    
                    satkonv_nilai = 1;
                    if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                    
                    rows.push({
                        'penerimaandet_id' : rec.penerimaandet_id,
                        't_penerimaan_id' : $('#penerimaan_id').val(),
                        'm_barang_id' : parseInt(m_barang_id[0].value||0),
                        'm_barangsatuan_id' : parseInt(m_barang_id[0].satuan_id||0),
                        'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                        'satkonv_nilai' : parseFloat(satkonv_nilai),
                        'penerimaandet_qty' : rec.penerimaandet_qty,
                        'penerimaandet_qtyold' : rec.penerimaandet_qtyold,
                    }); 
                }

                $.ajax({
                    url: "<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        penerimaan_id : $('#penerimaan_id').val(),
                        penerimaan_no : $('#penerimaan_no').val(),
                        penerimaan_tgl : moment($('#penerimaan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "Penerimaan Berhasil dibatalkan", "success");
                            
                        } else {
                            swal("Info!", "Penerimaan Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function resetForm() {
        var now = new Date();
        $('#penerimaan_id').val(0);
        $('#penerimaan_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#penerimaan_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#penerimaanGrid").jqxGrid('clear');
    }
</script>