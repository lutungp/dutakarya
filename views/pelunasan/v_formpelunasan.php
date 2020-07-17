<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<script>
    $(document).ready(function () {
        var datapelunasan = JSON.parse('<?php echo $dataparse ?>');
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

        });
        
        if(datapelunasan!==null) {
            
        }

        var datapelunasandetail = datapelunasan == null ? [] : datapelunasan.datapelunasandetail;
        var datapelunasandet = [];
        for (let index = 0; index < datapelunasandetail.length; index++) {

        }

        var pelunasanGridSource = {
            datatype: "array",
            localdata:  datapelunasandet,
            datafields: [
                { name: 'pelunasandet_id', type: 'int'},
                { name: 't_pelunasan_id', type: 'int'},
                { name: 't_penagihan_id', type: 'int'},
                { name: 'penagihan_no', type: 'string'},
                { name: 'penagihan_tgl', type: 'date'},
                { name: 't_penagihandet_id', type: 'int'},
                { name: 'm_barang_id', type: 'int'},
                { name: 'barang_nama', type: 'string'},
                { name: 't_penagihandet_total', type: 'double'},
                { name: 'pelunasandet_bayar', type: 'double'}
            ],
        };

        var pelunasanAdapter = new $.jqx.dataAdapter(pelunasanGridSource);
        $("#pelunasanGrid").jqxGrid({
            width: "100%",
            // height: "100%",
            autoheight : true,
            altrows: true,
            // pageable : true,
            autorowheight : true,
            showstatusbar: true,
            statusbarheight: 50,
            showaggregates: true,
            source: pelunasanAdapter,
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
                    var selectedrowindex = $("#pelunasanGrid").jqxGrid('getselectedrowindex');
                    var rowscount = $("#pelunasanGrid").jqxGrid('getdatainformation').rowscount;
                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                        var rechapus = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', selectedrowindex);
                        hapusdetail.push({penagihandet_id : rechapus.penagihandet_id});

                        var id = $("#pelunasanGrid").jqxGrid('getrowid', selectedrowindex);
                        var commit = $("#pelunasanGrid").jqxGrid('deleterow', id);
                    }
                });
            },
            columns: [
                {
                    text: 'No. Penagihan', datafield: 'penagihan_no', displayfield: 'penagihan_no', editable : false, width : 250,
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.penagihan_no + "</br>";
                        html += moment(recorddata.penagihan_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        html += "</div>";
                        return html;
                    },
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '/>";
                        return renderstring;
                    }
                },
                { 
                    text: 'Tagihan', datafield: 't_penagihandet_total', displayfield: 't_penagihandet_total', editable : false, cellsalign : 'right',
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
                { 
                    text: 'Pembayaran', datafield: 'pelunasandet_bayar', displayfield: 'pelunasandet_bayar', editable : false, cellsalign : 'right', editable : true,
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
            ]
        });
    });
</script>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header primary">
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_pelunasan'">Kembali</button>
                </div>
                <form id="formpelunasan">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pelunasan_no">No. Pelunasan</label>
                                    <input type="hidden" id="pelunasan_id" name="pelunasan_id">
                                    <input type="text" class="form-control" id="pelunasan_no" name="pelunasan_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pelunasan_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="pelunasan_tgl" name="pelunasan_tgl"
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
                            <div id="pelunasanGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->pelunasan_id) == 0) || ($update <> '' && $data->pelunasan_id > 0)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm float-right" style="margin-right: 5px;" onclick="cetak()">Cetak</button>
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
        $('#pelunasan_tgl').val(moment(now).format('DD-MM-YYYY'));
    });

    function cetak() {

    }
</script>