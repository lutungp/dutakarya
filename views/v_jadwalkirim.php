<?php 
    require_once(__ROOT__.'/layouts/userrole.php');
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    $data = json_decode($dataparse);
?>
<script>
    var datajadwaldet = [
        {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 1,
            hari : 1,
            hari_nama : 'Senin',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 2,
            hari : 2,
            hari_nama : 'Selasa',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 3,
            hari : 3,
            hari_nama : 'Rabu',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 4,
            hari : 4,
            hari_nama : 'Kamis',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 5,
            hari : 5,
            hari_nama : 'Jumat',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 6,
            hari : 6,
            hari_nama : 'Sabtu',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }, {
            jadwal_id : 0,
            m_pegdriver_id : 0,
            m_pegdriver_nama : '',
            m_peghelper_id : '',
            m_peghelper_nama : '',
            m_rekanan_id : 0,
            bulan : 7,
            hari : 7,
            hari_nama : 'Minggu',
            minggu1 : '',
            minggu2 : '',
            minggu3 : '',
            minggu4 : '',
            minggu5 : '',
        }
    ];
    $(document).ready(function () {
        gridrender2(datajadwaldet);
    });

    function gridrender2(datajadwaldet) {
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_jadwalkirim.php?action=getpegawai",
            type: "get",
            datatype : 'json',
            success : function (res) {
                res = JSON.parse(res);
                var driver = res.filter(p=>p.pegawai_bagian == 'driver');
                var driverArr = [];
                driver.forEach(element => {
                    driverArr.push({
                        value: element.pegawai_id, 
                        label: element.pegawai_nama
                    });
                });
                var helper = res.filter(p=>p.pegawai_bagian == 'helper');
                var helperArr = [];
                helper.forEach(element => {
                    helperArr.push({
                        value: element.pegawai_id, 
                        label: element.pegawai_nama
                    });
                });
                gridrender(datajadwaldet, driverArr, helperArr);
            }
        });
    }

    function gridrender(datajadwaldet, driverArr, helperArr) {
        var driverSource = {
            datatype: "array",
            datafields: [
                { name: 'label', type: 'string' },
                { name: 'value', type: 'int' }
            ],
            localdata: driverArr
        };
        var driverAdapter = new $.jqx.dataAdapter(driverSource, {
            autoBind: true
        });
        
        var helperSource = {
            datatype: "array",
            datafields: [
                { name: 'label', type: 'string' },
                { name: 'value', type: 'int' }
            ],
            localdata: helperArr
        };
        var helperAdapter = new $.jqx.dataAdapter(helperSource, {
            autoBind: true
        });

        var jadwalGridSource = {
            datatype: "array",
            localdata:  datajadwaldet,
            datafields: [
                { name: 'jadwal_id', type: 'int'},
                { name: 'm_pegdriver_nama', value: 'm_pegdriver_nama', values: { source: driverAdapter.records, value: 'pegawai_id', name: 'pegawai_nama' }},
                { name: 'm_pegdriver_id', type: 'int'},
                { name: 'm_peghelper_nama', value: 'm_peghelper_nama', values: { source: helperAdapter.records, value: 'pegawai_id', name: 'pegawai_nama' }},
                { name: 'm_peghelper_id', type: 'int'},
                { name: 'm_rekanan_id', type: 'int'},
                { name: 'bulan', type: 'int'},
                { name: 'hari', type: 'int'},
                { name: 'hari_nama', type: 'string'},
                { name: 'minggu1', type: 'int'},
                { name: 'minggu2', type: 'int'},
                { name: 'minggu3', type: 'int'},
                { name: 'minggu4', type: 'int'},
                { name: 'minggu5', type: 'int'},
            ],
        };
        var jadwalAdapter = new $.jqx.dataAdapter(jadwalGridSource);

        $("#jadwalGrid").jqxGrid({
            width: "100%",
            height: "100%",
            source: jadwalAdapter,
            editable: true,
            selectionmode: 'singlecell',
            columns: [
                { text: 'Hari', datafield: 'hari_nama', cellsalign: 'left', editable : false },
                {   
                    text: 'Driver', datafield: 'm_pegdriver_id', displayfield: 'm_pegdriver_nama', columntype: 'combobox',
                    createeditor: function (row, value, editor) {
                        editor.jqxComboBox({
                            source: driverAdapter,
                            valueMember: 'label',
                            displayMember: 'value',
                        });
                    },
                },
                {   
                    text: 'Helper', datafield: 'm_peghelper_id', displayfield: 'm_peghelper_nama', columntype: 'combobox',
                    createeditor: function (row, value, editor) {
                        editor.jqxComboBox({
                            source: helperAdapter,
                            valueMember: 'label',
                            displayMember: 'value',
                        });
                    },
                },
                { text: 'Minggu 1', datafield: 'minggu1', cellsalign: 'right', editable : true },
                { text: 'Minggu 2', datafield: 'minggu2', cellsalign: 'right', editable : true },
                { text: 'Minggu 3', datafield: 'minggu3', cellsalign: 'right', editable : true },
                { text: 'Minggu 4', datafield: 'minggu4', cellsalign: 'right', editable : true },
                { text: 'Minggu 5', datafield: 'minggu5', cellsalign: 'right', editable : true },
            ]
        });
    }
