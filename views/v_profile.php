<section class="content">
    <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Profile Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="formProfile" class="form-horizontal" action="<?php echo BASE_URL; ?>/controllers/C_profile.php?action=submit" method="POST">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="userpegawai" class="col-sm-4 col-form-label">Nama Pegawai</label>
                    <div class="col-sm-8">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $user->user_id ?>"/>
                      <input type="text" class="form-control" id="userpegawai" name="userpegawai" placeholder="Nama Pegawai" value="<?php echo $user->user_pegawai ?>">
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
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                </div>
                <!-- /.card-footer -->
              </form>
            </div>
          </div>
        </div>
    </div>
</section>
<script>
    $(function(){
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
                    if(hasil == 200) {
                        swal("Info!", "User " + $("#userpegawai").val() + " Berhasil disimpan", "success");
                    } else {
                        swal("Info!", "User " + $("#userpegawai").val() + " Gagal disimpan", "error");
                    }
                }
            })
            return false;
        });
    });
</script>