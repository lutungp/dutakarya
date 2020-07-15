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
        hargakontrak = JSON.parse('<?php echo $dataparse ?>');
        var hargakontrakdetail = [];
        if(hargakontrak!==null) {
            hargakontrakdetail = hargakontrak.hargakontrakdetail;
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
            var hargakontrakdet = [];
            for (let index = 0; index < hargakontrakdetail.length; index++) {
                const element = hargakontrakdetail[index];
                let datdet = {
                    hargakontrakdet_id : element.hargakontrakdet_id,
                    t_hargakontrak_id : element.t_hargakontrak_id,
                    m_barang_id : element.m_barang_id,
                    m_barangsatuan_id : element.m_barangsatuan_id,
                    m_satuan_id : element.m_satuan_id,
                    satkonv_nilai : element.satkonv_nilai,
                    hargakontrakdet_ppn : element.hargakontrakdet_ppn,
                    hargakontrakdet_harga : element.hargakontrakdet_harga,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id))
                };
                hargakontrakdet.push(datdet);
            }
            var hargakontrakGridSource = {
                datatype: "array",
                localdata:  hargakontrakdet,
                datafields: [
                    { name: 'hargakontrakdet_id', type: 'int'},
                    { name: 't_hargakontrak_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_barangsatuan_id', type: 'int'},
                    { name: 'm_satuan_id'},
                    { name: 'satkonv_nilai'},
                    { name: 'hargakontrakdet_ppn', type: 'float'},
                    { name: 'hargakontrakdet_harga', type: 'float'},
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

            var generaterow = function (i) {
                var row = {
                    hargakontrakdet_id : 0,
                    t_hargakontrak_id : 0,
                    satuankonv : '',
                    m_barang_id : '',
                    m_barangsatuan_id : 0,
                    m_satuan_id : '',
                    satkonv_nilai : 0,
                    hargakontrakdet_ppn : false,
                    hargakontrakdet_harga : 0,
                };
                return row;
            }

            var hargakontrakAdapter = new $.jqx.dataAdapter(hargakontrakGridSource);
            $("#hargakontrakGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: hargakontrakAdapter,
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
                        var commit = $("#hargakontrakGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#hargakontrakGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#hargakontrakGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#hargakontrakGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({hargakontrakdet_id : rechapus.hargakontrakdet_id});

                            var id = $("#hargakontrakGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#hargakontrakGrid").jqxGrid('deleterow', id);
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
                                var recorddata = $('#hargakontrakGrid').jqxGrid('getrenderedrowdata', row);
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
                                    satkonv.push({ value: parseInt(brg[0].satuan_id), label: brg[0].satuan_nama, satkonv_nilai : 1 });
                                    recorddata.satuankonv = JSON.stringify(satkonv);
                                    $("#hargakontrakGrid").jqxGrid('setcellvalue', row, "m_satuan_id", brg[0].satuan_nama);
                                }
                            });
                        },
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'combobox',
                        createeditor: function (row, value, editor) {
                            var recorddata = $('#hargakontrakGrid').jqxGrid('getrenderedrowdata', row);
                            var sat = JSON.parse(recorddata.satuankonv);
                            var satkonv = [];
                            for (let index = 0; index < sat.length; index++) {
                                const element = sat[index];
                                satkonv.push(element.label)
                            }
                            editor.jqxComboBox({
                                source: satkonv,
                                autoDropDownHeight : true
                            });
                        },
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#hargakontrakGrid').jqxGrid('getrenderedrowdata', row);
                            var sat = JSON.parse(recorddata.satuankonv);
                            var satkonv = [];
                            for (let index = 0; index < sat.length; index++) {
                                const element = sat[index];
                                satkonv.push(element.label)
                            }
                            editor.jqxComboBox({
                                source: satkonv,
                                autoDropDownHeight : true
                            });
                        },
                    },
                    { text: 'Harga', datafield: 'hargakontrakdet_harga', cellsalign: 'right', width : 200 },
                    { text: 'PPN', datafield: 'hargakontrakdet_ppn', threestatecheckbox: false, columntype: 'checkbox', width: 70 },
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_kontrakrekanan'">Kembali</button>
                </div>
                <form id="formhargakontrak">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hargakontrak_no">No. Kontrak</label>
                                    <input type="hidden" id="hargakontrak_id" name="hargakontrak_id">
                                    <input type="text" class="form-control" id="hargakontrak_no" name="hargakontrak_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hargakontrak_tgl">Tanggal Berlaku</label>
                                    <input type="text" class="form-control hargakontrak_tgl" id="hargakontrak_tgl" name="hargakontrak_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask 
                                    onchange="renderGrid()"
                                    require>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;" require></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="hargakontrakGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php 
                        $hargakontrak_id = isset($data->hargakontrak_id) ? $data->hargakontrak_id : 0;
                        if ($delete <> '') { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && $hargakontrak_id == 0) || ($update <> '' && $hargakontrak_id > 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="submit" class="btn btn-default btn-sm float-right" style="margin-right: 5px;">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        $('[data-mask]').inputmask();
        var now = new Date();
        $('#hargakontrak_tgl').val(moment(now).format('DD-MM-YYYY'));

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

        $('#formhargakontrak').submit(function (event) {
            event.preventDefault();
            var griddata = $('#hargakontrakGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#hargakontrakGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                rows.push({
                    hargakontrakdet_id : rec.hargakontrakdet_id,
                    t_hargakontrak_id : rec.t_hargakontrak_id,
                    m_barang_id : parseInt(m_barang_id[0].value||0),
                    m_barangsatuan_id : parseInt(m_barang_id[0].satuan_id||0),
                    m_satuan_id : parseInt(m_satuan_id[0].value||0),
                    satkonv_nilai : parseFloat(satkonv_nilai),
                    hargakontrakdet_ppn : rec.hargakontrakdet_ppn,
                    hargakontrakdet_harga : parseFloat(rec.hargakontrakdet_harga),
                }); 
            }
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_kontrakrekanan.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    hargakontrak_id : $('#hargakontrak_id').val(),
                    hargakontrak_no : $('#hargakontrak_no').val(),
                    hargakontrak_tgl : moment($('#hargakontrak_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (parseInt(res) == 200) {
                        resetForm();
                        swal("Info!", "Kontrak Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "Kontrak Gagal disimpan", "error");
                    }
                }
            });
        });

        if(hargakontrak!==null) {
            var dat = hargakontrak.hargakontrak;
            $('#hargakontrak_id').val(dat.hargakontrak_id);
            $('#hargakontrak_no').val(dat.hargakontrak_no);
            $('#hargakontrak_tgl').val(moment(dat.hargakontrak_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            $("#m_rekanan_id").prop("disabled", true);
            $('#batal').prop("disabled", false);
        }

        $('#batal').on('click', function() {
            var griddata = $('#hargakontrakGrid').jqxGrid('getdatainformation');
            swal({
                title: "Batalkan kotrak " + $('#hargakontrak_no').val(),
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
                    url: "<?php echo BASE_URL ?>/controllers/C_kontrakrekanan.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        hargakontrak_id : $('#hargakontrak_id').val(),
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "Kontrak Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "Kontrak Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });

    });

    function resetForm() {
        var now = new Date();
        $('#hargakontrak_id').val(0);
        $('#hargakontrak_no').val('');
        $('#hargakontrak_tgl').val(moment(now).format('DD-MM-YYYY'));
        $('#m_rekanan_id').empty();
        $("#hargakontrakGrid").jqxGrid('clear');
    }
</script>