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
        databaranghna = JSON.parse('<?php echo $dataparse ?>');
        var databaranghnadetail = [];
        if(databaranghna!==null) {
            databaranghnadetail = databaranghna.databaranghnadetail;
        }

        var generaterow = function (i) {
            var row = {};
            row["baranghnadet_id"] = 0;
            row["t_baranghna_id"] = 0;
            row["m_barang_id"] = '';
            row["baranghnadet_harga"] = 0;
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=getbarang", function(data, status){
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
            // prepare the data
            var databaranghnadet = [];
            
            for (let index = 0; index < databaranghnadetail.length; index++) {
                const element = databaranghnadetail[index];
                let datdet = {
                    baranghnadet_id : element.baranghnadet_id,
                    t_baranghna_id : element.t_baranghna_id,
                    m_barang_id : element.m_barang_id,
                    m_satuan_id : element.m_satuan_id,
                    satkonv_nilai : element.satkonv_nilai,
                    baranghnadet_harga : element.baranghnadet_harga,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id))
                };
                databaranghnadet.push(datdet);
            }
            var baranghnaGridSource = {
                datatype: "array",
                localdata:  databaranghnadet,
                datafields: [
                    { name: 'baranghnadet_id', type: 'int'},
                    { name: 't_baranghna_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'satkonv_nilai'},
                    { name: 'baranghnadet_harga', type: 'float'}
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

            var baranghnaAdapter = new $.jqx.dataAdapter(baranghnaGridSource);
            $("#baranghnaGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: baranghnaAdapter,
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
                        var commit = $("#baranghnaGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#baranghnaGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#baranghnaGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#baranghnaGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({baranghnadet_id : rechapus.baranghnadet_id});

                            var id = $("#baranghnaGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#baranghnaGrid").jqxGrid('deleterow', id);
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
                                var recorddata = $('#baranghnaGrid').jqxGrid('getrenderedrowdata', row);
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
                                    $("#baranghnaGrid").jqxGrid('setcellvalue', row, "m_satuan_id", "");
                                }
                            });
                        },
                    },
                    { text: 'Harga', datafield: 'baranghnadet_harga', cellsalign: 'right' },
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_hargabrg'">Kembali</button>
                </div>
                <form id="formbaranghna">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="baranghna_no">No. Transaksi HNA</label>
                                    <input type="hidden" id="baranghna_id" name="baranghna_id">
                                    <input type="text" class="form-control" id="baranghna_no" name="baranghna_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="baranghna_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="baranghna_tgl" name="baranghna_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask require>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="baranghnaGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->baranghna_id) == 0) || ($update <> '' && $data->baranghna_id > 0)) { ?>
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
        $('#baranghna_tgl').val(moment(now).format('DD-MM-YYYY'));

        $('#formbaranghna').submit(function (event) {
            event.preventDefault();
            var griddata = $('#baranghnaGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "baranghna Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#baranghnaGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                rows.push({
                    'baranghnadet_id' : rec.baranghnadet_id,
                    't_baranghna_id' : $('#baranghna_id').val(),
                    'm_barang_id' : parseInt(m_barang_id[0].value||0),
                    'baranghnadet_harga' : rec.baranghnadet_harga,
                }); 
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    baranghna_id : $('#baranghna_id').val(),
                    baranghna_no : $('#baranghna_no').val(),
                    baranghna_tgl : moment($('#baranghna_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    if (res == 200) {
                        resetForm();
                        swal("Info!", "baranghna Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "baranghna Gagal disimpan", "error");
                    }
                }
            });
        })

        databaranghna = JSON.parse('<?php echo $dataparse ?>');
        if(databaranghna!==null) {
            var dat = databaranghna.databaranghna;
            $('#baranghna_id').val(dat.baranghna_id);
            $('#baranghna_no').val(dat.baranghna_no);
            $('#baranghna_tgl').val(moment(dat.baranghna_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $('#batal').removeAttr('disabled')
        }

        $('#batal').on('click', function() {
            var griddata = $('#baranghnaGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "baranghna Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            swal({
                title: "Batalkan baranghna " + $('#baranghna_no').val(),
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
                    var rec = $('#baranghnaGrid').jqxGrid('getrenderedrowdata', i);
                    m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                    m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                    dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                    
                    satkonv_nilai = 1;
                    if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                    
                    rows.push({
                        'baranghnadet_id' : rec.baranghnadet_id,
                        't_baranghna_id' : $('#baranghna_id').val(),
                        'm_barang_id' : parseInt(m_barang_id[0].value||0),
                        'baranghnadet_harga' : rec.baranghnadet_harga,
                    }); 
                }

                $.ajax({
                    url: "<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        baranghna_id : $('#baranghna_id').val(),
                        baranghna_no : $('#baranghna_no').val(),
                        baranghna_tgl : moment($('#baranghna_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "baranghna Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "baranghna Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function resetForm() {
        var now = new Date();
        $('#baranghna_id').val(0);
        $('#baranghna_no').val('');
        $('#baranghna_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#baranghnaGrid").jqxGrid('clear');
    }
</script>