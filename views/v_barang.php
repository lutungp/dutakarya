<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script type="text/javascript">
    var satbesar = [];
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_barang.php?action=getbarang";
        // prepare the data
        var source = {
            datatype: "json",
            datafields: [
                { name: 'barang_id', type: 'int' },
                { name: 'barang_kode', type: 'string' },
                { name: 'barang_nama', type: 'string' },
                { name: 'barang_aktif', type: 'string' },
                { name: 'm_satuan_id', type: 'int' },
                { name: 'satuan_nama', type: 'string' },
            ],
            id: 'barang_id',
            url: url,
            updaterow: function (rowid, rowdata, commit) {
                // synchronize with the server - send update command
                // call commit with parameter true if the synchronization with the server is successful 
                // and with parameter false if the synchronization failder.
                commit(true);
            }
        };
        var addfilter = function () {
            var filtergroup = new $.jqx.filter();
            var filter_or_operator = 1;
            var filtervalue = '';
            var filtercondition = 'contains';
            var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            filtervalue = '';
            filtercondition = 'starts_with';
            var filter2 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);

            filtergroup.addfilter(filter_or_operator, filter1);
            filtergroup.addfilter(filter_or_operator, filter2);
            // add the filters.
            $("#grid").jqxGrid('addfilter', 'barang_nama', filtergroup);
            // // apply the filters.
            // $("#grid").jqxGrid('applyfilters');
        }
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        var satbesarAdapter = false;
        $.get("<?php echo BASE_URL ?>/controllers/C_barang.php?action=getsatuan", function(data, status){
            data = JSON.parse(data);
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                let datasat = { value: element.id, label: element.text };
                satbesar.push(datasat);
            }
            
            var satuanBesarSource = {
                    datatype: "array",
                    datafields: [
                        { name: 'label', type: 'string' },
                        { name: 'value', type: 'int' }
                    ],
                    localdata: satbesar
            };
            satbesarAdapter = new $.jqx.dataAdapter(satuanBesarSource, {
                autoBind: true
            });
            // prepare the data
            var satkonvGridSource = {
                datatype: "array",
                localdata:  [
                        {
                    'satkonv_id' : 0,
                    'm_barang_id' : 0,
                    'm_satuan_id' : '',
                    'satkonv_nilai' : '',
                },{
                    'satkonv_id' : 0,
                    'm_barang_id' : 0,
                    'm_satuan_id' : '',
                    'satkonv_nilai' : '',
                },{
                    'satkonv_id' : 0,
                    'm_barang_id' : 0,
                    'm_satuan_id' : '',
                    'satkonv_nilai' : '',
                }],
                datafields: [
                    { name: 'satkonv_id', type: 'int'},
                    { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satbesarAdapter.records, value: 'value', name: 'label' } },
                    { name: 'm_barang_id', type: 'int'},
                    { name: 'satkonv_nilai', type: 'float'}
                ]
            };

            var satkonvgridAdapter = new $.jqx.dataAdapter(satkonvGridSource);
            $("#satuanGrid").jqxGrid({
                height : 200,
                width: "95%",
                source: satkonvgridAdapter,
                selectionmode: 'singlecell',
                editable: true,
                columns: [
                    {
                        text: 'Satuan Konv', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({ source: satbesarAdapter, displayMember: 'label', valueMember: 'value' });
                        }, width : 200
                    },
                    { text: 'Nilai', datafield: 'satkonv_nilai'},
                ]
            });
            $("#satuanGrid").on('cellselect', function (event) {
                var column = $("#satuanGrid").jqxGrid('getcolumn', event.args.datafield);
                var value = $("#satuanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.datafield);
                var displayValue = $("#satuanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.displayfield);
                $("#eventLog").html("<div>Selected Cell<br/>Row: " + event.args.rowindex + ", Column: " + column.text + ", Value: " + value + ", Label: " + displayValue + "</div>");
            });
            $("#satuanGrid").on('cellendedit', function (event) {
                var column = $("#satuanGrid").jqxGrid('getcolumn', event.args.datafield);
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

        // initialize jqxGrid
        $("#grid").jqxGrid({
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            autoheight: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            selectionmode: 'multiplecellsadvanced',
            filterable: true,
            ready: function () {
                addfilter();
            },
            autoshowfiltericon: true,
            columns: [
                { text: 'Kode Barang', datafield: 'barang_kode'},
                { text: 'Nama Barang', datafield: 'barang_nama'},
                { text: 'Satuan Kecil', datafield: 'satuan_nama'},
                { text: 'Barang Aktif', datafield: 'barang_aktif', filterable: false, width:'100'},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        resetForm();
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#ModalSatuan").modal('toggle');
                        $("#barang_id").val(dataRecord.barang_id);
                        $("#barang_kode").val(dataRecord.barang_kode);
                        $("#barang_nama").val(dataRecord.barang_nama);
                        $("#m_satuan_id").data('select2').trigger('select', {
                            data: {"id": dataRecord.m_satuan_id, "text": dataRecord.satuan_nama }
                        });
                        
                        $.ajax({
                            url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=getsatkonv",
                            type: "post",
                            data: {barang_id : dataRecord.barang_id},
                            success : function (res) {
                                resSatkonv = JSON.parse(res);
                                var dataSatKonv = [];
                                for (let index = 0; index < resSatkonv.length; index++) {
                                    const element = resSatkonv[index];
                                    var data = {
                                        'satkonv_id' : element.satkonv_id,
                                        'm_barang_id' : element.m_barang_id,
                                        'm_satuan_id' : element.m_satuan_id,
                                        'satkonv_nilai' : element.satkonv_nilai,
                                    }
                                    dataSatKonv.push(data);
                                }
                                countdata = dataSatKonv.length;
                                for (let index = 0; index < (4 - countdata); index++) {
                                    var data = {
                                        'satkonv_id' : '',
                                        'm_barang_id' : dataRecord.barang_id,
                                        'm_satuan_id' : '',
                                        'satkonv_nilai' : '',
                                    }
                                    dataSatKonv.push(data);
                                }
                                var satkonvGridSource = {
                                    datatype: "array",
                                    localdata:  dataSatKonv,
                                    datafields: [
                                        { name: 'satkonv_id', type: 'int'},
                                        { name: 'm_satuan_id', value: 'm_satuan_id', values: { source: satbesarAdapter.records, value: 'value', name: 'label' } },
                                        { name: 'm_barang_id', type: 'int'},
                                        { name: 'satkonv_nilai', type: 'float'}
                                    ]
                                };
                                var satkonvgridAdapter = new $.jqx.dataAdapter(satkonvGridSource);
                                $("#satuanGrid").jqxGrid({
                                    height : 200,
                                    width: "95%",
                                    source: satkonvgridAdapter,
                                    selectionmode: 'singlecell',
                                    editable: true,
                                    columns: [
                                        {
                                            text: 'Satuan Konv', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                                            createeditor: function (row, value, editor) {
                                                editor.jqxDropDownList({ source: satbesarAdapter, displayMember: 'label', valueMember: 'value' });
                                            }, width : 200
                                        },
                                        { text: 'Nilai', datafield: 'satkonv_nilai'},
                                    ]
                                });
                                $("#satuanGrid").on('cellselect', function (event) {
                                    var column = $("#satuanGrid").jqxGrid('getcolumn', event.args.datafield);
                                    var value = $("#satuanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.datafield);
                                    var displayValue = $("#satuanGrid").jqxGrid('getcellvalue', event.args.rowindex, column.displayfield);
                                    $("#eventLog").html("<div>Selected Cell<br/>Row: " + event.args.rowindex + ", Column: " + column.text + ", Value: " + value + ", Label: " + displayValue + "</div>");
                                });
                                $("#satuanGrid").on('cellendedit', function (event) {
                                    var column = $("#satuanGrid").jqxGrid('getcolumn', event.args.datafield);
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
                            }
                        })
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        swal({
                            title: "Hapus barang " + dataRecord.barang_nama,
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
                                url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=deletesatuan",
                                type: "post",
                                data: { barang_id : dataRecord.barang_id,  alasan : inputValue},
                                success : function (res) {
                                    $("#grid").jqxGrid('updatebounddata');
                                    if (res == 200) {
                                        swal("Info!", "Barang " + $("#barang_nama").val() + " Berhasil dihapus", "success");
                                    } else {
                                        swal("Info!", "Barang " + $("#barang_nama").val() + " Gagal dihapus", "error");
                                    }
                                }
                            });
                        });
                    }
                }
            ]
        });

        $("#m_satuan_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_barang.php?action=getsatuan',
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

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="adduser()" 
                    <?php 
                        if ($create == '') {
                            echo "disabled";
                        }
                    ?>>Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
                <div class="modal fade" id="ModalSatuan" tabindex="-1" role="dialog" aria-labelledby="ModalUserLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalUserLabel">Data Barang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="barang_id" name="barang_id">
                                            <label for="barang_kode">Barang Kode</label>
                                            <input type="text" id="barang_kode" name="barang_kode" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="barang_nama">Barang Nama</label>
                                            <input type="text" id="barang_nama" name="barang_nama" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="m_satuan_id">Satuan</label>
                                            <select id="m_satuan_id" name="m_satuan_id" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div id="satuanGrid" style="margin: 10px;"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm()">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function submitForm() {
        var dataForm = {
            barang_id : $("#barang_id").val(),
            barang_kode : $("#barang_kode").val(),
            barang_nama : $("#barang_nama").val(),
            m_satuan_id : $("#m_satuan_id").val()
        };
        var griddata = $('#satuanGrid').jqxGrid('getdatainformation');
        var rows = [];
        var barang_id = $("#barang_id").val();
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#satuanGrid').jqxGrid('getrenderedrowdata', i);
            m_satuan_id = satbesar.filter(p=>p.label==rec.m_satuan_id);
            if (m_satuan_id.length > 0) {
                rows.push({
                    'satkonv_id' : rec.satkonv_id,
                    'm_barang_id' : barang_id,
                    'm_satuan_id' : m_satuan_id[0].value,
                    'satkonv_nilai' : rec.satkonv_nilai,
                });   
            }
        }

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=createbarang",
            type: "post",
            data: {
                dataForm : dataForm,
                dataSatKonv : rows
            },
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "Barang " + $("#barang_nama").val() + " Berhasil disimpan", "success");
                    $("#ModalSatuan").modal('toggle');
                } else {
                    swal("Info!", "Barang " + $("#barang_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#barang_id").val(0);
        $("#barang_kode").val('');
        $("#barang_nama").val('');
        $("#satuanGrid").jqxGrid('updatebounddata');
    }

    function adduser() {
        resetForm();
        $("#ModalSatuan").modal('toggle');
    }
</script>