<?php 
    require_once(__ROOT__.'/layouts/header_jqwidget.php');
    require_once(__ROOT__.'/layouts/userrole.php');
?>
<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=getpegawai";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'pegawai_id', type: 'int' },
                { name: 'pegawai_nama', type: 'string' },
                { name: 'pegawai_notelp', type: 'string' },
                { name: 'pegawai_bagian', type: 'string' },
                { name: 'pegawai_alamat', type: 'string' },
                { name: 'pegawaiaktif', type: 'string' }
            ],
            id: 'pegawai_id',
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
            $("#grid").jqxGrid('addfilter', 'pegawai_nama', filtergroup);
            // // apply the filters.
            // $("#grid").jqxGrid('applyfilters');
        }
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
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
                { text: 'Pegawai Nama', datafield: 'pegawai_nama'},
                { 
                    text: 'Pegawai Bagian', datafield: 'pegawai_bagian',
                    cellsrenderer: function (row) {
                        var recorddata = $('#grid').jqxGrid('getrenderedrowdata', row);
                        var pegawai_bagian = recorddata.pegawai_bagian;
                        pegawai_bagian = pegawai_bagian.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                            return letter.toUpperCase();
                        });
                        return '<div style="margin-top: 8.5px;margin-left: 8.5px;">' + pegawai_bagian + '</div>';
                    }
                },
                { text: 'No. Telp', datafield: 'pegawai_notelp'},
                { text: 'Pegawai Aktif', datafield: 'pegawaiaktif', filterable: false},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        $("#grid").offset();
                        $("#Modalpegawai").modal('toggle');
                        $("#pegawai_id").val(dataRecord.pegawai_id);
                        $("#pegawai_nama").val(dataRecord.pegawai_nama);
                        var pegawai_bagian = dataRecord.pegawai_bagian;
                        pegawai_bagian = pegawai_bagian.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                            return letter.toUpperCase();
                        });
                        $("#pegawai_bagian").data('select2').trigger('select', {
                            data: {"id":dataRecord.pegawai_bagian, "text": pegawai_bagian }
                        });
                        $("#pegawai_notelp").val(dataRecord.pegawai_notelp);
                        $("#pegawai_alamat").val(dataRecord.pegawai_alamat);
                        $.post("<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=getjadwal", { pegawai_id : dataRecord.pegawai_id }, function(result){
                            let res = JSON.parse(result);
                            if (res.length > 0) {
                                renderJadwal(res);   
                            }
                        });
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false, filterable: false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        editrow = row;
                        var dataRecord = $("#grid").jqxGrid('getrowdata', editrow);
                        swal({
                            title: "Hapus pegawai " + dataRecord.pegawai_nama,
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
                                url: "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=deletepegawai",
                                type: "post",
                                data: { pegawai_id : dataRecord.pegawai_id,  alasan : inputValue},
                                success : function (res) {
                                    $("#grid").jqxGrid('updatebounddata');
                                    if (res == 200) {
                                        swal("Info!", "Pegawai " + $("#pegawai_nama").val() + " Berhasil dihapus", "success");
                                    } else {
                                        swal("Info!", "Pegawai " + $("#pegawai_nama").val() + " Gagal dihapus", "error");
                                    }
                                }
                            });
                        });
                    }
                }
            ]
        });

        $("#m_pegawai_id").select2({
          ajax: {
            url: '<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=getpegawai',
            type: "post",
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

        var jadwalGridSource = {
            datatype: "array",
            localdata:  [
                    {
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 1,
                'hari_nama' : 'Senin',
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 2,
                'hari_nama' : 'Selasa',
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 3,
                'hari_nama' : 'Rabu',
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 4,
                'hari_nama' : 'Kamis',
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 5,
                'hari_nama' : "Jum'at",
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 6,
                'hari_nama' : 'Sabtu',
                'jadwalpeg_aktif' : false,
            },{
                'jadwalpeg_id' : 0,
                'm_pegawai_id' : 0,
                'hari' : 7,
                'hari_nama' : 'Minggu',
                'jadwalpeg_aktif' : false,
            }],
            datafields: [
                { name: 'jadwalpeg_id', type: 'int'},
                { name: 'hari_nama', type: 'string' },
                { name: 'hari', type: 'string' },
                { name: 'm_pegawai_id', type: 'int'},
                { name: 'jadwalpeg_aktif', type: 'boolean'}
            ]
        };

        var jadwalgridAdapter = new $.jqx.dataAdapter(jadwalGridSource);

        $("#jadwalGrid").jqxGrid({
            height : 300,
            width : "100%",
            source: jadwalgridAdapter,
            selectionmode: 'singlecell',
            editable: true,
            columns: [
                { text: 'Hari', datafield: 'hari_nama', displayfield: 'hari_nama' },
                { text: 'Masuk', datafield: 'jadwalpeg_aktif', threestatecheckbox: false, columntype: 'checkbox', width: 70 },
            ]
        });
    });

    function renderJadwal(data) {
        $("#jadwalGrid").jqxGrid('clear');
        var hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        for (let index = 0; index < 7; index++) {
            let datajdw = data.filter(p=>parseInt(p.hari)==parseInt(index+1));
            var jadwalpeg_aktif = datajdw.length > 0 ? datajdw[0].jadwalpeg_aktif : false;
            
            var jadwalpeg_id = datajdw.length > 0 ? datajdw[0].jadwalpeg_id : 0;
            var newdata = {
                'jadwalpeg_id' : jadwalpeg_id,
                'm_pegawai_id' : $('#pegawai_id').val(),
                'hari' : parseInt(index+1),
                'hari_nama' : hari[index],
                'jadwalpeg_aktif' : jadwalpeg_aktif == 'Y' ? true : false,
            }
            $("#jadwalGrid").jqxGrid('addrow', null, newdata);
        }
    }
</script>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addpegawai()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
                <div class="modal fade" id="Modalpegawai" tabindex="-1" role="dialog" aria-labelledby="ModalpegawaiLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalpegawaiLabel">Data pegawai</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" id="quickForm">
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="hidden" id="pegawai_id" name="pegawai_id">
                                            <label for="pegawai_nama">Nama</label>
                                            <input type="text" id="pegawai_nama" name="pegawai_nama" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_bagian">Bagian</label>
                                            <select id="pegawai_bagian" class="select2" style="width: 100%;">
                                                <option value="admin">Admin</option>
                                                <option value="driver">Driver</option>
                                                <option value="helper">Helper</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_notelp">No. Telp</label>
                                            <input type="text" id="pegawai_notelp" name="pegawai_notelp" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="pegawai_alamat">Alamat</label>
                                            <textarea class="form-control" id="pegawai_alamat" name="pegawai_alamat" rows="3" placeholder="Enter ..."></textarea>
                                        </div>
                                        <div id="jadwalGrid" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    $(document).ready(function () {
        $('.select2').select2();    
    });

    function submitForm() {
        if ($('#pegawai_nama').val() == '') {
            swal("Info!", "Inputan belum lengkap", "error");
            return false;
        }
        var griddata = $('#jadwalGrid').jqxGrid('getdatainformation');
        rows = [];
        for (var i = 0; i < griddata.rowscount; i++){
            var rec = $('#jadwalGrid').jqxGrid('getrenderedrowdata', i);
            rows.push({
                'jadwalpeg_id' : rec.jadwalpeg_id,
                'm_pegawai_id' : rec.m_pegawai_id,
                'hari' : rec.hari,
                'hari_nama' : rec.hari_nama,
                'jadwalpeg_aktif' : rec.jadwalpeg_aktif,
            }); 
        }
        var dataForm = {
            pegawai_id : $("#pegawai_id").val(),
            pegawai_nama : $("#pegawai_nama").val(),
            pegawai_bagian : $("#pegawai_bagian").val(),
            pegawai_alamat : $("#pegawai_alamat").val(),
            pegawai_notelp : $("#pegawai_notelp").val(),
            rows : rows
        };

        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_pegawai.php?action=createpegawai",
            type: "post",
            data: dataForm,
            success : function (res) {
                $("#grid").jqxGrid('updatebounddata');
                if (res == 200) {
                    resetForm();
                    swal("Info!", "pegawai " + $("#pegawai_nama").val() + " Berhasil disimpan", "success");
                    $("#Modalpegawai").modal('toggle');
                } else {
                    swal("Info!", "pegawai " + $("#pegawai_nama").val() + " Gagal disimpan", "error");
                }
            }
        });
    }

    function resetForm() {
        $("#pegawai_id").val(0);
        $("#pegawai_nama").val('');
    }

    function addpegawai() {
        resetForm();
        $("#Modalpegawai").modal('toggle');
    }
</script>