</script>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <form id="formjadwal">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Bulan & Tahun</label>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-12" style="margin-bottom : 5px;">
                                            <select id="bulan" class="form-control select2" style="width: 100%;">
                                                <option value=""></option>
                                            <?php 
                                                $bulan = $data->bulan;
                                                foreach ($bulan as $key => $value) {
                                                    $selected = $value->value==date('m') ? 'selected':'';
                                                    echo '<option value="'.$value->value.'" '.$selected.'>'.$value->text.'</option>';
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-12" style="margin-bottom : 5px;">
                                            <select id="tahun" class="form-control select2" style="width: 100%;">
                                                <option value=""></option>
                                            <?php 
                                                $tahun = $data->tahun;
                                                foreach ($tahun as $key => $value) {
                                                    $selected = $value==intval(date('Y')) ? 'selected':'';
                                                    echo '<option value="'.$value.'"  ' . $selected . '>'.$value.'</option>';
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="m_rekanan_id">Rekanan</label>
                                    <select id="m_rekanan_id" name="m_rekanan_id" style="width: 100%;" require></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="rit">Rit</label>
                                    <select id="rit" name="rit" class="select2" style="width: 100%;" require>
                                    <option value="1">Satu</option>
                                    <option value="2">Dua</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="m_barang_id">Barang</label>
                                    <select id="m_barang_id" name="m_barang_id" style="width: 100%;" require></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="jadwalGrid" style="margin: 10px;"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if (($create <> '' && isset($data->retur_id) == 0) || ($update <> '' && $data->retur_id > 0)) { ?>
                            <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(function () {
        $('.select2').select2()
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

        $("#m_barang_id").select2({
            ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_jadwalkirim.php?action=getbarang',
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
        $('#m_barang_id').on('select2:select', function (e) {
            getJadwal();
        });
        $('#m_rekanan_id').on('select2:select', function (e) {
            getJadwal();
        });
        $('#rit').on('select2:select', function (e) {
            getJadwal();
        });

        $('#formjadwal').submit(function (event) {
            event.preventDefault();
            var griddata = $('#jadwalGrid').jqxGrid('getdatainformation');
            var rows = [];
            for (var i = 0; i < griddata.rowscount; i++){
                var rec = $('#jadwalGrid').jqxGrid('getrenderedrowdata', i);
                
                rows.push({
                    jadwal_id : rec.jadwal_id,
                    m_rekanan_id : $('#m_rekanan_id').val(),
                    bulan : $('#bulan').val(),
                    tahun : $('#tahun').val(),
                    rit : $('#rit').val(),
                    m_barang_id : $('#m_barang_id').val(),
                    hari : rec.hari,
                    hari_nama : rec.hari_nama,
                    m_pegdriver_id : rec.m_pegdriver_id,
                    m_peghelper_id : rec.m_peghelper_id,
                    minggu1 : rec.minggu1,
                    minggu2 : rec.minggu2,
                    minggu3 : rec.minggu3,
                    minggu4 : rec.minggu4,
                    minggu5 : rec.minggu5,
                }); 
            }
            
            if ($('#m_rekanan_id').val() < 1 || $('bulan').val() == '' || $('tahun').val() < 1 ) {
                swal("Info!", "Inputan Belum Lengkap", "error");
                return false;
            }
            
            $.ajax({
                url: "<?php echo BASE_URL ?>/controllers/C_jadwalkirim.php?action=submit",
                type: "post",
                datatype : 'json',
                data: {rows : rows},
                success : function (res) {
                    if (parseInt(res) == 200) {
                        resetForm();
                        swal("Info!", "Jadwal Berhasil disimpan", "success");
                        $("#ModalSatuan").modal('toggle');
                    } else {
                        swal("Info!", "Jadwal Gagal disimpan", "error");
                    }
                }
            });
        });
    });

    function resetForm() {
        $("#m_rekanan_id").empty();
        $("#jadwalGrid").jqxGrid('clear');
        gridrender(datajadwaldet);
    }

    function getJadwal() {
        var data = {
            m_rekanan_id : $('#m_rekanan_id').val(),
            bulan : $('#bulan').val(),
            tahun : $('#tahun').val(),
            rit : $('#rit').val(),
            m_barang_id : $('#m_barang_id').val(),
        }
        $.post("<?php echo BASE_URL ?>/controllers/C_jadwalkirim.php?action=getjadwal", data, function(result){
            var res = JSON.parse(result);
            var datajadwal = [];
            var hari = [
                {
                    hari : 1,
                    hari_nama : 'Senin'
                }, {
                    hari : 2,
                    hari_nama : 'Selasa'
                }, {
                    hari : 3,
                    hari_nama : 'Rabu'
                }, {
                    hari : 4,
                    hari_nama : 'Kamis'
                }, {
                    hari : 5,
                    hari_nama : 'Jumat'
                }, {
                    hari : 6,
                    hari_nama : 'Sabtu'
                }, {
                    hari : 7,
                    hari_nama : 'Minggu'
                },
            ]
            res.forEach(element => {
                hariint = hari.filter(p=>p.hari==parseInt(element.hari));
                datajadwal.push({
                    jadwal_id : element.jadwal_id,
                    m_rekanan_id : element.m_rekanan_id,
                    bulan : element.bulan,
                    rit : element.rit,
                    hari : element.hari,
                    hari_nama : hariint[0].hari_nama,
                    m_pegdriver_id : element.m_pegdriver_id,
                    m_pegdriver_nama : element.m_pegdriver_nama,
                    m_peghelper_id : element.m_peghelper_id,
                    m_peghelper_nama : element.m_peghelper_nama,
                    minggu1 : element.minggu1,
                    minggu2 : element.minggu2,
                    minggu3 : element.minggu3,
                    minggu4 : element.minggu4,
                    minggu5 : element.minggu5,
                });
            });
            if (res.length > 0) {
                gridrender2(datajadwal);
            } else {
                gridrender2(datajadwaldet);
            }
        });
    }
</script>