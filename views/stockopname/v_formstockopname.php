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
        datastockopname = JSON.parse('<?php echo $dataparse ?>');
        var datastockopnamedetail = [];
        if(datastockopname!==null) {
            datastockopnamedetail = datastockopname.datastockopnamedetail;
        }
        var generaterow = function (i) {
            var row = {
                stockopnamedet_id : 0,
                t_stockopname_id : 0,
                m_barang_id : 0,
                barang_nama : '',
                m_satuan_id : 0,
                satuan_nama : '',
                t_barangtrans_akhir : 0,
                stockopnamedet_qty : 0,
                satkonv_nilai : 1,
                satuankonv : []
            };
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=getbarang", function(data, status){
            data = JSON.parse(data);
            databarang = data['barang'];
            satKonv = data['satuan_konversi'];
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
            var datastockopnamedet = [];
            
            for (let index = 0; index < datastockopnamedetail.length; index++) {
                const element = datastockopnamedetail[index];
                let datdet = {
                    stockopnamedet_id : element.stockopnamedet_id,
                    t_stockopname_id : element.t_stockopname_id,
                    m_barang_id : element.m_barang_id,
                    barang_nama : element.barang_nama,
                    m_satuan_id : element.m_satuan_id,
                    satuan_nama : element.satuan_nama,
                    t_barangtrans_akhir : element.t_barangtrans_akhir,
                    stockopnamedet_qtyold : element.stockopnamedet_qty,
                    stockopnamedet_qty : element.stockopnamedet_qty,
                    satkonv_nilai : 1
                };
                datastockopnamedet.push(datdet);
            }
            console.log(datastockopnamedet)
            var stockopnameGridSource = {
                datatype: "array",
                localdata:  datastockopnamedet,
                datafields: [
                    { name: 'stockopnamedet_id', type: 'int'},
                    { name: 't_stockopname_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'barang_nama', value: 'barang_nama', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_barang_id', type: 'int'},
                    { name: 'satuan_nama', type : 'string' },
                    { name: 'm_satuan_id', type: 'int'},
                    { name: 'satkonv_nilai'},
                    { name: 't_barangtrans_akhir', type: 'float'},
                    { name: 'stockopnamedet_qtyold', type: 'float'},
                    { name: 'stockopnamedet_qty', type: 'float'}
                ],
            };

            var stockopnameAdapter = new $.jqx.dataAdapter(stockopnameGridSource);
            $("#stockopnameGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: stockopnameAdapter,
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
                        var commit = $("#stockopnameGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#stockopnameGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#stockopnameGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#stockopnameGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({stockopnamedet_id : rechapus.stockopnamedet_id});

                            var id = $("#stockopnameGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#stockopnameGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    {
                        text: 'Barang', datafield: 'barang_id', displayfield: 'barang_nama', columntype: 'combobox', editable : true,
                        createeditor: function (row, value, editor) {
                            editor.jqxComboBox({
                                source: barangAdapter,
                                displayMember: 'label',
                                valueMember: 'value'
                            });
                            editor.on('select', function (event) {
                                var recorddata = $('#stockopnameGrid').jqxGrid('getrenderedrowdata', row);
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
                                    let brg0 = brg[0];
                                    recorddata.m_barang_id = parseInt(val);
                                    recorddata.m_satuan_id = parseInt(brg0.satuan_id);
                                    $("#stockopnameGrid").jqxGrid('setcellvalue', row, "satuan_nama", brg0.satuan_nama);
                                    let stockopname_tgl = $('#stockopname_tgl').val();
                                    let data = {
                                        barang_id : val,
                                        tanggal : moment(stockopname_tgl, "DD-MM-YYYY").format("YYYY-MM-DD"),
                                    }
                                    $.post("<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=getqty", data, function(result){
                                        $("#stockopnameGrid").jqxGrid('setcellvalue', row, "t_barangtrans_akhir", parseFloat(result));
                                    });
                                }
                            });
                        },
                        width : 300,
                    },
                    { text: 'Satuan', datafield: 'satuan_nama', displayfield: 'satuan_nama', editable : false},
                    { text: 'Qty', datafield: 't_barangtrans_akhir', cellsalign: 'right', editable : false },
                    { text: 'Qty Edit', datafield: 'stockopnamedet_qty', cellsalign: 'right', editable : true },
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_stockopname'">Kembali</button>
                </div>
                <form id="formstockopname">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stockopname_no">No. stockopname</label>
                                    <input type="hidden" id="stockopname_id" name="stockopname_id">
                                    <input type="text" class="form-control" id="stockopname_no" name="stockopname_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stockopname_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="stockopname_tgl" name="stockopname_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask require>
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
                            <div id="stockopnameGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <!-- <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button> -->
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->stockopname_id) == 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
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
        $('#stockopname_tgl').val(moment(now).format('DD-MM-YYYY'));

        $('#formstockopname').submit(function (event) {
            event.preventDefault();
            var griddata = $('#stockopnameGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "stockopname Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#stockopnameGrid').jqxGrid('getrenderedrowdata', i);
                if (rec.stockopnamedet_qty > 0) {
                    rows.push({
                        stockopnamedet_id : rec.stockopnamedet_id,
                        t_stockopname_id : rec.t_stockopname_id,
                        barang_nama : rec.barang_nama,
                        m_barang_id : rec.m_barang_id,
                        satuan_nama : rec.satuan_nama,
                        m_satuan_id : rec.m_satuan_id,
                        satkonv_nilai : rec.satkonv_nilai,
                        t_barangtrans_akhir : rec.t_barangtrans_akhir,
                        stockopnamedet_qtyold : rec.stockopnamedet_qtyold,
                        stockopnamedet_qty : rec.stockopnamedet_qty,
                    });    
                } else if (rec.stockopnamedet_qty == 0 && rec.m_satuan_id > 0) {
                    swal("Info!", "stockopname Gagal disimpan, terdapat isian dengan qty kosong ", "error");
                    return false;
                } else if (rec.m_satuan_id < 1) {
                    swal("Info!", "stockopname Gagal disimpan, terdapat isian dengan satuan kosong ", "error");
                    return false;
                }
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    stockopname_id : $('#stockopname_id').val(),
                    stockopname_no : $('#stockopname_no').val(),
                    stockopname_tgl : moment($('#stockopname_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    res - JSON.parse(res)
                    if (res['code'] == 200) {
                        resetForm();
                        swal("Info!", "stockopname Berhasil disimpan", "success");
                        
                    } else {
                        swal("Info!", "stockopname Gagal disimpan", "error");
                    }
                }
            });
        })

        datastockopname = JSON.parse('<?php echo $dataparse ?>');
        if(datastockopname!==null) {
            var dat = datastockopname.datastockopname;
            $('#stockopname_id').val(dat.stockopname_id);
            $('#stockopname_no').val(dat.stockopname_no);
            $('#stockopname_tgl').val(moment(dat.stockopname_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $('#batal').removeAttr('disabled')
        }

        $('#batal').on('click', function() {
            var griddata = $('#stockopnameGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "stockopname Gagal disimpan, detail barang masih kosong", "warning");
                return false;
            }
            swal({
                title: "Batalkan stockopname " + $('#stockopname_no').val(),
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
                    var rec = $('#stockopnameGrid').jqxGrid('getrenderedrowdata', i);
                    m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                    m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                    dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                    
                    satkonv_nilai = 1;
                    if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                    
                    rows.push({
                        stockopnamedet_id : rec.stockopnamedet_id,
                        t_stockopname_id : rec.t_stockopname_id,
                        barang_nama : rec.barang_nama,
                        m_barang_id : rec.m_barang_id,
                        satuan_nama : rec.satuan_nama,
                        m_satuan_id : rec.m_satuan_id,
                        satkonv_nilai : rec.satkonv_nilai,
                        t_barangtrans_akhir : rec.t_barangtrans_akhir,
                        stockopnamedet_qtyold : rec.stockopnamedet_qtyold,
                        stockopnamedet_qty : rec.stockopnamedet_qty,
                    }); 
                }

                $.ajax({
                    url: "<?php echo BASE_URL ?>/controllers/C_stockopname.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        stockopname_id : $('#stockopname_id').val(),
                        stockopname_no : $('#stockopname_no').val(),
                        stockopname_tgl : moment($('#stockopname_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "stockopname Berhasil dibatalkan", "success");
                            
                        } else {
                            swal("Info!", "stockopname Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function resetForm() {
        var now = new Date();
        $('#stockopname_id').val(0);
        $('#stockopname_no').val('');
        $('#stockopname_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#stockopnameGrid").jqxGrid('clear');
    }
</script>