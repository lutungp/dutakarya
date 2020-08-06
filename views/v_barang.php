<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
?>
<script type="text/javascript">
    var satbesar = [];
    $(document).ready(function () {
        $('#tabs').jqxTabs({ width: '100%', height: '100%', position: 'top'}); 
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
            $("#grid").jqxGrid('addfilter', 'barang_nama', filtergroup);
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
                autoheight : true,
                // width: "95%",
                width : "100%",
                source: satkonvgridAdapter,
                selectionmode: 'singlecell',
                editable: true,
                columns: [
                    {
                        text: 'Satuan Konv', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({ source: satbesarAdapter, displayMember: 'label', valueMember: 'value' });
                        }, width: 150
                    },
                    { text: 'Nilai', datafield: 'satkonv_nilai', width: 100, cellsalign : 'right'},
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
                                    autoheight : true,
                                    width: "95%",
                                    source: satkonvgridAdapter,
                                    selectionmode: 'singlecell',
                                    editable: true,
                                    columns: [
                                        {
                                            text: 'Satuan Konv', datafield: 'm_satuan_id', displayfield: 'm_satuan_id', columntype: 'dropdownlist',
                                            createeditor: function (row, value, editor) {
                                                editor.jqxDropDownList({ source: satbesarAdapter, displayMember: 'label', valueMember: 'value' });
                                            }, width : 150
                                        },
                                        { text: 'Nilai', datafield: 'satkonv_nilai', width : 100, cellsalign : 'right'},
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
                        });

                        $.ajax({
                            url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=getharga",
                            type: "post",
                            data: {barang_id : dataRecord.barang_id},
                            success : function (res) {
                                var resultHNA = JSON.parse(res);
                                var dataHNA = [];
                                for (let index = 0; index < resultHNA.length; index++) {
                                    const element = resultHNA[index];
                                    var data = {
                                        'baranghna_no' : element.baranghna_no,
                                        'baranghnadet_tglawal' : element.baranghnadet_tglawal,
                                        'baranghnadet_harga' : element.baranghnadet_harga,
                                    }
                                    dataHNA.push(data);
                                }

                                var hargaGridSource = {
                                    datatype: "array",
                                    localdata:  dataHNA,
                                    datafields: [
                                        { name: 'baranghna_no', type: 'string'},
                                        { name: 'baranghnadet_tglawal', type: 'date'},
                                        { name: 'baranghnadet_harga', type: 'float'}
                                    ]
                                };
                                var hargagridAdapter = new $.jqx.dataAdapter(hargaGridSource);
                                $("#hargaGrid").jqxGrid({
                                    height : 200,
                                    width : "95%",
                                    source: hargagridAdapter,
                                    selectionmode: 'singlecell',
                                    columns: [
                                        { text: 'Transaksi', datafield: 'baranghna_no', displayfield: 'baranghna_no', cellsalign:'center' },
                                        { text: 'Tanggal Berlaku', datafield: 'baranghnadet_tglawal', cellsformat: 'dd-MM-yyyy', displayfield: 'baranghnadet_tglawal', width : 150, cellsalign:'center' },
                                        { text: 'Nilai Harga', datafield: 'baranghnadet_harga', displayfield: 'baranghnadet_harga', cellsalign:'right', width : 100 },
                                    ]
                                });
                            }
                        });

                        bahanBaku();
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
                                url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=deletebarang",
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

    function bahanBaku() {
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=getbahanbaku",
            type: "post",
            data : {
                barang_id : $('#barang_id').val()
            },
            datatype : 'json',
            success : function (res) {
                var barang = JSON.parse(res);
                var barangArr = [];
                barang['barang'].forEach(element => {
                    barangArr.push({
                        value: element.barang_id, 
                        label: element.barang_nama
                    });
                });
                gridBahanBrg(barangArr, barang['bahanbrg']);
            }
        });
    }

    function gridBahanBrg(barangarr, bahanbrg) {
        var bahanBakuSource = {
            datatype: "array",
            datafields: [
                { name: 'label', type: 'string' },
                { name: 'value', type: 'int' }
            ],
            localdata: barangarr
        };
        bahanAdapter = new $.jqx.dataAdapter(bahanBakuSource, {
            autoBind: true
        });
        databahanbrg = [];
        for (let index = 0; index < bahanbrg.length; index++) {
            const element = bahanbrg[index];
            var data = {
                'bahanbrg_id' : element.bahanbrg_id,
                'm_barang_id' : element.m_barang_id,
                'm_barang_nama' : element.m_barang_nama,
                'bahanbrg_qty' : element.bahanbrg_qty,
                'bahanbrg_ketika' : element.bahanbrg_ketika
            }
            databahanbrg.push(data);
        }
        countdata = databahanbrg.length
        for (let index = 0; index < (4 - countdata); index++) {
            var data = {
                'bahanbrg_id' : 0,
                'm_barang_id' : 0,
                'm_barang_nama' : '',
                'bahanbrg_qty' : '',
                'bahanbrg_ketika' : ''
            }
            databahanbrg.push(data);
        }

        var bahanbrgGridSource = {
            datatype: "array",
            localdata:  databahanbrg,
            datafields: [
                { name: 'bahanbrg_id', type: 'int'},
                { name: 'm_barang_nama', value: 'm_barang_nama', values: { source: bahanAdapter.records, value: 'barang_id', name: 'barang_nama' } },
                { name: 'm_barang_id', type: 'int'},
                { name: 'bahanbrg_qty', type: 'float'},
                { name: 'bahanbrg_ketika', type: 'string'},
            ]
        };
        var bahanbrggridAdapter = new $.jqx.dataAdapter(bahanbrgGridSource);
        $("#bahanGrid").jqxGrid({
            height : 200,
            width : "100%",
            source: bahanbrggridAdapter,
            selectionmode: 'singlecell',
            editable: true,
            columns: [
                {
                    text: 'Barang', datafield: 'm_barang_id', displayfield: 'm_barang_nama', columntype: 'combobox', width:250,
                    createeditor: function (row, value, editor) {
                        editor.jqxComboBox({ source: bahanAdapter, displayMember: 'label', valueMember: 'value' });
                    }
                },
                { text: 'Qty', datafield: 'bahanbrg_qty', width:100, cellsalign : 'right' },
                {
                    text: 'Berkurang', datafield: 'bahanbrg_ketika', displayfield: 'bahanbrg_ketika', columntype: 'combobox',
                    width:100,
                    createeditor: function (row, value, editor) {
                        var ketikasource = {
                            localdata: [
                                { label : 'kirim', value : 'kirim' },
                                { label : 'produksi', value : 'produksi' },
                                { label : 'rusak', value : 'rusak' }
                            ],
                            datatype: "array"
                        };
                        var ketikaAdapter = new $.jqx.dataAdapter(ketikasource);
                        editor.jqxComboBox({ source: ketikaAdapter, displayMember: 'label', valueMember: 'value' });
                    }
                },
            ]
        });
    }
