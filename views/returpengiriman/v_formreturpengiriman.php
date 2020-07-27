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
    dataretur = JSON.parse('<?php echo $dataparse ?>');
    var datareturdetail = [];
    if(dataretur!==null) {
        datareturdetail = dataretur.datareturdetail;
    }
    $(document).ready(function(){
        gridRender();
    });

    function gridRender(params) {
        $.get("<?php echo BASE_URL ?>/controllers/C_returpengiriman.php?action=getsatkonv", function(data, status){
            data = JSON.parse(data);
            satKonv = data['satuan_konversi'];
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
            var datareturdet = [];
            for (let index = 0; index < datareturdetail.length; index++) {
                const element = datareturdetail[index];
                let datdet = {
                    returdet_id : element.returdet_id,
                    t_retur_id : element.t_retur_id,
                    t_pengirimandet_id : element.t_pengirimandet_id,
                    m_barang_id : element.m_barang_id,
                    m_barang_nama : element.m_barang_nama,
                    m_bahanbakubrg_id : element.m_bahanbakubrg_id,
                    m_barangsatuan_id : element.m_barangsatuan_id,
                    m_bahanbakubrg_id : element.m_bahanbakubrg_id,
                    m_satuanpengiriman_id : element.m_satuanpengiriman_id,
                    m_satuanpengiriman_nama : element.m_satuanpengiriman_nama,
                    kirimsatkonv_nilai : element.kirimsatkonv_nilai,
                    pengirimandet_qty : element.pengirimandet_qty,
                    m_satuan_id : element.m_satuan_id,
                    satkonv_nilai : element.satkonv_nilai,
                    returdet_qtyold : element.returdet_qtyold,
                    returdet_qty : element.returdet_qty,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id))
                };
                datareturdet.push(datdet);
            }
            
            var returGridSource = {
                datatype: "array",
                localdata:  datareturdet,
                datafields: [
                    { name: 'returdet_id', type: 'int'},
                    { name: 't_retur_id', type: 'int'},
                    { name: 't_pengirimandet_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id' },
                    { name: 'm_barang_nama', value: 'm_barang_nama' },
                    { name: 'm_barangsatuan_id', value: 'm_barangsatuan_id', type : 'int' },
                    { name: 'm_bahanbakubrg_id', value: 'm_bahanbakubrg_id', type : 'int' },
                    { name: 'm_satuanpengiriman_id', value: 'm_satuanpengiriman_id' },
                    { name: 'm_satuanpengiriman_nama', value: 'm_satuanpengiriman_nama' },
                    { name: 'kirimsatkonv_nilai', value : 'kirimsatkonv_nilai'},
                    { name: 'pengirimandet_qty', value: 'pengirimandet_qty' },
                    { name: 'satkonv_nilai', value : 'satkonv_nilai'},
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'returdet_qtyold', type: 'float'},
                    { name: 'returdet_qty', type: 'float'},
                ]
            };
            
            var returAdapter = new $.jqx.dataAdapter(returGridSource);
            $("#returGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: returAdapter,
                editable: true,
                showtoolbar: true,
                selectionmode: 'singlecell',
                rendertoolbar: function (toolbar) {
                    var me = this;
                    var container = $("<div style='margin: 5px;'></div>");
                    toolbar.append(container)
                    container.append('<input style="margin-left: 5px;" id="deleterowbutton" type="button" value="Hapus" />');
                    $("#deleterowbutton").jqxButton();
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#returGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#returGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#returGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({returdet_id : rechapus.returdet_id});

                            var id = $("#returGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#returGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    { text: 'Barang', datafield: 'm_barang_nama', displayfield: 'm_barang_nama', editable : false, width : 300 },
                    { text: 'Satuan Retur', datafield: 'm_satuanpengiriman_nama', cellsalign: 'left', editable : false, width : 200},
                    { text: 'Qty Kirim', datafield: 'pengirimandet_qty', cellsalign: 'right', editable : false },
                    {
                        text: 'Satuan Retur', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'combobox', width : 200,
                        createeditor: function (row, value, editor) {
                            var recorddata = $('#returGrid').jqxGrid('getrenderedrowdata', row);
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
                                }
                            });
                        },
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                            var recorddata = $('#returGrid').jqxGrid('getrenderedrowdata', row);
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
                                }
                            });
                        },
                    },
                    { text: 'Qty Retur', datafield: 'returdet_qty', cellsalign: 'right', columntype: 'numberinput',
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            let gridrecords = $('#returGrid').jqxGrid('getrows');
                            let currentrecord = gridrecords[row];
                            editor.on('keyup', function (event) {
                                var recorddata = $('#returGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                /* mengisi qty bahan baku */
                                let bbakuidx = gridrecords.findIndex(x => x.m_bahanbakubrg_id === parseInt(currentrecord.m_barang_id));
                                if (bbakuidx) {
                                    let bbkurec = $('#returGrid').jqxGrid('getrenderedrowdata', bbakuidx);
                                    var qtybbaku = parseInt(bbkurec.satkonv_nilai) * val;
                                    $("#returGrid").jqxGrid('setcellvalue', bbakuidx, "returdet_qty", qtybbaku);
                                }
                            });
                        }
                    },
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_returpengiriman'">Kembali</button>
                </div>
                <form id="formretur">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="retur_no">No. Retur</label>
                                    <input type="hidden" id="retur_id" name="retur_id">
                                    <input type="text" class="form-control" id="retur_no" name="retur_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="t_pengiriman_id">No. Pengiriman</label>
                                    <select id="t_pengiriman_id" name="t_pengiriman_id" style="width: 100%;" require></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="retur_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="retur_tgl" name="retur_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask readonly require>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <input type="hidden" id="m_rekanan_id" name="m_rekanan_id">
                                    <input type="text" class="form-control" id="m_rekanan_nama" name="m_rekanan_nama" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>
                        <div class="row">
                            <div id="returGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php
                        $penagihan_no = isset($data->dataretur->t_penagihan_no) ? $data->dataretur->t_penagihan_no : '';
                         if ($delete <> '' && $penagihan_no == '') { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if ((($create <> '' && isset($data->retur_id) == 0) || ($update <> '' && $data->retur_id > 0) && $penagihan_no == '')) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;" onclick="cetak()">Cetak</button>
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
        $('#retur_tgl').val(moment(now).format('DD-MM-YYYY'));
        $('#formretur').submit(function (event) {
            event.preventDefault();
            var griddata = $('#returGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#returGrid').jqxGrid('getrenderedrowdata', i);
                
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                rows.push({
                    'returdet_id' : rec.returdet_id,
                    't_retur_id' : $('#retur_id').val(),
                    't_pengirimandet_id' : rec.t_pengirimandet_id,
                    'm_barang_id' : rec.m_barang_id,
                    'm_bahanbakubrg_id' : rec.m_bahanbakubrg_id,
                    'm_barangsatuan_id' : parseInt(rec.m_barangsatuan_id),
                    'm_satuan_id' : parseInt(m_satuan_id[0].value),
                    'satkonv_nilai' : rec.satkonv_nilai,
                    'returdet_qtyold' : rec.returdet_qtyold,
                    'returdet_qty' : rec.returdet_qty,
                }); 
            }

            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_returpengiriman.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    retur_id : $('#retur_id').val(),
                    t_pengiriman_id : $('#t_pengiriman_id').val(),
                    retur_no : $('#retur_no').val(),
                    retur_tgl : moment($('#retur_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    // window.open('<?php // echo BASE_URL;?>/controllers/C_returpengiriman.php?action=exportpdf&id=' + $('#retur_id').val());
                    if (res == 200) {
                        resetForm();
                        swal("Info!", "Retur Berhasil disimpan", "success");
                        $("#ModalSatuan").modal('toggle');
                    } else {
                        swal("Info!", "Retur Gagal disimpan", "error");
                    }
                }
            });
        });

        $('#batal').on('click', function () {
            var griddata = $('#returGrid').jqxGrid('getdatainformation');
            var rows = [];
            var griddata = $('#returGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#returGrid').jqxGrid('getrenderedrowdata', i);
                
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                rows.push({
                    'returdet_id' : rec.returdet_id,
                    't_retur_id' : $('#retur_id').val(),
                    't_pengirimandet_id' : rec.t_pengirimandet_id,
                    'm_barang_id' : rec.m_barang_id,
                    'm_barangsatuan_id' : parseInt(rec.m_barangsatuan_id),
                    'm_bahanbakubrg_id' : rec.m_bahanbakubrg_id,
                    'm_satuan_id' : parseInt(m_satuan_id[0].value),
                    'satkonv_nilai' : rec.satkonv_nilai,
                    'returdet_qtyold' : rec.returdet_qtyold,
                    'returdet_qty' : rec.returdet_qty,
                }); 
            }

            swal({
                title: "Batalkan retur " + $('#retur_no').val(),
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
                    url: "<?php echo BASE_URL ?>/controllers/C_returpengiriman.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        retur_id : $('#retur_id').val(),
                        t_pengiriman_id : $('#t_pengiriman_id').val(),
                        retur_no : $('#retur_no').val(),
                        retur_tgl : moment($('#retur_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "Retur Berhasil dibatalkan", "success");
                            $("#ModalSatuan").modal('toggle');
                        } else {
                            swal("Info!", "Retur Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });

        $('#t_pengiriman_id').select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_returpengiriman.php?action=getpengiriman',
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

        $('#t_pengiriman_id').on('select2:select', function (e) {
            var value = $(e.currentTarget).find("option:selected").val();
            $.post("<?php echo BASE_URL ?>/controllers/C_returpengiriman.php?action=getpengirimandet", {pengiriman_id : value}, function(result){
                let res = JSON.parse(result);
                $('#m_rekanan_id').val(res['pengiriman'].m_rekanan_id);
                $('#m_rekanan_nama').val(res['pengiriman'].rekanan_nama);
                datareturdetail = [];
                for (let index = 0; index < res['pengirimandet'].length; index++) {
                    const element = res['pengirimandet'][index];
                    var data = {
                        returdet_id : 0,
                        t_retur_id : 0,
                        t_pengiriman_id : element.t_pengiriman_id,
                        t_pengirimandet_id : element.pengirimandet_id,
                        m_barang_id : element.m_barang_id,
                        m_barang_nama : element.barang_nama,
                        m_bahanbakubrg_id : element.m_bahanbakubrg_id,
                        m_barangsatuan_id : element.m_barangsatuan_id,
                        m_satuanpengiriman_id : element.m_satuan_id,
                        m_satuanpengiriman_nama : element.satuan_nama,
                        pengirimandet_qty : element.pengirimandet_qty,
                        kirimsatkonv_nilai : element.satkonv_nilai,
                        m_satuan_id : element.m_satuan_id,
                        returdet_qtyold : 0,
                        returdet_qty : 0,
                        satkonv_nilai : element.satkonv_nilai,
                    }
                    datareturdetail.push(data);
                }
                if(dataretur==null) {
                    gridRender();
                }
            });
        });

        dataretur = JSON.parse('<?php echo $dataparse ?>');
        if(dataretur!==null) {
            var dat = dataretur.dataretur;
            $('#retur_id').val(dat.retur_id);
            $("#t_pengiriman_id").data('select2').trigger('select', {
                data: {"id":dat.t_pengiriman_id, "text": dat.pengiriman_no }
            });
            $('#retur_no').val(dat.retur_no);
            $('#retur_tgl').val(moment(dat.retur_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $('#m_rekanan_id').val(dat.m_rekanan_id);
            $('#m_rekanan_nama').val(dat.rekanan_nama);
            $('#batal').removeAttr('disabled')
            $("#t_pengiriman_id").prop("disabled", true);
            if (dat.t_penagihan_no !== '') {
                var penagihanstr = dat.t_penagihan_no == null || dat.t_penagihan_no == '' ? '' : ", sudah dibuat penagihan dengan No. Penagihan " + dat.t_penagihan_no;
                swal("Info!", "No. pengiriman " + dat.pengiriman_no + penagihanstr, "warning");
            }
        }

    });

    function cetak() {
        var retur_id = $('#retur_id').val();
        window.open('<?php echo BASE_URL;?>/controllers/C_returpengiriman.php?action=exportpdf&id=' + retur_id);
    }

    function resetForm() {
        var now = new Date();
        $('#retur_id').val(0);
        $("#t_pengiriman_id").empty()
        $('#retur_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
        $('#retur_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#returGrid").jqxGrid('clear');
    }
</script>