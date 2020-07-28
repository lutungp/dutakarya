<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<script>
    $(document).ready(function () {
        var datahutang = JSON.parse('<?php echo $dataparse ?>');
        $("#m_rekanan_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_hutang.php?action=getrekanan',
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
            $("#hutangGrid").jqxGrid('clear');
            var value = $(e.currentTarget).find("option:selected").val();
            var data = {
                m_rekanan_id : value,
                hutang_tgl : moment($('#hutang_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD')
            };
            var hutang_id = datahutang == null ? 0 : datahutang.datahutang.hutang_id;
            if (hutang_id < 1) {
                $.post("<?php echo BASE_URL ?>/controllers/C_hutang.php?action=getmaklondet", data, function(result){
                    var res = JSON.parse(result);
                    if (res.length > 0) {
                        res.forEach(element => {
                            $("#hutangGrid").jqxGrid('addrow', null, element);
                        });
                    //     $("#hutangGrid").jqxGrid('selectcell', 0, 'hutangdet_no');
                    //     $("#hutangGrid").jqxGrid('focus');
                    }
                });
            }
        });
        
        if(datahutang!==null) {
            let dat = datahutang.datahutang;
            $('#hutang_id').val(dat.hutang_id);
            $('#hutang_no').val(dat.hutang_no);
            $('#hutang_tgl').val(dat.hutang_tgl);
            $("#m_rekanan_id").data('select2').trigger('select', {
                data: {"id":dat.m_rekanan_id, "text": dat.rekanan_nama }
            });
            $("#m_rekanan_id").prop("disabled", true);
            $('#batal').prop("disabled", false);
        }

        var datahutangdet = datahutang == null ? [] : datahutang.datahutangdetail;

        var hutangGridSource = {
            datatype: "array",
            localdata:  datahutangdet,
            datafields: [
                { name: 'hutangdet_id', type: 'int'},
                { name: 't_hutang_id', type: 'int'},
                { name: 't_maklon_id', type: 'int'},
                { name: 'maklon_no', type: 'string'},
                { name: 'maklon_noa', type: 'string'},
                { name: 'maklon_tgl', type: 'date'},
                { name: 't_maklondet_id', type: 'int'},
                { name: 'm_barang_id', type: 'int'},
                { name: 'barang_nama', type: 'string'},
                { name: 'm_satuan_id', type: 'int'},
                { name: 'satuan_nama', type: 'string'},
                { name: 't_maklondet_qty', type: 'double'},
                { name: 't_maklondet_subtotal', type: 'double'},
                { name: 't_maklondet_ppn', type: 'double'},
                { name: 'hutangdet_noa', type: 'string'},
                { name: 'hutangdet_tagihan', type: 'double'},
                { name: 'hutangdet_bayarold', type: 'double'},
                { name: 'hutangdet_bayar', type: 'double'},
            ],
        };

        var hutangAdapter = new $.jqx.dataAdapter(hutangGridSource);
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: 'Tekan tombol F9 pada keyboard disaat focus pada column pembayaran' });
        }
        $("#hutangGrid").jqxGrid({
            width: "100%",
            // height: "100%",
            autoheight : true,
            altrows: true,
            // pageable : true,
            autorowheight : true,
            showstatusbar: true,
            statusbarheight: 50,
            showaggregates: true,
            source: hutangAdapter,
            editable: true,
            editmode: 'selectedcell',
            selectionmode: 'singlecell',
            handlekeyboardnavigation: function (eventkey) {
                $("#hutangGrid").on('cellselect', function (event) {
                    var columnheader = $("#hutangGrid").jqxGrid('getcolumn', event.args.datafield).text;
                    if (columnheader == 'Tagihan') {
                        if (eventkey.keyCode == 86 && eventkey.ctrlKey == true) {
                            let recorddata = $('#hutangGrid').jqxGrid('getrenderedrowdata', event.args.rowindex);
                            $("#hutangGrid").jqxGrid('setcellvalue', event.args.rowindex, "hutangdet_bayar", recorddata.hutangdet_tagihan);
                            return true;
                        }
                    }
                    if (columnheader == 'No. Maklon') {
                        if (eventkey.keyCode == 86 && eventkey.ctrlKey == true) {
                            let recorddata = $('#hutangGrid').jqxGrid('getrenderedrowdata', event.args.rowindex);
                            $("#hutangGrid").jqxGrid('setcellvalue', event.args.rowindex, "maklon_no", recorddata.maklon_noa);
                            return true;
                        }
                    }
                    if (columnheader == 'Barang') {
                        if (eventkey.keyCode == 86 && eventkey.ctrlKey == true) {
                            let recorddata = $('#hutangGrid').jqxGrid('getrenderedrowdata', event.args.rowindex);
                            $("#hutangGrid").jqxGrid('setcellvalue', event.args.rowindex, "barang_nama", recorddata.barang_namaa);
                            return true;
                        }
                    }
                });
            },
            columns: [
                {
                    text: 'No. Maklon', datafield: 'maklon_no', displayfield: 'maklon_no', editable : false, width : 250,
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#hutangGrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.maklon_no + "</br>";
                        html += moment(recorddata.maklon_tgl, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        html += "</div>";
                        return html;
                    },
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '/>";
                        return renderstring;
                    }
                },
                {
                    text: 'Barang', datafield: 'barang_nama', displayfield: 'barang_nama', editable : false, width : 250,
                    cellsrenderer : function (row, column, value) {
                        var recorddata = $('#hutangGrid').jqxGrid('getrenderedrowdata', row);
                        var html = "<div style='padding: 5px;'>";
                        html += recorddata.barang_nama + " " + recorddata.t_maklondet_qty + " " + recorddata.satuan_nama;
                        html += "</div>";
                        return html;
                    }
                },
                { 
                    text: 'Tagihan', datafield: 'hutangdet_tagihan', displayfield: 'hutangdet_tagihan', editable : false, cellsalign : 'right', columntype: 'numberinput',
                    // cellsformat: 'F',
                    aggregates: ['sum'],
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                        var subtotal = 0;
                        $.each(aggregates, function (key, value) {
                            subtotal = parseFloat(subtotal) + parseFloat(value);
                            renderstring += '<div style="padding:5px;font-size:16px;"><b>' + subtotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '</b></div>';
                        });
                        renderstring += "</div>";
                        return renderstring;
                    }
                },
                { 
                    text: 'Pembayaran', datafield: 'hutangdet_bayar', displayfield: 'hutangdet_bayar', editable : true, cellsalign : 'right', columntype: 'numberinput',
                    aggregates: ['sum'],
                    aggregatesrenderer: function (aggregates, column, element) {
                        var renderstring = "<div class='jqx-widget-content jqx-widget-content-office' style='float: left; width: 100%; height: 100%; '>";
                        var subtotal = 0;
                        $.each(aggregates, function (key, value) {
                            subtotal = parseFloat(subtotal) + parseFloat(value);
                            renderstring += '<div style="padding:5px;font-size:16px;"><b>' + subtotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '</b></div>';
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
                    <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_hutang'">Kembali</button>
                </div>
                <form id="formhutang">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hutang_no">No. Hutang</label>
                                    <input type="hidden" id="hutang_id" name="hutang_id">
                                    <input type="text" class="form-control" id="hutang_no" name="hutang_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hutang_tgl">Tanggal</label>
                                    <input type="text" class="form-control tgllahir" id="hutang_tgl" name="hutang_tgl"
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
                            <div id="hutangGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($delete <> '' ) { ?>
                        <button type="button" id="batal" class="btn btn-danger btn-sm" disabled>Batal</button>
                        <?php } ?>
                        <?php if (($create <> '' && isset($data->hutang_id) == 0) || ($update <> '' && $data->hutang_id > 0)) { ?>
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
        $('#hutang_tgl').val(moment(now).format('DD-MM-YYYY'));

        $('#formhutang').submit(function (event) {
            event.preventDefault();
            var griddata = $('#hutangGrid').jqxGrid('getdatainformation');
            var rows = [];

            if ($('#m_rekanan_id').val() < 1) {
                swal("Info!", "Inputan belum lengkap", "error");
                return false;
            }

            var totalbayar = 0;
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#hutangGrid').jqxGrid('getrenderedrowdata', i);
                
                if (rec.hutangdet_bayar != rec.hutangdet_bayarold) {
                    rows.push({
                        hutangdet_id : rec.hutangdet_id,
                        t_hutang_id : rec.t_hutang_id,
                        t_maklon_id : rec.t_maklon_id,
                        maklon_no : rec.maklon_no,
                        maklon_noa : rec.maklon_noa,
                        maklon_tgl : rec.maklon_tgl,
                        t_maklondet_id : rec.t_maklondet_id,
                        m_barang_id : rec.m_barang_id,
                        barang_nama : rec.barang_nama,
                        m_satuan_id : rec.m_satuan_id,
                        satuan_nama : rec.satuan_nama,
                        t_maklondet_qty : parseFloat(rec.t_maklondet_qty),
                        t_maklondet_subtotal : parseFloat(rec.t_maklondet_subtotal),
                        t_maklondet_ppn : parseFloat(rec.t_maklondet_ppn),
                        hutangdet_noa : rec.hutangdet_noa,
                        hutangdet_tagihan : parseFloat(rec.hutangdet_tagihan),
                        hutangdet_bayarold : parseFloat(rec.hutangdet_bayarold),
                        hutangdet_bayar : parseFloat(rec.hutangdet_bayar),
                    });
                    totalbayar = totalbayar + parseFloat(rec.hutangdet_bayar);
                }
            }

            if (totalbayar == 0) {
                swal("Info!", "Pembayaran belum diisi sama", "error");
                return false;
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_hutang.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {
                    hutang_id : $('#hutang_id').val(),
                    hutang_no : $('#hutang_no').val(),
                    hutang_tgl : moment($('#hutang_tgl').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    rows : rows,
                },
                success : function (res) {
                    res = JSON.parse(res);
                    if (res['code'] == 200) {
                        window.open('<?php echo BASE_URL;?>/controllers/C_hutang.php?action=exportpdf&id=' + res['id']);
                        resetForm();
                        swal("Info!", "Hutang Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "Hutang Gagal disimpan", "error");
                    }
                }
            });
        });

        $('#batal').on('click', function () {
            var rows = [];
            var griddata = $('#hutangGrid').jqxGrid('getdatainformation');
            if(griddata.rowscount == 0) {
                swal("Info!", "Pembatalan Gagal disimpan, detail hutangdet masih kosong, refresh halaman terlebih dahulu", "warning");
                return false;
            }
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#hutangGrid').jqxGrid('getrenderedrowdata', i);
                rows.push({
                    t_hutangdet_id : rec.t_hutangdet_id,
                    hutangdet_bayar : rec.hutangdet_bayar
                }); 
            }
            swal({
                title: "Batalkan hutang " + $('#hutang_no').val(),
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
                    url: "<?php echo BASE_URL ?>/controllers/C_hutang.php?action=batal",
                    type: "post",
                    datatype : 'json',
                    data: {
                        hutang_id : $('#hutang_id').val(),
                        rows : rows,
                        alasan : inputValue
                    },
                    success : function (res) {
                        res = JSON.parse(res);
                        if (res['code'] == 200) {
                            resetForm();
                            swal("Info!", "Hutang Berhasil dibatalkan", "success");
                        } else {
                            swal("Info!", "Hutang Gagal dibatalkan", "error");
                        }
                    }
                });
            });
        });
    });

    function resetForm() {
        var now = new Date();
        $('#hutang_id').val(0)
        $('#hutang_tgl').val(moment(now).format('DD-MM-YYYY'));
        $('#m_rekanan_id').empty()
        $("#hutangGrid").jqxGrid('clear');
    }

    function cetak() {
        window.open('<?php echo BASE_URL;?>/controllers/C_hutang.php?action=exportpdf&id=' + $('#hutang_id').val());
    }
</script>