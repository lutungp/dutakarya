<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script>
    $(document).ready(function(){
        var barangAdapter = false;
        var satuanAdapter = false; 
        $.get("<?php echo BASE_URL ?>/controllers/C_penerimaan_brg.php?action=getbarang", function(data, status){
            data = JSON.parse(data);
            databarang = data['barang'];
            var barang = [];
            for (let index = 0; index < databarang.length; index++) {
                const element = databarang[index];
                barang.push({ value: element.barang_id, label: element.barang_nama, satuan_id : element.m_satuan_id });
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
            var satuan = [];
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
                        m_barang_id : 0,
                        m_satuan_id : 0,
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
                console.log(event)
                if (event.args) {
                    var item = event.args.item;
                    if (item) {
                        
                        // var valueelement = $("<div></div>");
                        // valueelement.text("Value: " + item.value);
                        // var labelelement = $("<div></div>");
                        // labelelement.text("Label: " + item.label);
                        // $("#selectionlog").children().remove();
                        // $("#selectionlog").append(labelelement);
                        // $("#selectionlog").append(valueelement);
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
                <form role="form">
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
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="penerimaanGrid" style="margin: 10px;"></div>
                        </div>
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
    });
</script>