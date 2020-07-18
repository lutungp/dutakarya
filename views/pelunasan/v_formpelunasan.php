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
            $("#pelunasanGrid").jqxGrid('clear');
            var value = $(e.currentTarget).find("option:selected").val();
            var data = {
                m_rekanan_id : value,
                pelunasan_tgl : moment($('#pelunasan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD')
            };
            var pelunasan_id = datapelunasan == null ? 0 : datapelunasan.datapelunasan.pelunasan_id;
            if (pelunasan_id < 1) {
                $.post("<?php echo BASE_URL ?>/controllers/C_pelunasan.php?action=getpenagihan", data, function(result){
                    var res = JSON.parse(result);
                    if (res.length > 0) {
                        res.forEach(element => {
                            var datarow = {
                                pelunasandet_id : 0,
                                t_pelunasan_id : 0,
                                t_penagihan_id : element.penagihan_id,
                                penagihan_noa : element.penagihan_no,
                                penagihan_no : element.penagihan_no,
                                penagihan_tgl : element.penagihan_tgl,
                                m_rekanan_id : element.m_rekanan_id,
                                pelunasandet_tagihan : element.penagihandet_total,
                                penagihandet_total : element.penagihandet_total,
                                pelunasandet_bayar : 0,
                            };
                            $("#pelunasanGrid").jqxGrid('addrow', null, datarow);
                        });
                        $("#pelunasanGrid").jqxGrid('selectcell', 0, 'penagihan_no');
                        $("#pelunasanGrid").jqxGrid('focus');
                    }
                });
            }
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
                { name: 'penagihan_noa', type: 'string'},
                { name: 'penagihan_no', type: 'string'},
                { name: 'penagihan_tgl', type: 'date'},
                { name: 'pelunasandet_tagihan', type: 'double'},
                { name: 'penagihandet_total', type: 'double'},
                { name: 'pelunasandet_bayar', type: 'double'},
            ],
        };

        var pelunasanAdapter = new $.jqx.dataAdapter(pelunasanGridSource);
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: 'Tekan tombol F9 pada keyboard disaat focus pada column pembayaran' });
        }
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
            editmode: 'selectedcell',
            selectionmode: 'singlecell',
            handlekeyboardnavigation: function (eventkey) {
                $("#pelunasanGrid").on('cellselect', function (event) {
                    var columnheader = $("#pelunasanGrid").jqxGrid('getcolumn', event.args.datafield).text;
                    if (columnheader == 'Tagihan') {
                        if (eventkey.keyCode == 86 && eventkey.ctrlKey == true) {
                            let recorddata = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', event.args.rowindex);
                            $("#pelunasanGrid").jqxGrid('setcellvalue', event.args.rowindex, "penagihandet_total", recorddata.pelunasandet_tagihan);
                        }
                    }
                    if (columnheader == 'No. Penagihan') {
                        if (eventkey.keyCode == 86 && eventkey.ctrlKey == true) {
                            let recorddata = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', event.args.rowindex);
                            $("#pelunasanGrid").jqxGrid('setcellvalue', event.args.rowindex, "penagihan_no", recorddata.penagihan_noa);
                        }
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
                    text: 'Tagihan', datafield: 'penagihandet_total', displayfield: 'penagihandet_total', editable : false, cellsalign : 'right',
                    cellsformat: 'F',
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
                    text: 'Pembayaran', datafield: 'pelunasandet_bayar', editable : true, cellsalign : 'right', columntype: 'numberinput',
                    cellsformat: 'F',
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

    function setLunasAll() {
        var griddata = $('#pelunasanGrid').jqxGrid('getdatainformation');
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', i);
            // rec.pelunasandetcheck = true;
            // rec.pelunasandet_bayar = rec.penagihandet_total;
            // $("#pelunasanGrid").jqxGrid('setcellvalue', i, "pelunasandet_bayar", rec.penagihandet_total);
        }
    }
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

        $('#formpelunasan').submit(function (event) {
            event.preventDefault();
            var griddata = $('#pelunasanGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            var totalbayar = 0;
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#pelunasanGrid').jqxGrid('getrenderedrowdata', i);
                
                if (rec.pelunasandet_bayar > 0) {
                    rows.push({
                        pelunasandet_id : rec.pelunasandet_id,
                        t_pelunasan_id : rec.t_pelunasan_id,
                        t_penagihan_id : rec.t_penagihan_id,
                        penagihan_noa : rec.penagihan_noa,
                        penagihan_no : rec.penagihan_no,
                        penagihan_tgl : rec.penagihan_tgl,
                        pelunasandet_tagihan : rec.pelunasandet_tagihan,
                        penagihandet_total : rec.penagihandet_total,
                        pelunasandet_bayar : rec.pelunasandet_bayar,
                    });
                    totalbayar = totalbayar + parseFloat(rec.pelunasandet_bayar);
                }
            }

            if (totalbayar == 0) {
                swal("Info!", "Pembayaran belum diisi sama", "error");
                return false;
            }

            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_pelunasan.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    pelunasan_id : $('#pelunasan_id').val(),
                    pelunasan_no : $('#pelunasan_no').val(),
                    pelunasan_tgl : moment($('#pelunasan_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        // window.open('<?php // echo BASE_URL;?>/controllers/C_pelunasan.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "Pelunasan Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "Pelunasan Gagal disimpan", "error");
                    }
                }
            });
        });
    });

    function resetForm() {
        
    }

    function cetak() {

    }
</script>