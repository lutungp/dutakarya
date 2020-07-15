<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<script>
    var barangAdapter = false;
    var barang = [];
    var hapusdetail = [];
    $(document).ready(function(){
        renderGrid();
    });

    function renderGrid() {
        var generaterow = function (i) {
            var row = {};
            row["baranghnadet_id"] = 0;
            row["t_baranghna_id"] = 0;
            row["m_barang_id"] = '';
            row["baranghnadet_last"] = 0;
            row["baranghnadet_harga"] = 0;
            return row;
        }
        databaranghna = JSON.parse('<?php echo $dataparse ?>');
        var databaranghnadetail = [];
        if(databaranghna!==null) {
            databaranghnadetail = databaranghna.databaranghnadetail;
        }
        $.get("<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=getbarang", function(data, status){
            data = JSON.parse(data);
            databarang = data['barang'];
            for (let index = 0; index < databarang.length; index++) {
                const element = databarang[index];
                barang.push({ value: element.barang_id, label: element.barang_nama, satuan_id : element.m_satuan_id, satuan_nama : element.satuan_nama });
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
                    baranghnadet_last : element.baranghnadet_last,
                    baranghnadet_harga : element.baranghnadet_harga,
                };
                databaranghnadet.push(datdet);
            }
            var baranghnaGridSource = {
                datatype: "array",
                localdata:  databaranghnadet,
                datafields: [
                    { name: 'baranghnadet_id', type: 'int'},
                    { name: 't_baranghna_id', type: 'int'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'baranghnadet_last', type: 'float'},
                    { name: 'baranghnadet_harga', type: 'float'}
                ],
                addrow: function (rowid, rowdata, position, commit) {
                    commit(true);
                },
                deleterow: function (rowid, commit) {
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
                                if (event.args) {
                                    var val = event.args.item.value;
                                    var baranghna_tglawal = $('#baranghna_tglawal').val()
                                    var data = {
                                        barang_id : val,
                                        baranghna_tglawal : moment(baranghna_tglawal, "DD-MM-YYYY").format("YYYY-MM-DD")
                                    }
                                    $.post("<?php echo BASE_URL ?>/controllers/C_hargabrg.php?action=getlasthna", data, function(result){
                                        var lasthna = JSON.parse(result);
                                        $("#baranghnaGrid").jqxGrid('setcellvalue', row, "baranghnadet_last", lasthna.baranghnadet_harga);
                                    });
                                }
                            });
                        },
                    },
                    { text: 'Harga Sebelumnya', datafield: 'baranghnadet_last', cellsalign: 'right', width : 200 },
                    { text: 'Harga', datafield: 'baranghnadet_harga', cellsalign: 'right', width : 200 },
                ]
            });
        });
    }
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
                                    <label for="baranghna_tglawal">Tanggal Berlaku</label>
                                    <input type="text" class="form-control baranghna_tglawal" id="baranghna_tglawal" name="baranghna_tglawal"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask 
                                    onchange="renderGrid()"
                                    require>
                                </div>
                            </div>
                            <div class="col-md-4">
                                
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
        $('#baranghna_tglawal').val(moment(now).format('DD-MM-YYYY'));

        $('#formbaranghna').submit(function (event) {
            event.preventDefault();
            var griddata = $('#baranghnaGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "HNA Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#baranghnaGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                
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
                    baranghna_tglawal : moment($('#baranghna_tglawal').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    if (res == 200) {
                        resetForm();
                        swal("Info!", "HNA Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "HNA Gagal disimpan", "error");
                    }
                }
            });
        })

        databaranghna = JSON.parse('<?php echo $dataparse ?>');
        if(databaranghna!==null) {
            var dat = databaranghna.databaranghna;
            $('#baranghna_id').val(dat.baranghna_id);
            $('#baranghna_no').val(dat.baranghna_no);
            console.log(dat.baranghna_tglawal)
            $('#baranghna_tglawal').val(moment(dat.baranghna_tglawal, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $('#batal').removeAttr('disabled')
        }

        $('#batal').on('click', function() {
            var griddata = $('#baranghnaGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "HNA Gagal disimpan, detail barang masih kosong", "warning");
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
                        baranghna_tglawal : moment($('#baranghna_tglawal').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "HNA Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "HNA Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });

        databaranghna = JSON.parse('<?php echo $dataparse ?>');
        if(databaranghna!==null) {
            var dat = databaranghna.databaranghna;
            $('#baranghna_id').val(dat.baranghna_id);
            $('#baranghna_no').val(dat.baranghna_no);
            $('#baranghna_tglawal').val(moment(dat.baranghna_tglawal, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $('#batal').removeAttr('disabled')
        }
    });

    function resetForm() {
        var now = new Date();
        $('#baranghna_id').val(0);
        $('#baranghna_no').val('');
        $('#baranghna_tglawal').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#baranghnaGrid").jqxGrid('clear');
    }
</script>