</script>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addbarang()" 
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
                                    <div id='tabs'>
                                        <ul>
                                            <li style="margin-left: 30px;">Satuan Konversi</li>
                                            <li>Harga</li>
                                            <li>Bahan Baku</li>
                                        </ul>
                                        <div style="padding: 5px;">
                                            <div id="satuanGrid"></div>
                                        </div>
                                        <div style="padding: 5px;">
                                            <div id="hargaGrid"></div>
                                        </div>
                                        <div style="padding: 5px;">
                                            <div id="bahanGrid"></div>
                                        </div>
                                    </div>
                                </div> 
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

        var dataBahan = $('#bahanGrid').jqxGrid('getdatainformation');
        var bahanrows = [];
        var barang_id = $("#barang_id").val();
        for (var i = 0; i < dataBahan.rowscount; i++){
            var rec = $('#bahanGrid').jqxGrid('getrenderedrowdata', i);
            if(rec.m_barang_id > 0) {
                bahanrows.push({
                    bahanbrg_id: rec.bahanbrg_id,
                    bahanbrg_ketika: rec.bahanbrg_ketika,
                    bahanbrg_qty: rec.bahanbrg_qty,
                    m_barang_id: rec.m_barang_id
                });
            }
        }
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_barang.php?action=createbarang",
            type: "post",
            data: {
                dataForm : dataForm,
                dataSatKonv : rows,
                dataBahan : bahanrows
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
        $("#bahanGrid").jqxGrid('updatebounddata');
    }

    function addbarang() {
        resetForm();
        $("#ModalSatuan").modal('toggle');
        bahanBaku();
    }
</script>