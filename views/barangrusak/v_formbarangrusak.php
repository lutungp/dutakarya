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
        databarangrusak = JSON.parse('<?php echo $dataparse ?>');
        var databarangrusakdetail = [];
        if(databarangrusak!==null) {
            databarangrusakdetail = databarangrusak.databarangrusakdetail;
        }

        var generaterow = function (i) {
            var row = {
                'barangrusakdet_id' : 0,
                't_barangrusak_id' : 0,
                'm_barang_id' : 0,
                'barang_nama' : '',
                'm_satuan_id' : 0,
                'satuan_nama' : '',
                'barangrusakdet_qtyold' : 0,
                'barangrusakdet_qty' : 0,
                'barangrusakdet_harga' : 0,
                'barangrusakdet_subtotal' : 0,
                'barangrusakdet_alasan' : null
            };
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=getbarang", function(data, status){
            databarang = JSON.parse(data);
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
            var databarangrusakdet = [];
            
            for (let index = 0; index < databarangrusakdetail.length; index++) {
                const element = databarangrusakdetail[index];
                let datdet = {
                    barangrusakdet_id : element.barangrusakdet_id,
                    t_barangrusak_id : element.t_barangrusak_id,
                    m_barang_id : element.m_barang_id,
                    barang_nama : element.barang_nama,
                    satuan_nama : element.satuan_nama,
                    m_satuan_id : element.m_satuan_id,
                    barangrusakdet_qtyold : element.barangrusakdet_qty,
                    barangrusakdet_qty : element.barangrusakdet_qty,
                    barangrusakdet_harga : element.barangrusakdet_harga,
                    barangrusakdet_subtotal : element.barangrusakdet_subtotal,
                    barangrusakdet_alasan : element.barangrusakdet_alasan,
                };
                databarangrusakdet.push(datdet);
            }
            
            var barangrusakGridSource = {
                datatype: "array",
                localdata:  databarangrusakdet,
                datafields: [
                    { name: 'barangrusakdet_id', type: 'int'},
                    { name: 't_barangrusak_id', type: 'int'},
                    { name: 'barang_nama', value: 'barang_nama', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_barang_id', type: 'int'},
                    { name: 'satuan_nama', type: 'string'},
                    { name: 'm_satuan_id', type: 'int'},
                    { name: 'barangrusakdet_qtyold', type: 'float'},
                    { name: 'barangrusakdet_qty', type: 'float'},
                    { name: 'barangrusakdet_harga', type: 'float'},
                    { name: 'barangrusakdet_subtotal', type: 'float'},
                    { name: 'barangrusakdet_alasan', type: 'string'},
                ]
            };

            var barangrusakAdapter = new $.jqx.dataAdapter(barangrusakGridSource);
            $("#barangrusakGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: barangrusakAdapter,
                editable: true,
                altrows: true,
                showtoolbar: true,
                showstatusbar: true,
                statusbarheight: 50,
                showaggregates: true,
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
                        var commit = $("#barangrusakGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#barangrusakGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#barangrusakGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#barangrusakGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({barangrusakdet_id : rechapus.barangrusakdet_id});

                            var id = $("#barangrusakGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#barangrusakGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    {
                        text: 'Barang', datafield: 'barang_nama', displayfield: 'barang_nama', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({
                                source: barangAdapter,
                                displayMember: 'label',
                                valueMember: 'value'
                            });
                            editor.on('select', function (event) {
                                var recorddata = $('#barangrusakGrid').jqxGrid('getrenderedrowdata', row);
                                if (event.args) {
                                    var val = event.args.item.value;
                                    brg = barang.filter(p=>parseInt(p.value)==val);
                                    recorddata.m_barang_id = parseInt(val);
                                    recorddata.m_satuan_id = parseInt(brg[0].satuan_id);
                                    $("#barangrusakGrid").jqxGrid('setcellvalue', row, "satuan_nama", brg[0].satuan_nama);
                                    let param = {
                                        barang_id : parseInt(val),
                                        tanggal : moment($('#barangrusak_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                                        m_rekanan_id : $('#m_rekanan_id').val(),
                                    };
                                    $.post("<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=gethargakontrak", param, function(result){
                                        let res = JSON.parse(result);
                                        $("#barangrusakGrid").jqxGrid('setcellvalue', row, "barangrusakdet_harga", res.hargakontrakdet_harga);
                                        $("#barangrusakGrid").jqxGrid('setcellvalue', row, "barangrusakdet_subtotal", (res.hargakontrakdet_harga * recorddata.barangrusakdet_qty));
                                    });
                                }
                            });
                        },
                        width : 300
                    },
                    { text: 'Satuan', datafield: 'satuan_nama', displayfield: 'satuan_nama', width : 100 },
                    { 
                        text: 'Qty', datafield: 'barangrusakdet_qty', cellsalign: 'right', editable : true, width : 100,
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            editor.on('keyup', function (event) {
                                var recorddata = $('#barangrusakGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                var subtotal = recorddata.barangrusakdet_harga * val;
                                $("#barangrusakGrid").jqxGrid('setcellvalue', row, "barangrusakdet_subtotal", subtotal);
                            });
                        }
                    },
                    { text: 'Harga', datafield: 'barangrusakdet_harga', cellsalign: 'right', editable : false, width : 150 },
                    { 
                        text: 'Subtotal', datafield: 'barangrusakdet_subtotal', cellsalign: 'right', editable : false, width : 150,
                        cellsformat: 'F',
                        aggregates: ['sum'],
                        aggregatesrenderer: function (aggregates, column, element) {
                            var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                            var subtotal = 0;
                            $.each(aggregates, function (key, value) {
                                subtotal = parseFloat(subtotal) + parseFloat(value);
                                // var name = key == 'sum' ? 'Sum' : 'Avg';
                                renderstring += '<div style="padding:5px;font-size:16px;"><b>' + value + '</b></div>';
                            });
                            renderstring += "</div>";
                            return renderstring;
                        }
                    },
                    { text: 'Keterangan', datafield: 'barangrusakdet_alasan', cellsalign: 'left', editable : true },
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_barangrusak'">Kembali</button>
                </div>
                <form id="formbarangrusak">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barangrusak_no">No. barangrusak</label>
                                    <input type="hidden" id="barangrusak_id" name="barangrusak_id">
                                    <input type="text" class="form-control" id="barangrusak_no" name="barangrusak_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barangrusak_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="barangrusak_tgl" name="barangrusak_tgl"
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
                        <div class="row">
                            <div id="barangrusakGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->barangrusak_id) == 0) || ($update <> '' && $data->barangrusak_id > 0)) { ?>
                        <button type="submit" id="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <!-- <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;">Cetak</button> -->
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
        $('#barangrusak_tgl').val(moment(now).format('DD-MM-YYYY'));

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

        $('#formbarangrusak').submit(function (event) {
            event.preventDefault();
            var griddata = $('#barangrusakGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "barangrusak Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#barangrusakGrid').jqxGrid('getrenderedrowdata', i);
                if (rec.m_barang_id > 0 && rec.barangrusakdet_qty > 0 && rec.m_satuan_id > 0) {
                    rows.push({
                        'barangrusakdet_id': rec.barangrusakdet_id,
                        't_barangrusak_id': rec.t_barangrusak_id,
                        'm_barang_id': rec.m_barang_id,
                        'barang_nama': rec.barang_nama,
                        'm_satuan_id': rec.m_satuan_id,
                        'satuan_nama': rec.satuan_nama,
                        'barangrusakdet_harga': rec.barangrusakdet_harga,
                        'barangrusakdet_qty': rec.barangrusakdet_qty,
                        'barangrusakdet_qtyold': rec.barangrusakdet_qtyold,
                        'barangrusakdet_subtotal': rec.barangrusakdet_subtotal,
                        'barangrusakdet_alasan' : rec.barangrusakdet_alasan
                    });    
                } else if (rec.barangrusakdet_qty == 0 && rec.m_satuan_id > 0) {
                    swal("Info!", "barangrusak Gagal disimpan, terdapat isian dengan qty kosong ", "error");
                    return false;
                } else if (rec.m_satuan_id < 1) {
                    swal("Info!", "barangrusak Gagal disimpan, terdapat isian dengan satuan kosong ", "error");
                    return false;
                }
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    barangrusak_id : $('#barangrusak_id').val(),
                    barangrusak_no : $('#barangrusak_no').val(),
                    barangrusak_tgl : moment($('#barangrusak_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    let result = JSON.parse(res)
                    if (parseInt(result['code']) == 200) {
                        resetForm();
                        swal("Info!", "barangrusak Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "barangrusak Gagal disimpan", "error");
                    }
                }
            });
        })

        databarangrusak = JSON.parse('<?php echo $dataparse ?>');
        if(databarangrusak!==null) {
            var dat = databarangrusak.databarangrusak;
            $('#barangrusak_id').val(dat.barangrusak_id);
            $('#barangrusak_no').val(dat.barangrusak_no);
            $('#barangrusak_tgl').val(moment(dat.barangrusak_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            
            if (dat.barangrusak_no !== '') {
                $('#batal').attr('disabled', true);
                $('#submit').attr('disabled', true);
                var penagihanstr = dat.t_penagihan_no == null || dat.t_penagihan_no == '' ? '' : ", sudah dibuat penagihan dengan No. Penagihan " + dat.t_penagihan_no;
                swal("Info!", "No. Barang Rusak " + dat.barangrusak_no + penagihanstr, "warning");
            } else {
                $('#batal').removeAttr('disabled');
            }
        }

        $('#batal').on('click', function() {
            var griddata = $('#barangrusakGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "barangrusak Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            swal({
                title: "Batalkan barangrusak " + $('#barangrusak_no').val(),
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
                    var rec = $('#barangrusakGrid').jqxGrid('getrenderedrowdata', i);
                    m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                    m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                    dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                    
                    satkonv_nilai = 1;
                    if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                    
                    rows.push({
                        'barangrusakdet_id' : rec.barangrusakdet_id,
                        't_barangrusak_id' : $('#barangrusak_id').val(),
                        'm_barang_id' : parseInt(m_barang_id[0].value||0),
                        'm_barangsatuan_id' : parseInt(m_barang_id[0].satuan_id||0),
                        'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                        'satkonv_nilai' : parseFloat(satkonv_nilai),
                        'barangrusakdet_qty' : rec.barangrusakdet_qty,
                        'barangrusakdet_qtyold' : rec.barangrusakdet_qtyold,
                    }); 
                }

                $.ajax({
                    url: "<?php echo BASE_URL ?>/controllers/C_barangrusak.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        barangrusak_id : $('#barangrusak_id').val(),
                        barangrusak_no : $('#barangrusak_no').val(),
                        barangrusak_tgl : moment($('#barangrusak_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        let result = JSON.parse(res)
                        if (parseInt(result['code']) == 200) {
                            resetForm();
                            swal("Info!", "barangrusak Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "barangrusak Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function resetForm() {
        var now = new Date();
        $('#barangrusak_id').val(0);
        $('#barangrusak_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#barangrusak_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#barangrusakGrid").jqxGrid('clear');
    }
</script>