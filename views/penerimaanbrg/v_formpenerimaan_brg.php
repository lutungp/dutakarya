<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script>
    $(document).ready(function(){
        var barangAdapter = false;
        var satuanAdapter = false;
        var barang = [];
        var satuan = [];
        $.get("<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=getbarang", function(data, status){
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

            var datasatuan = data['satuan'];
            for (let index = 0; index < datasatuan.length; index++) {
                const element = datasatuan[index];
                satuan.push({ value: element.satuan_id, label: element.satuan_nama });
            }
            
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
                        penerimaan_qty : 0
                    }
                ],
                datafields: [
                    { name: 'penerimaandet_id', type: 'int'},
                    { name: 't_penerimaan_id', type: 'int'},
                    { name: 'm_barang_id', value: 'm_barang_id', values: { source: barangAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'value', name: 'label' } },
                    { name: 'penerimaan_qty', type: 'float'}
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
                            editor.jqxDropDownList({ source: barangAdapter, displayMember: 'label', valueMember: 'value' });
                        }, width : 400,
                        select: function (row) {
                            console.log(row)
                        }
                    },
                    {
                        text: 'Satuan', datafield: 'm_satkonv_id', displayfield: 'm_satkonv_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({ source: satuanAdapter, displayMember: 'label', valueMember: 'value' });
                        }, width : 400
                    },
                    { text: 'Qty', datafield: 'penerimaan_qty', cellsalign: 'right'},
                ]
            });

            $("#penerimaanGrid").on('select', function (event) {
                if (event.args) {
                    var item = event.args.item;
                    if (event.args.owner.id == 'dropdownlisteditorpenerimaanGridm_barang_id') {
                        barang_id = item.value;
                        var datasatkonv = data['satuan_konversi'];
                        datasatkonv = datasatkonv.filter(p=>parseInt(p.m_barang_id)==barang_id);
                        var satkonv = [];
                        for (let index = 0; index < datasatkonv.length; index++) {
                            const element = datasatkonv[index];
                            satkonv.push({ value: element.satkonv_id, label: element.satuan_nama, satkonv_nilai : element.satkonv_nilai });
                        }
                        var selectbarang = barang.filter(p=>parseInt(p.value)==barang_id);
                        satkonv.push({ value: 0, label: selectbarang[0].satuan_nama, satkonv_nilai : 1 });
                        var satuanSource = {
                                datatype: "array",
                                datafields: [
                                    { name: 'label', type: 'string' },
                                    { name: 'value', type: 'int' },
                                    { name: 'satkonv_nilai', type: 'float' }
                                ],
                                localdata: satkonv
                        };
                        satuanAdapter = new $.jqx.dataAdapter(satuanSource, {
                            autoBind: true
                        });

                        $("#penerimaanGrid").jqxGrid('updatebounddata', 'cells');
                    }
                }
            });

            $("#penerimaanGrid").on('cellselect', function (event) {
                var column = $("#penerimaanGrid").jqxGrid('getcolumn', event.args.datafield);
                var value = $("#penerimaanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.datafield);
                var displayValue = $("#penerimaanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.displayfield);
                $("#eventLog").html("<div>Selected Cell<br/>Row: " + event.args.rowindex + ", Column: " + column.text + ", Value: " + value + ", Label: " + displayValue + "</div>");
            });
            $("#penerimaanGrid").on('cellendedit', function (event) {
                var column = $("#penerimaanGrid").jqxGrid('getcolumn', event.args.datafield);
                if (column.displayfield != column.datafield) {
                    $("#eventLog").html("<div>Cell Edited:<br/>Index: " + event.args.rowindex + ", Column: " + column.text + "<br/>Value: " + event.args.value.value + ", Label: " + event.args.value.label
                        + "<br/>Old Value: " + event.args.oldvalue.value + ", Old Label: " + event.args.oldvalue.label + "</div>"
                        );
                }
                else {
                    $("#eventLog").html("<div>Cell Edited:<br/>Row: " + event.args.rowindex + ", Column: " + column.text + "<br/>Value: " + event.args.value
                        + "<br/>Old Value: " + event.args.oldvalue + "</div>"
                        );
                }
            });
        });
    });
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <h3 class="card-title">Penerimaan barang</h3>
                </div>
                <form id="formpenerimaan">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penerimaan_no">No. Penerimaan</label>
                                    <input type="text" class="form-control" id="penerimaan_no" readonly>
                                </div>
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
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
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
            console.log($(this).serialize())
        })
    });
</script>