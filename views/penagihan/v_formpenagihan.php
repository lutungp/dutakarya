<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<script>
    $(document).ready(function(){
        var datapenagihandetail = [];
        var datapenagihandet = [];
        for (let index = 0; index < datapenagihandetail.length; index++) {
            const element = datapenagihandetail[index];
            let datdet = {
                penagihandet_id : element.penagihandet_id,
                t_penagihan_id : element.t_penagihan_id,
                m_rekanan_id : element.m_rekanan_id,
                t_pengiriman_id : element.t_pengiriman_id,
                t_pengiriman_no : element.t_pengiriman_no,
                t_pengiriman_tgl : element.t_pengiriman_tgl,
                t_pengirimandet_id : element.t_pengirimandet_id,
                m_barang_id : element.m_barang_id,
                barang_nama : element.barang_nama,
                m_barangsatuan_id : element.m_barangsatuan_id,
                m_barangsatuan_nama : element.m_barangsatuan_nama,
                m_satuan_id : element.m_satuan_id,
                satkonv_nilai : element.satkonv_nilai,
                penagihandet_qty : element.penagihandet_qty,
                penagihandet_qtyreal : element.penagihandet_qtyreal,
                penagihandet_subtotal : element.penagihandet_subtotal,
                penagihandet_potongan : element.penagihandet_potongan,
                penagihandet_total : element.penagihandet_total,
                t_returdet_qty : element.t_returdet_qty,
            };
            datapenagihandet.push(datdet);
        }
        
        var penagihanGridSource = {
            datatype: "array",
            localdata:  datapenagihandet,
            // pagesize: 20,
            datafields: [
                { name: 'penagihandet_id', type: 'int'},
                { name: 't_penagihan_id', type: 'int'},
                { name: 'm_rekanan_id', type: 'int'},
                { name: 't_pengiriman_id', type: 'int'},
                { name: 't_pengiriman_no', type: 'string'},
                { name: 't_pengiriman_tgl', type: 'date'},
                { name: 't_pengirimandet_id', type: 'int'},
                { name: 'm_barang_id', type: 'int'},
                { name: 'barang_nama', type: 'string'},
                { name: 'm_barangsatuan_id', type: 'int'},
                { name: 'm_barangsatuan_nama', type: 'string'},
                { name: 'm_satuan_id', type: 'int'},
                { name: 'satkonv_nilai', type: 'float'},
                { name: 'penagihandet_qty', type: 'float'},
                { name: 'penagihandet_qtyreal', type: 'float'},
                { name: 'penagihandet_subtotal', type: 'float'},
                { name: 'penagihandet_potongan', type: 'float'},
                { name: 'penagihandet_total', type: 'float'},
                { name: 't_returdet_qty', type: 'float'},
            ],
        };

        var penagihanAdapter = new $.jqx.dataAdapter(penagihanGridSource);
        $("#penagihanGrid").jqxGrid({
            width: "100%",
            // height: "100%",
            autoheight : true,
            altrows: true,
            // pageable : true,
            autorowheight : true,
            showstatusbar: true,
            statusbarheight: 50,
            showaggregates: true,
            source: penagihanAdapter,
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
                    var selectedrowindex = $("#penagihanGrid").jqxGrid('getselectedrowindex');
                    var rowscount = $("#penagihanGrid").jqxGrid('getdatainformation').rowscount;
                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                        var rechapus = $('#penagihanGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                        hapusdetail.push({penagihandet_id : rechapus.penagihandet_id});

                        var id = $("#penagihanGrid").jqxGrid('getrowid', selectedrowindex);
                        var commit = $("#penagihanGrid").jqxGrid('deleterow', id);
                    }
                });
            },
            columns: [
                { 
                    text: 'No. Pengiriman', datafield: 't_pengiriman_no', displayfield: 't_pengiriman_no', editable : false, width : 250,
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#penagihanGrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.t_pengiriman_no + "</br>";
                        html += moment(recorddata.t_pengiriman_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        html += "</div>";
                        return html;
                    },
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '/>";
                        return renderstring;
                    }
                },
                { text: 'Barang', datafield: 'barang_nama', displayfield: 'barang_nama', editable : false, width : 250, 
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#penagihanGrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += value;
                        html += " " + (recorddata.penagihandet_qtyreal - recorddata.t_returdet_qty) + " " + recorddata.m_barangsatuan_nama;
                        html += "</div>";
                        return html;
                    },
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '/>";
                        return renderstring;
                    }
                },
                { text: 'Subtotal', datafield: 'penagihandet_subtotal', displayfield: 'penagihandet_subtotal', editable : false, cellsalign : 'right',
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
                { text: 'Potongan', datafield: 'penagihandet_potongan', displayfield: 'penagihandet_potongan', editable : false, cellsalign : 'right',
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
                { text: 'Total', datafield: 'penagihandet_total', displayfield: 'penagihandet_total', editable : false, cellsalign : 'right',
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
                }
            ]
        });
    });
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_penagihan'">Kembali</button>
                </div>
                <form id="formpenagihan">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penagihan_no">No. Penagihan</label>
                                    <input type="hidden" id="penagihan_id" name="penagihan_id">
                                    <input type="text" class="form-control" id="penagihan_no" name="penagihan_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penagihan_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="penagihan_tgl" name="penagihan_tgl"
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
                            <div id="penagihanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->penagihan_id) == 0) || ($update <> '' && $data->penagihan_id > 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="submit" class="btn btn-default btn-sm float-right" style="margin-right: 5px;">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('[data-mask]').inputmask();
        var now = new Date();
        $('#penagihan_tgl').val(moment(now).format('DD-MM-YYYY'));
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

        $('#m_rekanan_id').on('select2:select', function (e) {
            var value = $(e.currentTarget).find("option:selected").val();
            var data = {
                m_rekanan_id : value,
                penagihan_tgl : moment($('#penagihan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD')
            };
            $.post("<?php echo BASE_URL ?>/controllers/C_penagihan.php?action=getpengiriman", data, function(result){
                var res = JSON.parse(result);
                res.forEach(element => {
                    var datarow = {
                        penagihandet_id : element.penagihandet_id,
                        t_penagihan_id : element.t_penagihan_id,
                        m_rekanan_id : element.m_rekanan_id,
                        t_pengiriman_id : element.pengiriman_id,
                        t_pengiriman_no : element.pengiriman_no,
                        t_pengiriman_tgl : element.pengiriman_tgl,
                        t_pengirimandet_id : element.pengirimandet_id,
                        m_barang_id : element.m_barang_id,
                        barang_nama : element.barang_nama,
                        m_barangsatuan_id : element.m_barangsatuan_id,
                        m_barangsatuan_nama : element.m_barangsatuan_nama,
                        m_satuan_id : element.m_satuan_id,
                        satkonv_nilai : element.satkonv_nilai,
                        penagihandet_qty : element.pengirimandet_qty,
                        penagihandet_qtyreal : element.pengirimandet_qtyreal,
                        penagihandet_subtotal : element.pengirimandet_subtotal,
                        penagihandet_potongan : element.pengirimandet_potongan,
                        penagihandet_total : element.pengirimandet_total,
                        t_returdet_qty : element.t_returdet_qty,
                    }
                    $("#penagihanGrid").jqxGrid('addrow', null, datarow);
                });
            });
        });

        $('#formpenagihan').submit(function (event) {
            event.preventDefault();
            var griddata = $('#penagihanGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "Penagihan Gagal disimpan, detail penagihan masih kosong, pilih Rekanan terlebih dahulu", "warning");
                return false;
            }
            
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#penagihanGrid').jqxGrid('getrenderedrowdata', i);
                rows.push({
                    penagihandet_id : rec.penagihandet_id,
                    t_penagihan_id : rec.t_penagihan_id,
                    m_rekanan_id : rec.m_rekanan_id,
                    t_pengiriman_id : rec.t_pengiriman_id,
                    t_pengiriman_no : rec.t_pengiriman_no,
                    t_pengiriman_tgl : rec.t_pengiriman_tgl,
                    t_pengirimandet_id : rec.t_pengirimandet_id,
                    m_barang_id : rec.m_barang_id,
                    barang_nama : rec.barang_nama,
                    m_barangsatuan_id : rec.m_barangsatuan_id,
                    m_barangsatuan_nama : rec.m_barangsatuan_nama,
                    m_satuan_id : rec.m_satuan_id,
                    satkonv_nilai : parseFloat(rec.satkonv_nilai),
                    penagihandet_qty : parseFloat(rec.penagihandet_qty),
                    penagihandet_qtyreal : parseFloat(rec.penagihandet_qtyreal),
                    penagihandet_subtotal : parseFloat(rec.penagihandet_subtotal),
                    penagihandet_potongan : parseFloat(rec.penagihandet_potongan),
                    penagihandet_total : parseFloat(rec.penagihandet_total),
                    t_returdet_qty : parseFloat(rec.t_returdet_qty),
                }); 
            }
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_penagihan.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    penagihan_id : $('#penagihan_id').val(),
                    penagihan_no : $('#penagihan_no').val(),
                    penagihan_tgl : moment($('#penagihan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        window.open('<?php echo BASE_URL;?>/controllers/C_penagihan.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "Penagihan Berhasil disimpan", "success");
                        $("#ModalSatuan").modal('toggle');
                    } else {
                        swal("Info!", "Penagihan Gagal disimpan", "error");
                    }
                }
            });
        })
    });

    function resetForm() {
        
    }
</script>