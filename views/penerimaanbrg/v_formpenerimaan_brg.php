<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script>
    var barangAdapter = false;
    var satuanAdapter = false;
    var barang = [];
    var satuan = [];
    $(document).ready(function(){
        $.get("<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=getbarang", function(data, status){
            data = JSON.parse(data);
            databarang = data['barang'];
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
            var penerimaanGridSource = {
                datatype: "array",
                localdata:  [
                    {
                        penerimaandet_id : 0,
                        t_penerimaan_id : 0,
                        m_barang_id : '',
                        m_satuan_id : '',
                        penerimaandet_qty : 0,
                        satuankonv : []
                    }
                ],
                datafields: [
                    { name: 'penerimaandet_id', type: 'int'},
                    { name: 't_penerimaan_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'penerimaandet_qty', type: 'float'}
                ]
            };

            var penerimaanAdapter = new $.jqx.dataAdapter(penerimaanGridSource);
            $("#penerimaanGrid").jqxGrid({
                height : 200,
                width: "100%",
                height: 360,
                source: penerimaanAdapter,
                selectionmode: 'singlecell',
                editable: true,
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
                                    recorddata.satuankonv = satkonv;
                                    $("#penerimaanGrid").jqxGrid('setcellvalue', row, "m_satuan_id", "");
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', row);
                            var satuanSource = {
                                    datatype: "array",
                                    datafields: [
                                        { name: 'label', type: 'string' },
                                        { name: 'value', type: 'int' }
                                    ],
                                    localdata: recorddata.satuankonv
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
                        width : 300
                    },
                    { text: 'Qty', datafield: 'penerimaandet_qty', cellsalign: 'right'},
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
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask require>
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
                            <div id="penerimaanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger btn-sm">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
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
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#penerimaanGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                rows.push({
                    'penerimaandet_id' : rec.penerimaandet_id,
                    't_penerimaan_id' : $('#penerimaan_id').val(),
                    'm_barang_id' : parseInt(m_barang_id[0].value||0),
                    'm_satuan_id' : parseInt(m_satuan_id[0].value||0),
                    'penerimaandet_qty' : rec.penerimaandet_qty,
                }); 
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
                    rows : rows
                },
                success : function (res) {
                    if (res == 200) {
                        resetForm();
                        swal("Info!", "Penerimaan Berhasil disimpan", "success");
                        $("#ModalSatuan").modal('toggle');
                    } else {
                        swal("Info!", "Penerimaan Gagal disimpan", "error");
                    }
                }
            });
        })
    });

    function resetForm() {
        $("#penerimaanGrid").jqxGrid('updatebounddata');
        $('#penerimaan_id').val(0);
        $('#penerimaan_no').val('');
        var now = new Date();
        $('#penerimaan_tgl').val(moment(now).format('DD-MM-YYYY'));
        $('#m_rekanan_id').val('');
    }
</script>