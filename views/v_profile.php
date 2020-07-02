<style>
  @media (min-width: 750px) {
    .summary-profile {
      font-size: 13px;
    }
  }

  @media (max-width: 500px) {
    .card-body {
      padding : 10px;
    }
  }
  
</style>
<script>
    $(document).ready(function () {
      $('#photoprofile').on('change',function(){
          //get the file name
          var fileName = $(this).val();
          //replace the "Choose a file" label
          $(this).next('.custom-file-label').html(fileName);
      })
    });
</script>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline summary-profile">
          <div class="card-body box-profile">
            <div class="text-center">
              <?php 
                $imageprofile = BASE_URL . "/assets/img/dummy-profile.png";
                if ($dataparse["pegawai"]->pegawai_photo <> '') {
                    $imageprofile = BASE_URL . $dataparse["pegawai"]->pegawai_photo;
                }
              ?>
              <img class="profile-user-img img-fluid img-circle responsive" src="<?php echo $imageprofile; ?>" 
              alt="User profile picture" data-toggle="modal" data-target="#uploadModal" style="width: 150px;height: 150px;">
            </div>

            <h3 class="profile-username text-center"><?php echo $user->user_nama?></h3>

            <p class="text-muted text-center"><?php echo $dataparse["pegawai"]->pegawai_nama; ?></p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>NIK</b> <a class="float-right"><?php echo $dataparse["pegawai"]->pegawai_kode; ?></a>
              </li>
              <li class="list-group-item">
                <?php $unit_nama = $dataparse["pegawai"]->unit_nama <> '' ? $dataparse["pegawai"]->unit_nama : "-"?>
                <b>Bagian / Unit</b> <a class="float-right"><?php echo $unit_nama; ?></a>
              </li>
              <li class="list-group-item">
                <?php $pegawaijbt_nama = $dataparse["pegawai"]->pegawaijbt_nama <> '' ? $dataparse["pegawai"]->pegawaijbt_nama : "-"?>
                <b>Jabatan</b> <a class="float-right"><?php echo $pegawaijbt_nama; ?></a>
              </li>
            </ul>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- About Me Box -->
        <div class="card card-primary summary-profile">
          <div class="card-header">
            <h3 class="card-title">Rumah</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <strong><i class="fas fa-home mr-1"></i> Alamat</strong>

            <p class="text-muted">
              <?php $pegawai_alamat = $dataparse["pegawai"]->pegawai_alamat <> '' ? $dataparse["pegawai"]->pegawai_alamat : "-"?>
              <?php echo $pegawai_alamat ?>
            </p>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Lokasi</strong>
            <?php 
              $lokasi = array();
              if ($dataparse["pegawai"]->alamatkelurahan <> "") {
                array_push($lokasi, $dataparse["pegawai"]->alamatkelurahan);
              }

              if ($dataparse["pegawai"]->alamatkecamatan <> "") {
                array_push($lokasi, $dataparse["pegawai"]->alamatkecamatan);
              }

              if ($dataparse["pegawai"]->alamatkota <> "") {
                array_push($lokasi, $dataparse["pegawai"]->alamatkota);
              }
              $lokasi = implode(",", $lokasi);
            ?>
            <p class="text-muted"><?php echo $lokasi; ?></p>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#login" data-toggle="tab" style="padding: 4px 20px;">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="#private" data-toggle="tab" style="padding: 4px 20px;">Data Pribadi</a></li>
              <li class="nav-item"><a class="nav-link" href="#family" data-toggle="tab" style="padding: 4px 20px;">Data Keluarga</a></li>
            </ul>
          </div><!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="login">
                <form id="formProfile" class="form-horizontal" action="<?php echo BASE_URL; ?>/controllers/C_profile.php?action=submit" method="POST">
                  <div class="card-body">
                    <div class="form-group row">
                      <label for="password1" class="col-sm-4 col-form-label">Kode Pegawai</label>
                      <div class="col-sm-8">
                        <input type="text" id="user_nopegawai" name="user_nopegawai" class="form-control"  value="<?php echo $user->user_nopegawai ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="userpegawai" class="col-sm-4 col-form-label">Nama Pegawai</label>
                      <div class="col-sm-8">
                          <input type="hidden" id="user_id" name="user_id" value="<?php echo $user->user_id ?>"/>
                        <input type="text" class="form-control" id="userpegawai" name="userpegawai" placeholder="Nama Pegawai" value="<?php echo $user->user_pegawai ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password1" class="col-sm-4 col-form-label">Nama User</label>
                      <div class="col-sm-8">
                        <input type="text" id="user_nama" name="user_nama" class="form-control"  value="<?php echo $user->user_nama ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password1" class="col-sm-4 col-form-label">Password</label>
                      <div class="col-sm-8">
                        <input type="password" class="form-control password" id="password1" name="password" placeholder="Password">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password2" class="col-sm-4 col-form-label">Re-type Password</label>
                      <div class="col-sm-8">
                        <input type="password" class="form-control password" id="password2" placeholder="Password">
                      </div>
                    </div>
                  </div>
                  <div class="footer">
                    <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                  </div>
                </form>
              </div>
              <div class="tab-pane" id="private">
                <form id="formPrivacy" class="form-horizontal" action="<?php echo BASE_URL; ?>/controllers/C_profile.php?action=submitprivacy" method="POST">
                  <div class="card-body">
                    <div class="form-group row">
                      <label for="password1" class="col-sm-4 col-form-label">Kode Pegawai</label>
                      <div class="col-sm-8">
                        <input type="text" id="user_nopegawai" name="user_nopegawai" class="form-control"  value="<?php echo $user->user_nopegawai ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_nama" class="col-sm-4 col-form-label">Nama Pegawai</label>
                      <div class="col-sm-8">
                        <input type="hidden" id="pegawai_id" name="pegawai_id" value="<?php echo $data["pegawai"]->pegawai_id ?>"/>
                        <input type="text" class="form-control" id="pegawai_nama" name="pegawai_nama" placeholder="Nama Pegawai" 
                        value="<?php echo $data["pegawai"]->pegawai_nama ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_alias" class="col-sm-4 col-form-label">Nama Alias</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_alias" name="pegawai_alias" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_alias ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="m_unit_id" class="col-sm-4 col-form-label">Unit</label>
                      <div class="col-sm-8">
                        <select id="m_unit_id" name="m_unit_id" style="width: 100%;"></select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_alamat" class="col-sm-4 col-form-label">Alamat</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_alamat" name="pegawai_alamat" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_alamat ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_alamat" class="col-sm-4 col-form-label">RT/RW</label>
                      <div class="col-sm-2">
                        <!-- <input type="text" id="pegawai_rtrw" name="pegawai_rtrw" class="form-control" value="" data-inputmask-inputformat="000/000" data-mask> -->
                        <input type="text"  id="pegawai_rtrw" name="pegawai_rtrw" value="<?php echo $data["pegawai"]->pegawai_rt ." / " . $data["pegawai"]->pegawai_rw ?>"
                        class="form-control" data-inputmask='"mask": "999/999"' data-mask>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_kode_pos" class="col-sm-4 col-form-label">Kode Pos</label>
                      <div class="col-sm-2">
                        <input type="text" class="form-control" id="pegawai_kode_pos" name="pegawai_kode_pos" value="<?php echo $data['pegawai']->pegawai_kode_pos ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="m_kelurahan_id" class="col-sm-4 col-form-label">Regional</label>
                      <div class="col-sm-8">
                        <select id="m_kelurahan_id" name="m_kelurahan_id" style="width: 100%;"></select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_telp" class="col-sm-4 col-form-label">Telp.</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_telp" name="pegawai_telp" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_telp ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_email" class="col-sm-4 col-form-label">Email</label>
                      <div class="col-sm-8">
                        <input type="email" id="pegawai_email" name="pegawai_email" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_email ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_nokk" class="col-sm-4 col-form-label">No. KK</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_nokk" name="pegawai_nokk" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_nokk ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_noid" class="col-sm-4 col-form-label">No. KTP</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_noid" name="pegawai_noid" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_noid ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_kartubpjs" class="col-sm-4 col-form-label">No. BPJS</label>
                      <div class="col-sm-8">
                        <input type="text" id="pegawai_kartubpjs" name="pegawai_kartubpjs" class="form-control"  value="<?php echo $data["pegawai"]->pegawai_kartubpjs ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_tgllahir" class="col-sm-4 col-form-label">Tgl. Lahir</label>
                      <div class="col-sm-8">
                        <?php
                          $pegawai_tgllahir = $data["pegawai"]->pegawai_tgllahir <> '' ? date('d-m-Y', strtotime($data["pegawai"]->pegawai_tgllahir)) : '00-00-0000';
                        ?>
                        <input type="text" id="pegawai_tgllahir" name="pegawai_tgllahir" class="form-control" value="<?php echo $pegawai_tgllahir ?>"
                        data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="m_kotalahir_id" class="col-sm-4 col-form-label">Tempat Lahir</label>
                      <div class="col-sm-8">
                        <select id="m_kotalahir_id" name="m_kotalahir_id" style="width: 100%;"></select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pegawai_kelamin" class="col-sm-4 col-form-label">Jenis Kelamin</label>
                      <div class="col-sm-8">
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="Laki-laki" name="pegawai_kelamin" value="L" <?php echo $data["pegawai"]->pegawai_kelamin == 'L' ? 'checked' : '' ?>>
                            <label for="Laki-laki"></label>Laki-laki&nbsp;
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="Perempuan" name="pegawai_kelamin" value="P" <?php echo $data["pegawai"]->pegawai_kelamin == 'P' ? 'checked' : '' ?>>
                            <label for="Perempuan"></label>Perempuan
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    
                  </div>
                  <div class="footer">
                    <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                  </div>
                </form>
              </div>
              <div class="tab-pane" id="family">
                <div id="family-form" class="col-md-12">
                </div>
                <button type="button" id="addkeluarga" class="btn btn-success btn-sm float-right">Tambah Baru</button>
              </div>
            </div>
            <!-- /.tab-content -->
          </div><!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
      <!-- /.col -->
    </div>
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <form id="photoForm">
              <div class="form-group" style="padding:10px;">
                <label for="photoprofile">Ganti Photo Profile</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input type="hidden" name="pegawai_id" value="<?php echo $data["pegawai"]->pegawai_id ?>">
                    <input type="file" class="custom-file-input" id="photoprofile" name="photoprofile">
                    <label class="custom-file-label" for="photoprofile">Choose file</label>
                  </div>
                  <div class="input-group-append">
                    <button type="submit" class="input-group-text" style="z-index: 100;">Upload</button type="submit">
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<script>
    var indexklg = 0;
    $(function(){
        $('[data-mask]').inputmask();
        var data = [];
        $('#m_unit_id').select2({data:data});

        $.ajax({
          url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getunit',
          dataType: 'json',
          type: "GET",
          success:function(result) {
            $('#m_unit_id').select2({
              data: result
            });
            $("#m_unit_id").data('select2').trigger('select', {
                data: {"id": "<?php echo $data["pegawai"]->m_unit_id ?>", "text": "<?php echo $data["pegawai"]->unit_nama ?>" }
            });
          }
        });

        $("#m_kelurahan_id").select2({
          ajax: {
            url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getregional',
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

        $("#m_kotalahir_id").select2({
          ajax: {
            url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getkota',
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
        
        $("#m_kelurahan_id").data('select2').trigger('select', {
            data: {"id": "<?php echo $data["pegawai"]->m_kelurahan_id ?>", "text": "<?php echo $data["pegawai"]->regional ?>" }
        });

        $("#m_kotalahir_id").data('select2').trigger('select', {
            data: {"id": "<?php echo $data["pegawai"]->m_kotalahir_id ?>", "text": "<?php echo $data["pegawai"]->kotalahir_nama ?>" }
        });

        $(".password").on("keyup", function () {
            if($(this).val() !== '') {
                $(".password").removeClass("is-invalid");
            }
            if($("#password1").val() !== $("#password2").val()) {
                $(".password").addClass("is-invalid");
            }
        });

        $("#formProfile").submit(function(event){
            if($("#password1").val() == '' || $("#password2").val() == '') {
                $(".password").addClass("is-invalid");
                return false;
            }
            if($("#password1").val() !== $("#password2").val()) {
                $(".password").addClass("is-invalid");
                return false;
            }

            event.preventDefault();  
            $.ajax({
                url:$(this).attr("action"),
                data:$(this).serialize(),
                type:$(this).attr("method"),
                dataType: 'html',
                success:function(hasil) {
                    $(".profile-username").html($("#user_nama").val());
                    hasil = parseInt(hasil);
                    if(hasil == 200) {
                      swal("Info!", "User " + $("#userpegawai").val() + " Berhasil disimpan", "success");
                    } if(hasil == 203) {
                      swal("Info!", "User " + $("#userpegawai").val() + " gagal disimpan, Nama User sudah ada yang memakai", "success");
                    } else if(hasil == 202) {
                      swal("Info!", "User " + $("#userpegawai").val() + " Gagal disimpan", "error");
                    }
                }
            })
            return false;
        });

        $("#formPrivacy").submit(function(event){
            event.preventDefault();  
            $.ajax({
                url:$(this).attr("action"),
                data:$(this).serialize(),
                type:$(this).attr("method"),
                dataType: 'html',
                success:function(hasil) {
                    hasil = parseInt(hasil);
                    if(hasil == 200) {
                      swal("Info!", "User " + $("#userpegawai").val() + " Berhasil disimpan", "success");
                    } if(hasil == 203) {
                      swal("Info!", "User " + $("#userpegawai").val() + " gagal disimpan, Nama User sudah ada yang memakai", "success");
                    } else if(hasil == 202) {
                      swal("Info!", "User " + $("#userpegawai").val() + " Gagal disimpan", "error");
                    }
                }
            })
            return false;
        });

        $.ajax({
          url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getkeluarga',
          dataType: 'json',
          type: "POST",
          data : {
            pegawai_id : $("#pegawai_id").val()
          },
          success:function(result) {
            
            result.forEach(function (elem, index) {
              var form = '';
              form += '<div class="card card-primary">';
              form += '<form id="formkeluarga-'+index+'" role="form">';
              form += '<div class="card-body">';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_nama-'+index+'">Nama</label>';
              form += '<input type="hidden" name="index"  value="'+index+'">';
              form += '<input type="hidden" id="pegkeluarga_id-'+index+'" name="pegkeluarga_id-'+index+'"  value="'+elem.pegkeluarga_id+'">';
              form += '<input type="text" class="form-control" id="pegkeluarga_nama-'+index+'" name="pegkeluarga_nama-'+index+'" value="'+elem.pegkeluarga_nama+'">';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label>Hubungan Keluarga</label>';
              form += '<select class="hubkeluarga" name="pegkeluarga_hub" style="width: 100%;">';
              var Suami = '';
              var Istri = '';
              var Anak = '';
              if (elem.pegkeluarga_hub == 'Suami') {
                  Suami = 'selected';
              } else if (elem.pegkeluarga_hub == 'Istri') {
                  Istri = 'selected';
              } else if (elem.pegkeluarga_hub == 'Anak') {
                Anak = 'selected';
              }
              form += '<option value="Suami" '+Suami+'>Suami</option>';
              form += '<option value="Istri" '+Istri+'>Istri</option>';
              form += '<option value="Anak" '+Anak+'>Anak</option>';
              form += '</select>';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_kartubpjs-'+index+'">No. BPJS</label>';
              form += '<input type="text" class="form-control" id="pegkeluarga_kartubpjs-'+index+'" name="pegkeluarga_kartubpjs-'+index+'" value="'+elem.pegkeluarga_kartubpjs+'">';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_kartubpjstk-'+index+'">No. BPJS Tenaga Kerja</label>';
              form += '<input type="text" class="form-control" id="pegkeluarga_kartubpjstk-'+index+'" name="pegkeluarga_kartubpjstk-'+index+'" value="'+elem.pegkeluarga_kartubpjstk+'">';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_nokk-'+index+'">No. KK</label>';
              form += '<input type="text" class="form-control" id="pegkeluarga_nokk-'+index+'" name="pegkeluarga_nokk-'+index+'" value="'+elem.pegkeluarga_nokk+'">';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_noid-'+index+'">No. KTP</label>';
              form += '<input type="text" class="form-control" id="pegkeluarga_noid-'+index+'" name="pegkeluarga_noid-'+index+'" value="'+elem.pegkeluarga_noid+'">';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="m_kotalahir_id-'+index+'">Kota</label>';
              form += '<select type="text" class="kotalahir" id="m_kotalahir_id-'+index+'" name="m_kotalahir_id-'+index+'" style="width: 100%;"></select>';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_tgllahir-'+index+'">Tgl. Lahir</label>';
              var pegkeluarga_tgllahir = elem.pegkeluarga_tgllahir != '' ? moment(elem.pegkeluarga_tgllahir, 'YYYY-MM-DD').format('DD-MM-YYYY') : '00-00-0000';
              form += '<input type="text" class="form-control tgllahir" id="pegkeluarga_tgllahir-'+index+'" name="pegkeluarga_tgllahir-'+index+'" value="'+pegkeluarga_tgllahir+'" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>';
              form += '</div>';
              form += '<div class="form-group">';
              form += '<label for="pegkeluarga_tgllahir-'+index+'">Jenis Kelamin</label>';
              form += '<div class="form-group clearfix">';
              form += '<div class="icheck-primary d-inline">';
              var L = "";
              var P = "";
              if (elem.pegkeluarga_kelamin == 'L') {
                L = "checked";
              } else {
                P = "checked";
              }
              form += '<input type="radio" id="pegkeluarga_kelamin-'+index+'-L" name="pegkeluarga_kelamin-'+index+'" '+ L +' value="L">';
              form += '<label for="pegkeluarga_kelamin-'+index+'"></label>Laki-laki&nbsp;&nbsp;';
              form += '</div>';
              form += '<div class="icheck-primary d-inline">';
              form += '<input type="radio" id="pegkeluarga_kelamin-'+index+'-P" name="pegkeluarga_kelamin-'+index+'" '+ P +' value="P">';
              form += '<label for="pegkeluarga_kelamin-'+index+'"></label>Perempuan';
              form += '</label>';
              form += '</div>';
              form += '</div>';
              form += '</div>';
              form += '</div>';
              form += '<div class="card-footer">';
              form += '<button type="submit" class="btn btn-primary btn-sm float-right">Submit</button>';
              form += '</div>';
              form += '</form>';
              form += '</div>';
              $("#family-form").append(form);
              indexklg = index;
            });

            $('.tgllahir').inputmask();
            $('.hubkeluarga').select2();
            $(".kotalahir").select2({
              ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getkota',
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

            result.forEach(function (elem, index) {
                $("#m_kotalahir_id-"+index).data('select2').trigger('select', {
                    data: {"id": elem.m_kotalahir_id, "text": elem.m_kotalahir_nama}
                });
                $("#formkeluarga-"+index).submit(function(event){
                    event.preventDefault();  
                    $.ajax({
                        url: '<?php echo BASE_URL; ?>/controllers/C_profile.php?action=submitkeluarga',
                        data:$(this).serialize(),
                        type:'POST',
                        dataType: 'html',
                        success:function(hasil) {
                            hasil = parseInt(hasil);
                            if(hasil == 200) {
                              swal("Info!", "Anggota keluarga " + $("#userpegawai").val() + " Berhasil disimpan", "success");
                            } if(hasil == 203) {
                              swal("Info!", "Anggota keluarga " + $("#userpegawai").val() + " gagal disimpan, Nama User sudah ada yang memakai", "success");
                            } else if(hasil == 202) {
                              swal("Info!", "Anggota keluarga " + $("#userpegawai").val() + " Gagal disimpan", "error");
                            }
                        }
                    });
                    return false;
                });
            });
          }
        });

        $("#addkeluarga").on("click", function (params) {
            var index = parseInt(indexklg)+1;
            var form = '';
            form += '<div class="card card-primary">';
            form += '<form id="formkeluarga-'+index+'" role="form">';
            form += '<div class="card-body">';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_nama-'+index+'">Nama</label>';
            form += '<input type="hidden" name="index"  value="'+index+'">';
            form += '<input type="hidden" id="pegkeluarga_id-'+index+'" name="pegkeluarga_id-'+index+'"  value="0">';
            form += '<input type="hidden" id="m_pegawai_id-'+index+'" name="m_pegawai_id-'+index+'"  value="'+$("#pegawai_id").val()+'">';
            form += '<input type="text" class="form-control" id="pegkeluarga_nama-'+index+'" name="pegkeluarga_nama-'+index+'" value="">';
            form += '</div>';
            form += '<div class="form-group">';
            var Suami = '';
            var Istri = '';
            var Anak = '';
            if (elem.pegkeluarga_hub == 'Suami') {
                Suami = 'selected';
            } else if (elem.pegkeluarga_hub == 'Istri') {
                Istri = 'selected';
            } else if (elem.pegkeluarga_hub == 'Anak') {
                Anak = 'selected';
            }
            form += '<label>Hubungan Keluarga</label>';
            form += '<select class="hubkeluarga" name="pegkeluarga_hub" style="width: 100%;">';
            form += '<option value="Suami" '+Suami+'>Suami</option>';
            form += '<option value="Istri" '+Istri+'>Istri</option>';
            form += '<option value="Anak" '+Anak+'>Anak</option>';
            form += '</select>';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_kartubpjs-'+index+'">No. BPJS</label>';
            form += '<input type="text" class="form-control" id="pegkeluarga_kartubpjs-'+index+'" name="pegkeluarga_kartubpjs-'+index+'" value="">';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_kartubpjstk-'+index+'">No. BPJS Tenaga Kerja</label>';
            form += '<input type="text" class="form-control" id="pegkeluarga_kartubpjstk-'+index+'" name="pegkeluarga_kartubpjstk-'+index+'" value="">';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_nokk-'+index+'">No. KK</label>';
            form += '<input type="text" class="form-control" id="pegkeluarga_nokk-'+index+'" name="pegkeluarga_nokk-'+index+'" value="">';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_noid-'+index+'">No. KTP</label>';
            form += '<input type="text" class="form-control" id="pegkeluarga_noid-'+index+'" name="pegkeluarga_noid-'+index+'" value="">';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="m_kotalahir_id-'+index+'">Kota</label>';
            form += '<select type="text" class="kotalahir" id="m_kotalahir_id-'+index+'" name="m_kotalahir_id-'+index+'" style="width: 100%;"></select>';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_tgllahir-'+index+'">Tgl. Lahir</label>';
            form += '<input type="text" class="form-control tgllahir" id="pegkeluarga_tgllahir-'+index+'" name="pegkeluarga_tgllahir-'+index+'"  data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>';
            form += '</div>';
            form += '<div class="form-group">';
            form += '<label for="pegkeluarga_kelamin-'+index+'">Jenis Kelamin</label>';
            form += '<div class="form-group clearfix">';
            form += '<div class="icheck-primary d-inline">';
            var L = "checked";
            var P = "";
            form += '<input type="radio" id="pegkeluarga_kelamin-'+index+'-L" name="pegkeluarga_kelamin-'+index+'" '+ L +' value="L">';
            form += '<label for="pegkeluarga_kelamin-'+index+'"></label>Laki-laki&nbsp;&nbsp;';
            form += '</div>';
            form += '<div class="icheck-primary d-inline">';
            form += '<input type="radio" id="pegkeluarga_kelamin-'+index+'-P" name="pegkeluarga_kelamin-'+index+'" '+ P +' value="P">';
            form += '<label for="pegkeluarga_kelamin-'+index+'"></label>Perempuan';
            form += '</label>';
            form += '</div>';
            form += '</div>';
            form += '</div>';
            form += '</div>';
            form += '<div class="card-footer">';
            form += '<button type="submit" class="btn btn-primary btn-sm float-right">Submit</button>';
            form += '</div>';
            form += '</form>';
            form += '</div>';
            $("#family-form").append(form);
            $('#formkeluarga-'+index).submit(function(event){
                event.preventDefault();  
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/controllers/C_profile.php?action=submitkeluarga',
                    data:$(this).serialize(),
                    type:'POST',
                    dataType: 'html',
                    success:function(hasil) {
                        hasil = parseInt(hasil);
                        if(hasil > 0) {
                          $("#pegkeluarga_id-"+index).val(hasil);
                          swal("Info!", "Anggota keluarga " + $("#userpegawai").val() + " Berhasil disimpan", "success");
                        } else {
                          swal("Info!", "Anggota keluarga " + $("#userpegawai").val() + " Gagal disimpan", "error");
                        }
                    }
                });
                return false;
            });
            $('.tgllahir').inputmask();
            $('.hubkeluarga').select2();
            $("#m_kotalahir_id-"+index).select2({
              ajax: {
                url: '<?php echo BASE_URL ?>/controllers/C_profile.php?action=getkota',
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
        });

        $('#photoForm').on('submit', function(event){
            event.preventDefault();
            $.ajax({  
                    url: "<?php echo BASE_URL ?>/controllers/C_profile.php?action=uploadphoto",
                    method:"POST",
                    data:new FormData(this),
                    contentType:false,
                    processData:false,
                    async: true,
                    success:function(result){
                        if(result == 200){
                          location.reload();
                        } else {
                            swal("Warning!", "Upload photo gagal", "warning");
                        }
                    }  
            });
        });
    });

</script>