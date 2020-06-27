<style>
  @media (min-width: 750px) {
    .summary-profile {
      font-size: 13px;
    }
  }
</style>
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
              ?>
              <img class="profile-user-img img-fluid img-circle" src="<?php echo $imageprofile; ?>" alt="User profile picture">
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
                        <!-- <input type="text" id="m_unit_id" name="m_unit_id" class="form-control"  value="<?php //echo $data["pegawai"]->m_unit_id ?>"> -->
                        <select id="m_unit_id" name="m_unit_id" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;">
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="footer">
                    <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
            <!-- /.tab-content -->
          </div><!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<script>
    $(function(){
        $('.select2').select2();
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
    });
</script>