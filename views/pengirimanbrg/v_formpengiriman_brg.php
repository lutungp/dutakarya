<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<style>
    #pengiriman_total {
        font-size : 18px;
        font-weight : bold;
    }
</style>
<script>
    var barangAdapter = false;
    var satuanAdapter = false;
    var barang = [];
    var satuan = [];
    var satKonv = [];
    var hapusdetail = [];
    $(document).ready(function(){
        datapengiriman = '<?php echo $dataparse ?>';
        var datapengiriman = datapengiriman.replace(/\\n/g, "\\n").replace(/\\'/g, "\\'").replace(/\\"/g, '\\"').replace(/\\&/g, "\\&").replace(/\\r/g, "\\r").replace(/\\t/g, "\\t").replace(/\\b/g, "\\b").replace(/\\f/g, "\\f");
        datapengiriman = JSON.parse(datapengiriman.replace(/[\u0000-\u0019]+/g,""));
        var datapengirimandetail = [];
        if(datapengiriman!==null) {
            datapengirimandetail = datapengiriman.datapengirimandetail;
        }

        var generaterow = function (i) {
            var row = {};
            row["pengirimandet_id"] = 0;
            row["t_pengiriman_id"] = 0;
            row["satuankonv"] = '';
            row["m_barang_id"] = 0;
            row["m_barang_nama"] = '';
            row["m_barangsatuan_id"] = 0,
            row["m_bahanbakubrg_id"] = 0,
            row["hargakontrak"] = 0;
            row['pengirimandet_harga'] = 0;
            row['hargakontrakdet_ppn'] = 'N';
            row['pengirimandet_ppn'] = 0;
            row["m_satuan_id"] = 0;
            row["m_satuan_nama"] = '';
            row["satkonv_nilai"] = 1;
            row["pengirimandet_qtyold"] = 0;
            row["pengirimandet_qty"] = 0;
            row['pengirimandet_subtotal'] = 0;
            row['pengirimandet_potongan'] = 0;
            row['pengirimandet_total'] = 0;
            row['t_returdet_qty'] = 0;
            return row;
        }

        $.get("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getbarang", function(data, status){
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
            var datapengirimandet = [];
            var retur = 0;
            for (let index = 0; index < datapengirimandetail.length; index++) {
                const element = datapengirimandetail[index];
                let datdet = {
                    pengirimandet_id : element.pengirimandet_id,
                    t_pengiriman_id : element.t_pengiriman_id,
                    m_barang_id : element.m_barang_id,
                    m_barang_nama : element.m_barang_nama,
                    m_barangsatuan_id : element.m_barangsatuan_id,
                    m_bahanbakubrg_id : element.m_bahanbakubrg_id,
                    m_satuan_id : element.m_satuan_id,
                    m_satuan_nama : element.m_satuan_nama,
                    hargakontrak : element.hargakontrak,
                    satkonv_nilai : element.satkonv_nilai,
                    pengirimandet_harga : element.pengirimandet_harga,
                    hargakontrakdet_ppn : element.hargakontrakdet_ppn,
                    pengirimandet_ppn : element.pengirimandet_ppn,
                    pengirimandet_qtyold : element.pengirimandet_qty,
                    pengirimandet_qty : element.pengirimandet_qty,
                    pengirimandet_subtotal : element.pengirimandet_subtotal,
                    pengirimandet_potongan : element.pengirimandet_potongan,
                    pengirimandet_total : element.pengirimandet_total,
                    satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==element.m_barang_id)),
                    t_returdet_qty : element.t_returdet_qty
                };

                retur = retur + parseFloat(element.t_returdet_qty);
                datapengirimandet.push(datdet);
            }

            if (retur > 0) {
                swal("Info!", "Transaksi sudah terdapat retur, tidak dapat diedit dan batal", "error");
                $("#simpan").prop("disabled", true);
                $("#batal").prop("disabled", true);
            }
            
            var pengirimanGridSource = {
                datatype: "array",
                localdata:  datapengirimandet,
                datafields: [
                    { name: 'pengirimandet_id', type: 'int'},
                    { name: 't_pengiriman_id', type: 'int'},
                    { name: 'satuankonv'},
                    { name: 'm_barang_nama', value: 'm_barang_nama', values: { source: barangAdapter.records, value: 'barang_id', name: 'barang_nama' } },
                    { name: 'm_barang_id', type: 'int'},
                    { name: 'm_barangsatuan_id', type: 'int'},
                    { name: 'm_bahanbakubrg_id', type: 'int'},
                    { name: 'hargakontrak', type: 'float'},
                    { name: 'pengirimandet_harga', type: 'float'},
                    { name: 'hargakontrakdet_ppn', type: 'string'},
                    { name: 'pengirimandet_ppn', type: 'float'},
                    { name: 'm_satuan_nama', value: 'm_satuan_id', values: { source: satuanAdapter.records, value: 'satuan_id', name: 'satuan_nama' } },
                    { name: 'm_satuan_id', type: 'int'},
                    { name: 'satkonv_nilai'},
                    { name: 'pengirimandet_qtyold', type: 'float'},
                    { name: 'pengirimandet_qty', type: 'float'},
                    { name: 'pengirimandet_subtotal', type: 'float'},
                    { name: 'pengirimandet_potongan', type: 'float'},
                    { name: 'pengirimandet_total', type: 'float'},
                    { name: 't_returdet_qty', type: 'float'}
                ]
            };

            var pengirimanAdapter = new $.jqx.dataAdapter(pengirimanGridSource);
            var tooltiprenderer = function (element) {
                $(element).jqxTooltip({position: 'mouse', content: 'Tekan tombol F9 pada keyboard disaat focus pada column potongan' });
            }
            $("#pengirimanGrid").jqxGrid({
                width: "100%",
                height: 360,
                source: pengirimanAdapter,
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
                        var commit = $("#pengirimanGrid").jqxGrid('addrow', null, datarow);
                    });
                    // delete row.
                    $("#deleterowbutton").on('click', function () {
                        var selectedrowindex = $("#pengirimanGrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#pengirimanGrid").jqxGrid('getdatainformation').rowscount;
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var rechapus = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                            hapusdetail.push({pengirimandet_id : rechapus.pengirimandet_id});

                            var id = $("#pengirimanGrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#pengirimanGrid").jqxGrid('deleterow', id);
                        }
                    });
                },
                columns: [
                    {
                        text: 'Barang', datafield: 'm_barang_id', displayfield: 'm_barang_nama', columntype: 'combobox',
                        initeditor: function (row, value, editor) {
                            editor.jqxComboBox({
                                source: barangAdapter,
                                valueMember: 'label',
                                displayMember: 'value',
                            });
                            editor.on('change', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var datasatkonv = satKonv;
                                if (event.args) {
                                    var val = event.args.item.value;
                                    dtsatkonv = datasatkonv.filter(p=>parseInt(p.m_barang_id)==val);
                                    var satkonv = [];
                                    for (let index = 0; index < dtsatkonv.length; index++) {
                                        const element = dtsatkonv[index];
                                        satkonv.push({ value: parseInt(element.satkonv_id), label: element.satuan_nama, satkonv_nilai : parseFloat(element.satkonv_nilai) });
                                    }

                                    let brg = barang.filter(p=>parseInt(p.value)==val);
                                    satkonv.push({ value: brg[0].satuan_id, label: brg[0].satuan_nama, satkonv_nilai : 1 });
                                    recorddata.satuankonv = JSON.stringify(satkonv);
                                    var pengiriman_tgl = $('#pengiriman_tgl').val();
                                    var data = {
                                        barang_id : val,
                                        tanggal : moment(pengiriman_tgl, "DD-MM-YYYY").format("YYYY-MM-DD"),
                                        m_rekanan_id : $("#m_rekanan_id").val()
                                    }
                                    let brg0 = brg[0];
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "m_satuan_id", brg0.satuan_id);
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "m_satuan_nama", brg0.satuan_nama);
                                    recorddata.m_barangsatuan_id = parseInt(brg0.satuan_id);
                                    
                                    $.post("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=gethargakontrak", data, function(result){
                                        let res = JSON.parse(result);
                                        $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_harga", res.hargakontrakdet_harga);
                                        recorddata.hargakontrakdet_ppn = res.hargakontrakdet_ppn;
                                        recorddata.hargakontrak = res.hargakontrakdet_harga;
                                        let pengirimandet_ppn = res.hargakontrakdet_ppn == 'Y' ? res.hargakontrakdet_harga*10/100 : 0;
                                        $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_ppn", pengirimandet_ppn);
                                        recorddata.m_barang_nama = brg0.label;
                                        recorddata.m_barang_id = parseInt(brg0.value);
                                        $.post("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getbahanbaku", data, function(result){
                                            let res = JSON.parse(result);
                                            res.forEach(element => {
                                                var dataw = {
                                                    hargakontrak: 0,
                                                    hargakontrakdet_ppn: "N",
                                                    m_barang_id: parseInt(element.m_barang_id),
                                                    m_barang_nama: element.m_barang_nama,
                                                    m_barangsatuan_id: parseInt(element.m_satuan_id),
                                                    m_bahanbakubrg_id: parseInt(brg0.value),
                                                    m_satuan_id: parseInt(element.m_satuan_id),
                                                    m_satuan_nama: element.satuan_nama,
                                                    pengirimandet_harga: 0,
                                                    pengirimandet_id: 0,
                                                    pengirimandet_potongan: 0,
                                                    pengirimandet_ppn: 0,
                                                    pengirimandet_qty: 0,
                                                    pengirimandet_qtyold: 0,
                                                    pengirimandet_subtotal: 0,
                                                    pengirimandet_total: 0,
                                                    satkonv_nilai: 1*element.bahanbrg_qty,
                                                    satuankonv: [],
                                                    t_pengiriman_id: "",
                                                    t_returdet_qty: 0,
                                                }
                                                $("#pengirimanGrid").jqxGrid('addrow', null, dataw);
                                                recorddata.m_barang_nama = brg0.label;
                                                recorddata.m_barang_id = parseInt(brg0.value); 
                                            });
                                        });
                                    });
                                    
                                }
                            });
                        },
                        width : 300,
                    },
                    {
                        text: 'Satuan', datafield: 'm_satuan_id', displayfield: 'm_satuan_nama', columntype: 'combobox',
                        initeditor: function (row, value, editor) {
                            var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            /* lintang */
                            var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                            var sat = JSON.parse(recorddata.satuankonv);
                            var satkonv = [];
                            var m_barangsatuan_nama = datasatuan.filter(p=>p.satuan_id==recorddata.m_barangsatuan_id);
                            console.log(m_barangsatuan_nama)
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
                            
                            editor.on('change', function (event) {
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
                                    var ppn = recorddata.hargakontrakdet_ppn == 'Y' ? (recorddata.hargakontrak * 10/100) : 0;
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_qty", 1);
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_ppn", parseFloat(ppn*val));
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_harga", (parseFloat(recorddata.hargakontrak*val) + parseFloat(ppn*val)));
                                    settotal();
                                }
                            });
                        }, 
                    },
                    { text: 'Harga', datafield: 'pengirimandet_harga', cellsalign: 'right', editable : false, width : 100 },
                    { text: 'Qty', datafield: 'pengirimandet_qty', cellsalign: 'right', columntype: 'numberinput',
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            let gridrecords = pengirimanAdapter.records;
                            let currentrecord = pengirimanAdapter.records[row];
                            editor.on('keyup', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                var ppn = recorddata.hargakontrakdet_ppn == 'Y' ? (recorddata.hargakontrak * 10/100) : 0;
                                var pengirimandet_subtotal = val * recorddata.hargakontrak * recorddata.satkonv_nilai;
                                $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_ppn", (pengirimandet_subtotal*10/100));
                                pengirimandet_subtotal = pengirimandet_subtotal + parseFloat(pengirimandet_subtotal*10/100)
                                $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_subtotal", pengirimandet_subtotal);
                                $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(pengirimandet_subtotal) - recorddata.pengirimandet_potongan));
                                settotal();

                                /* mengisi qty bahan baku */
                                let bbakuidx = gridrecords.findIndex(x => x.m_bahanbakubrg_id === currentrecord.m_barang_id);
                                if (bbakuidx) {
                                    let bbkurec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', bbakuidx);
                                    var qtybbaku = bbkurec.satkonv_nilai * val;
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', bbakuidx, "pengirimandet_qty", qtybbaku);
                                }
                            });
                            editor.on('change', function (event) {
                                let bbakuidx = gridrecords.findIndex(x => x.m_bahanbakubrg_id === currentrecord.m_barang_id);
                                if (bbakuidx) {
                                    let bbkurec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', bbakuidx);
                                    var qtybbaku = bbkurec.satkonv_nilai * val;
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', bbakuidx, "pengirimandet_qty", qtybbaku);
                                }
                            });
                        }
                    },
                    { text: 'PPN', datafield: 'pengirimandet_ppn', cellsalign: 'right', editable : false, width : 100 },
                    { text: 'Subtotal', datafield: 'pengirimandet_subtotal', cellsalign: 'right', editable : false, cellsformat: 'F',},
                    { text: 'Potongan', datafield: 'pengirimandet_potongan', cellsalign: 'right', columntype: 'numberinput', rendered: tooltiprenderer, cellsformat: 'F',
                        createeditor: function (row, value, editor) {
                            editor.jqxNumberInput({ decimalDigits: 0 });
                            editor.on('keyup', function (event) {
                                var recorddata = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', row);
                                var val = event.target.value||0;
                                if (event.keyCode == 120) {
                                    /* jika tekan tombol F9 */
                                    if (parseFloat(val)<=100) {
                                        editor.val(val/100*parseFloat(recorddata.pengirimandet_subtotal))
                                        $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(recorddata.pengirimandet_subtotal) - parseFloat(val/100*parseFloat(recorddata.pengirimandet_subtotal))));
                                    }
                                } else {
                                    $("#pengirimanGrid").jqxGrid('setcellvalue', row, "pengirimandet_total", (parseFloat(recorddata.pengirimandet_subtotal) - parseFloat(val)));
                                }
                                settotal();
                            });
                        } 
                    },
                    { text: 'Subtotal', datafield: 'pengirimandet_total', cellsalign: 'right', editable : false, cellsformat: 'F' },
                ]
            });

            settotal();
        });

    });

    function settotal() {
        var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
        var rows = [];
        var total = 0;
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
            total = total + parseFloat(rec.pengirimandet_total);
        }
        total = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        $('#pengiriman_total').val(total);
    }

    function renderJadwal(datajadwaldet) {
        var jadwalSource = {
            datatype: "array",
            localdata:  datajadwaldet,
            datafields: [
                { name: 'jadwal_id', type: 'int'},
                { name: 'rit', type: 'int'},
                { name: 'm_rekanan_id', type: 'int'},
                { name: 'rekanan_nama', type: 'string'},
                { name: 'm_pegdriver_id', type: 'int'},
                { name: 'm_pegdriver_nama', type: 'string'},
                { name: 'm_peghelper_id', type: 'int'},
                { name: 'm_peghelper_nama', type: 'string'},
                { name: 'm_barang_id', type: 'int'},
                { name: 'm_satuan_id', type: 'int'},
                { name: 'satuan_nama', type: 'string'},
                { name: 'barang_nama', type: 'string'},
                { name: 'hargakontrak', type: 'double'},
                { name: 'hargakontrakdet_ppn', type: 'string'},
                { name: 'minggu', type: 'int'},
                { name: 'hari', type: 'string'},
                { name: 'jadwal_qty', type: 'int'},
                { name: 'sudahkirim', type: 'float'},
            ],
            id: 'jadwal_id',
        };
        var dataAdapter = new $.jqx.dataAdapter(jadwalSource, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        $("#jadwalGrid").jqxGrid({
            width: '100%',
            height: '100%',
            source: dataAdapter,                
            pageable: false,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            filterable: true,
            autoshowfiltericon: true,
            columns: [
                { text: 'Hari', datafield: 'hari', cellsalign: 'left', width : 50 },
                { text: 'Rekanan', datafield: 'rekanan_nama', cellsalign: 'left', width : 200 },
                { text: 'Minggu', datafield: 'minggu', cellsalign: 'center' , width : 50},
                { text: 'Rit', datafield: 'rit', cellsalign: 'center', width : 120 },
                { text: 'Nama Barang', datafield: 'barang_nama', cellsalign: 'left' },
                { text: 'Jml', datafield: 'jadwal_qty', cellsalign: 'right' , width : 80},
                { text: 'Sudah Kirim', datafield: 'sudahkirim', cellsalign: 'right', width : 80 },
            ]
        });

        $("#jadwalGrid").on('rowselect', function (event) {
            $("#pengirimanGrid").jqxGrid('clear');
            var record = event.args.row;
            
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":record.m_rekanan_id, "text": record.rekanan_nama }
            });
            $("#m_pegdriver_id").data('select2').trigger('select', {
                data: {"id":record.m_pegdriver_id, "text": record.m_pegdriver_nama }
            });
            $("#m_peghelper_id").data('select2').trigger('select', {
                data: {"id":record.m_peghelper_id, "text": record.m_peghelper_nama }
            });
            $('#rit').val(record.rit);
            var qty = record.jadwal_qty - record.sudahkirim;
            qty = qty <= 0 ? record.jadwal_qty : qty;
            var ppn = record.hargakontrakdet_ppn == 'Y' ? (record.hargakontrak * 10/100) : 0;
            var datarow = {
                pengirimandet_id : 0,
                t_pengiriman_id : 0,
                satuankonv : JSON.stringify(satKonv.filter(p=>parseInt(p.m_barang_id)==record.m_barang_id)),
				m_barang_id : record.m_barang_id,
                m_barang_nama : record.barang_nama,
                m_barangsatuan_id : record.m_satuan_id,
				m_bahanbakubrg_id : 0,
                hargakontrak : record.hargakontrak,
                hargakontrakdet_ppn : record.hargakontrakdet_ppn,
                pengirimandet_ppn : ppn,
                pengirimandet_harga : record.hargakontrak,
				m_satuan_id : record.m_satuan_id,
                m_satuan_nama : record.satuan_nama,
                satkonv_nilai : 1,
                pengirimandet_qtyold : 0,
                pengirimandet_qty : qty,
                pengirimandet_subtotal : qty * record.hargakontrak,
                pengirimandet_potongan : 0,
                pengirimandet_total : qty * record.hargakontrak,
                t_returdet_qty : 0
            };
            
            $("#pengirimanGrid").jqxGrid('addrow', null, datarow);
			var pengiriman_tgl = $('#pengiriman_tgl').val()
            var data = {
				barang_id : record.m_barang_id,
				tanggal : moment(pengiriman_tgl, "DD-MM-YYYY").format("YYYY-MM-DD"),
				m_rekanan_id : $("#m_rekanan_id").val()
			};
			$.post("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getbahanbaku", data, function(result){
				let res = JSON.parse(result);
				res.forEach(element => {
					var dataw = {
						hargakontrak: 0,
						hargakontrakdet_ppn: "N",
						m_barang_id: parseInt(element.m_barang_id),
						m_barang_nama: element.m_barang_nama,
						m_barangsatuan_id: parseInt(element.m_satuan_id),
						m_bahanbakubrg_id: parseInt(record.m_barang_id),
						m_satuan_id: parseInt(element.m_satuan_id),
						m_satuan_nama: element.satuan_nama,
						pengirimandet_harga: 0,
						pengirimandet_id: 0,
						pengirimandet_potongan: 0,
						pengirimandet_ppn: 0,
						pengirimandet_qty: qty,
						pengirimandet_qtyold: 0,
						pengirimandet_subtotal: 0,
						pengirimandet_total: 0,
						satkonv_nilai: 1*element.bahanbrg_qty,
						satuankonv: [],
						t_pengiriman_id: "",
						t_returdet_qty: 0,
					}
					$("#pengirimanGrid").jqxGrid('addrow', null, dataw);
				});
			});
            settotal();
			$("#ModalJadwal").modal('toggle');
        });
    }
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_pengiriman_brg'">Kembali</button>
                    <button type="button" class="btn btn-default btn-sm" onclick="lihatJadwal()">Lihat Jadwal</button>
                </div>
                <form id="formpengiriman">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pengiriman_no">No. Pengiriman</label>
                                    <input type="hidden" id="pengiriman_id" name="pengiriman_id">
                                    <input type="hidden" id="rit" name="rit">
                                    <input type="text" class="form-control" id="pengiriman_no" name="pengiriman_no" readonly>
                                </div>
                                <?php 
                                $t_penagihan_no = isset($data->datapengiriman->t_penagihan_no) ? $data->datapengiriman->t_penagihan_no : '';
                                if ($t_penagihan_no <> '') {
                                    ?>
                                    <div class="form-group">
                                        <label for="penagihan_no">No. Penagihan</label>
                                        <input type="hidden" id="penagihan_id" name="penagihan_id">
                                        <input type="text" class="form-control" id="penagihan_no" name="penagihan_no" value="<?php echo $data->datapengiriman->t_penagihan_no ?>" readonly>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pengiriman_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="pengiriman_tgl" name="pengiriman_tgl"
                                    data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask require>
                                </div>
                                <div class="form-group">
                                    <label for="m_pegdriver_id">Driver</label>
                                    <select id="m_pegdriver_id" name="m_pegdriver_id" style="width: 100%;" require></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;" require></select>
                                </div>
                                <div class="form-group">
                                    <label for="m_peghelper_id">Helper</label>
                                    <select id="m_peghelper_id" name="m_peghelper_id" style="width: 100%;" require></select>
                                </div>
                                <div class="form-group">
                                    <label for="pengiriman_total">TOTAL</label>
                                    <input type="text" class="form-control" style="text-align: right;" id="pengiriman_total" name="pengiriman_total" value="0" readonly>
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
                            <div id="pengirimanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php 
                        $t_penagihan_no = isset($data->datapengiriman->t_penagihan_no) ? $data->datapengiriman->t_penagihan_no : '';
                        if ($delete <> '' &&  $t_penagihan_no == '') { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php 
                        if ((($create <> '' && isset($data->pengiriman_id) == 0) || ($update <> '' && $data->pengiriman_id > 0) && $t_penagihan_no == '')) { ?>
                        <button type="submit" id="simpan" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;" onclick="cetak()">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="ModalJadwal" tabindex="-1" role="dialog" aria-labelledby="ModalJadwalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card-body">
                            <div id="jadwalGrid" style="margin: 5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('[data-mask]').inputmask();
        var now = new Date();
        $('#pengiriman_tgl').val(moment(now).format('DD-MM-YYYY'));

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

        $("#m_pegdriver_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getpegdriver',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        tanggal: $('#pengiriman_tgl').val(),
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

        $("#m_peghelper_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getpeghelper',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        tanggal: $('#pengiriman_tgl').val(),
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

        $('#formpengiriman').submit(function (event) {
            event.preventDefault();
            var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1 || $('#m_pegdriver_id').val() < 1 || $('#m_peghelper_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(rec.m_barang_id)&&parseInt(p.m_satuan_id)==parseInt(rec.m_satuan_id));
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                if (rec.pengirimandet_qty > 0) {
                    rows.push({
                        'pengirimandet_id' : rec.pengirimandet_id,
                        't_pengiriman_id' : $('#pengiriman_id').val(),
                        'm_barang_id' : rec.m_barang_id,
                        'm_barang_nama' : rec.m_barang_nama,
                        'm_barangsatuan_id' : rec.m_barangsatuan_id,
                        'm_bahanbakubrg_id' : rec.m_bahanbakubrg_id,
                        'm_satuan_id' : rec.m_satuan_id,
                        'm_satuan_nama' : rec.m_satuan_nama,
                        'satkonv_nilai' : parseFloat(satkonv_nilai),
                        'hargakontrak' : parseFloat(rec.hargakontrak),
                        'pengirimandet_harga' : parseFloat(rec.pengirimandet_harga),
                        'pengirimandet_ppn' : parseFloat(rec.pengirimandet_ppn),
                        'pengirimandet_qty' : rec.pengirimandet_qty,
                        'pengirimandet_qtyold' : rec.pengirimandet_qtyold,
                        'pengirimandet_subtotal' : parseFloat(rec.pengirimandet_subtotal),
                        'pengirimandet_potongan' : parseFloat(rec.pengirimandet_potongan),
                        'pengirimandet_total' : parseFloat(rec.pengirimandet_total),
                    });
                } else if (rec.pengirimandet_qty == 0 && rec.m_satuan_id > 0) {
                    swal("Info!", "Pengiriman Gagal disimpan, terdapat isian dengan qty kosong ", "error");
                    return false;
                } else if (rec.m_satuan_id < 1) {
                    swal("Info!", "Pengiriman Gagal disimpan, terdapat isian dengan satuan kosong ", "error");
                    return false;
                }
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    pengiriman_id : $('#pengiriman_id').val(),
                    pengiriman_no : $('#pengiriman_no').val(),
                    rit : $('#rit').val(),
                    pengiriman_tgl : moment($('#pengiriman_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    m_pegdriver_id : $('#m_pegdriver_id').val(),
                    m_peghelper_id : $('#m_peghelper_id').val(),
                    rows : rows,
                    'hapusdetail' : hapusdetail
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        window.open('<?php echo BASE_URL;?>/controllers/C_pengiriman_brg.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "Pengiriman Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "Pengiriman Gagal disimpan", "error");
                    }
                }
            });
        });

        datapengiriman = '<?php echo $dataparse ?>';
        var datapengiriman = datapengiriman.replace(/\\n/g, "\\n").replace(/\\'/g, "\\'").replace(/\\"/g, '\\"').replace(/\\&/g, "\\&").replace(/\\r/g, "\\r").replace(/\\t/g, "\\t").replace(/\\b/g, "\\b").replace(/\\f/g, "\\f");
        datapengiriman = JSON.parse(datapengiriman.replace(/[\u0000-\u0019]+/g,""));
        if(datapengiriman!==null) {
            var dat = datapengiriman.datapengiriman;
            $('#pengiriman_id').val(dat.pengiriman_id);
            $('#pengiriman_no').val(dat.pengiriman_no);
            $('#rit').val(dat.rit);
            $('#pengiriman_tgl').val(moment(dat.pengiriman_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY'));
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            $("#m_pegdriver_id").data('select2').trigger('select', {
                data: {"id":dat.pegdriver_id, "text": dat.pegdriver_nama }
            });
            $("#m_peghelper_id").data('select2').trigger('select', {
                data: {"id":dat.peghelper_id, "text": dat.peghelper_nama }
            });
            $("#m_rekanan_id").prop("disabled", true);
            $('#batal').removeAttr('disabled');
            if (dat.t_penagihan_no !== '') {
                var penagihanstr = dat.t_penagihan_no == null || dat.t_penagihan_no == '' ? '' : ", sudah dibuat penagihan dengan No. Penagihan " + dat.t_penagihan_no;
                swal("Info!", "No. pengiriman " + dat.pengiriman_no + penagihanstr, "warning");
            }
        }

        $('#batal').on('click', function () {
            var griddata = $('#pengirimanGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#pengirimanGrid').jqxGrid('getrenderedrowdata', i);
                m_barang_id = barang.filter(p=>p.label==rec.m_barang_id);
                m_satuan_id = satuan.filter(p=>p.label==rec.m_satuan_id);
                dtsatkonv = satKonv.filter(p=>parseInt(p.m_barang_id)==parseInt(m_barang_id[0].value||0)&&parseInt(p.m_satuan_id)==parseInt(m_satuan_id[0].value||0));
                
                satkonv_nilai = 1;
                if(dtsatkonv.length > 0) { satkonv_nilai = dtsatkonv[0].satkonv_nilai}
                
                rows.push({'pengirimandet_id' : rec.pengirimandet_id});
            }

            swal({
                title: "Batalkan pengiriman " + $('#pengiriman_no').val(),
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
                    url: "<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        pengiriman_id : $('#pengiriman_id').val(),
                        pengiriman_no : $('#pengiriman_no').val(),
                        pengiriman_tgl : moment($('#pengiriman_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        m_rekanan_id : $('#m_rekanan_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        if (res == 200) {
                            resetForm();
                            swal("Info!", "Pengiriman Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "Pengiriman Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function cetak() {
        var pengiriman_id = $('#pengiriman_id').val();
        window.open('<?php echo BASE_URL;?>/controllers/C_pengiriman_brg.php?action=exportpdf&id=' + pengiriman_id);
    }

    function resetForm() {
        var now = new Date();
        $('#rit').val(0);
        $('#pengiriman_id').val(0);
        $('#pengiriman_no').val('');
        $('#m_rekanan_id').val('');
        $('#m_rekanan_id').trigger('change');
		$('#m_pegdriver_id').val('');
        $('#m_pegdriver_id').trigger('change');
		$('#m_peghelper_id').val('');
        $('#m_peghelper_id').trigger('change');
        $('#pengiriman_tgl').val(moment(now, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $("#pengirimanGrid").jqxGrid('clear');
    }

    function lihatJadwal() {
        var pengiriman_tgl = $('#pengiriman_tgl').val();
        var data = {
            tanggal : moment(pengiriman_tgl, "DD-MM-YYYY").format("YYYY-MM-DD")
        };
        $.post("<?php echo BASE_URL ?>/controllers/C_pengiriman_brg.php?action=getjadwal", data, function(result){
            $('#ModalJadwal').modal('toggle');
            renderJadwal(JSON.parse(result));
        });
    }
</script>