<style>
  #readgaji {
    display : none;
  }
  .gaji-table {
    width: 100%;
    border : 1px solid #00a5a5;
    color : #00a5a5;
    margin-bottom: 10px !important;
  }
  .gaji-table > tbody > tr > td{
    padding : 5px 10px;
    border : 1px solid #00a5a5;
    color : #0b4848;
    font-size: 14px;
  }
  .title-column {
    text-align: left;
    border: none !important;
  }
  .price-column {
    text-align: right;
    border-left: none !important;
    border-top: none !important;
    border-bottom: none !important;
  }

  .mailbox {
    padding: 2px;
  }

  .terima {
    border-top : 1px solid #00a5a5;
    background-color: #e9e1f5;
  }

  @media only screen and (max-width: 500px) {
    .gaji-table > tbody > tr > td{
      font-size: 12px;
      padding : 5px 10px;
      border : none;
      color : #0b4848;
      vertical-align: top;
    }

    .card-body.p-0 .table tbody>tr>td:first-of-type, .card-body.p-0 .table tbody>tr>th:first-of-type, .card-body.p-0 .table thead>tr>td:first-of-type, .card-body.p-0 .table thead>tr>th:first-of-type {
      padding: 12px;
    }

    .mailbox-name {
      font-size: 12px;
      padding: 4px !important;
    }
    .mailbox-date {
      font-size: 12px;
      padding: 4px !important;
    }

    .card-body.p-0 .table tbody>tr>td:last-of-type, .card-body.p-0 .table tbody>tr>th:last-of-type, .card-body.p-0 .table thead>tr>td:last-of-type, .card-body.p-0 .table thead>tr>th:last-of-type {
        padding: 5px;
    }
  }

  @media only screen and (min-width: 400px) {
      .gaji-table-center {
        text-align: center;
      }
      .gaji-table.total {
        width : 50%;
        display: inline-table;
      }
  }
  
</style>
<section class="content">
      <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
          <div id="listgaji" class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Inbox</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Search Mail">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></button>
                <div class="float-right">
                <span>1-50/200</span>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm" onclick="chevronLeft()"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm" onclick="chevronRight()"><i class="fas fa-chevron-right"></i></button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
              <div class="table-responsive mailbox-messages">
                <table id="mailbox" class="table table-hover table-striped">
                  <tbody>
                    
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
              <div class="mailbox-controls">
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></button>
                <div class="float-right">
                  <span>1-50/200</span>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm" onclick="chevronLeft()"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm" onclick="chevronRight()"><i class="fas fa-chevron-right"></i></button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
            </div>
          </div>

          <div id="readgaji" class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Delete"
                  onclick="listGaji()">
                  <i class="fas fa-list-ol"></i>
                </button>&nbsp;&nbsp;&nbsp;List Gaji
              </h3>

              <div class="card-tools">
                <a href="#" class="btn btn-tool" data-toggle="tooltip" title="Previous"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="btn btn-tool" data-toggle="tooltip" title="Next"><i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-read-info">
                <input type="hidden" id="gajih_id"/>
                <h5>Gaji Karyawan </h5>
                <h6>Dari: Sdm Rumah Sakit Haji Jakarta
                  <span id="tglgajih" class="mailbox-read-time float-right"></span></h6>
              </div>
              <!-- /.mailbox-read-info -->
              <div class="mailbox-controls with-border text-center">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" title="pdf"
                  onclick="exportPdf2()">
                  <i class="far fa-file-pdf"></i>&nbsp;&nbsp;&nbsp;Export PDF
                </button>
              </div>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <table class="gaji-table">
                  <tr style="background-color: #e9e1f5;">
                    <td align="center" width="50%" colspan="2">GAJI DAN TUNJANGAN</td>
                    <td align="center" width="50%" colspan="2">INSENTIF</td>
                  </tr>
                  <tr>
                    <td class="title-column">GAJI RSHJ</td>
                    <td id="gajirshaji" class="price-column">-</td>
                    <td class="title-column">Jasa Yan</td>
                    <td id="jasayan" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Kehadiran</td>
                    <td id="kehadiran" class="price-column">-</td>
                    <td class="title-column">Shift</td>
                    <td id="shift" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Transport</td>
                    <td id="transport" class="price-column">-</td>
                    <td class="title-column">Lembur</td>
                    <td id="lembur" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Operasional</td>
                    <td id="operasional" class="price-column">-</td>
                    <td class="title-column">On Call</td>
                    <td id="oncall" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Rapel/MOD</td>
                    <td id="rapelmod" class="price-column">-</td>
                    <td class="title-column">Uang Cuti</td>
                    <td id="uangcuti" class="price-column">-</td>
                  </tr>
                </table>
                <table class="gaji-table">
                  <tr style="background-color: #e9e1f5;">
                    <td align="center" width="50%" colspan="4">POTONGAN KARYAWAN</td>
                  </tr>
                  <tr>
                    <td class="title-column">Simwa</td>
                    <td id="simwa" class="price-column">-</td>
                    <td class="title-column">BPJS TK</td>
                    <td id="bpjstk" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Koperasi</td>
                    <td id="koperasi" class="price-column">-</td>
                    <td class="title-column">BPJS Pensiun</td>
                    <td id="Pensiun" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Parkir</td>
                    <td id="parkir" class="price-column">-</td>
                    <td class="title-column">Pajak</td>
                    <td id="pajak" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">SIMPOK</td>
                    <td id="simpok" class="price-column">-</td>
                    <td class="title-column">Sekolah</td>
                    <td id="sekolah" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Kesehatan</td>
                    <td id="kesehatan" class="price-column">-</td>
                    <td class="title-column">Lain-lain</td>
                    <td id="lainlain" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Absensi</td>
                    <td id="absensi" class="price-column">-</td>
                    <td class="title-column">FKK</td>
                    <td id="fkk" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">ZIS</td>
                    <td id="zis" class="price-column">-</td>
                    <td class="title-column">IBI</td>
                    <td id="ibi" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Qurban</td>
                    <td id="qurban" class="price-column">-</td>
                    <td class="title-column">SP</td>
                    <td id="sp" class="price-column">-</td>
                  </tr>
                  <tr>
                    <td class="title-column">Infaq Masjid</td>
                    <td id="masjid" class="price-column">-</td>
                    <td class="title-column"></td>
                    <td class="price-column"></td>
                  </tr>
                </table>
                <div class="gaji-table-center">
                  <table class="gaji-table total">
                    <tr style="background-color: #e9e1f5;">
                      <td align="center" width="50%" colspan="2">TOTAL</td>
                    </tr>
                    <tr>
                      <td class="title-column">Gaji & Tunjangan </td>
                      <td id="totalgajitunj" class="price-column">-</td>
                    </tr>
                    <tr>
                      <td class="title-column">Insentif </td>
                      <td id="totalinsentif" class="price-column">-</td>
                    </tr>
                    <tr>
                      <td class="title-column">Potongan (-)</td>
                      <td id="totalpotongan" class="price-column">-</td>
                    </tr>
                    <tr class="terima">
                      <td class="title-column">Terima </td>
                      <td id="totalterima" class="price-column">-</td>
                    </tr>
                  </table>
                </div>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.card-body -->
            <!-- /.card-footer -->
            <div class="card-footer">
              
            </div>
            <!-- /.card-footer -->
          </div>
          
          <input type="hidden" id="page" value="1"/>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <script>
      $(document).ready(function(){
          getGajiKaryawan(1, 0, 50);
          $(".fa-sync-alt").on("click", function(){
              getGajiKaryawan($('#page').val(), 0, 50);
          });
      });

      var datagaji = [];
      function getGajiKaryawan(page, offset, limit) {
        $("#mailbox tbody").html('');
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_gajikaryawan.php?action=getgajikaryawan",
            type: "post",
            data: {
              page : page,
              offset : offset,
              limit : limit
            },
            success : function (res) {
                datagaji = JSON.parse(res);
                pages =  datagaji['num_rows'] < 50 ? 1 : datagaji['num_rows']/50;
                pages = pages % 1 === 0 ? pages : parseInt(pages)+1;
                $('#page').val(page);
                $(".float-right > span").html(page +" - "+ pages +" / "+ datagaji['num_rows']);
                var bulan = ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
                for (let index = 0; index < datagaji['gaji'].length; index++) {
                    const element = datagaji['gaji'][index];
                    var html = '<tr>';
                    var bintang_baca = '';
                    if (element.gaji_view_count < 1) {
                        bintang_baca = '<a href="#"><i class="fas fa-star text-warning"></i></a>';
                    }
                    html += '<td class="mailbox-star">'+bintang_baca+'</td>';
                    html += '<td class="mailbox-name"><a href="#" onClick="viewSlipGaji('+element.gaji_id+')">Pemberitahuan gaji</a> <br>Bulan '+bulan[element.gaji_bulan]+' '+ element.gaji_tahun+ '</td>';
                    html += '<td class="mailbox-date">'+moment(element.gaji_created_date, 'YYYY-MM-DD').format('DD-MM-YYYY')+'</td>';
                    html += '<td class="mailbox-subject" align="center">';
                    html += '<button type="button" class="btn btn-default btn-sm" onClick="viewSlipGaji('+element.gaji_id+')"><i class="far fa-sticky-note"></i></button>';
                    html += '<button type="button" class="btn btn-default btn-sm" onClick="exportPdf('+element.gaji_id+')"><i class="far fa-file-pdf"></i></button>';
                    html += '</td>';
                    html += '</tr>';
                    $("#mailbox tbody").append(html);
                }
            }
        });
      }

      function resetForm(){
        $("#gajirshaji").html('-');
        $("#jasayan").html('-');
        $("#kehadiran").html('-');
        $("#shift").html('-');
        $("#transport").html('-');
        $("#lembur").html('-');
        $("#operasional").html('-');
        $("#oncall").html('-');
        $("#rapelmod").html('-');
        $("#uangcuti").html('-');
        $("#simwa").html('-');
        $("#bpjstk").html('-');
        $("#koperasi").html('-');
        $("#Pensiun").html('-');
        $("#parkir").html('-');
        $("#pajak").html('-');
        $("#simpok").html('-');
        $("#sekolah").html('-');
        $("#kesehatan").html('-');
        $("#lainlain").html('-');
        $("#absensi").html('-');
        $("#fkk").html('-');
        $("#zis").html('-');
        $("#ibi").html('-');
        $("#qurban").html('-');
        $("#sp").html('-');
        $("#masjid").html('-');
      }

      function viewSlipGaji(gaji_id) {
          resetForm();
          $("#readgaji").css('display', 'block');
          $("#listgaji").css('display', 'none');
          $("#gajih_id").val(gaji_id);
          dataslipgaji = datagaji['gaji'].filter(p=>p.gaji_id==gaji_id);
          $("#tglgajih").html(moment(dataslipgaji[0].gaji_created_date, 'YYYY-MM-DD hh:mm:ss').format('DD-MM-YYYY hh:mm:ss'));
          var gaji_pokok  = parseFloat(dataslipgaji[0].gaji_pokok||0);
          $("#gajirshaji").html(gaji_pokok > 0 ? Number(gaji_pokok).toLocaleString() : '-');
          
          var gaji_jasalayanan  = parseFloat(dataslipgaji[0].gaji_jasalayanan||0);
          $("#jasayan").html(gaji_jasalayanan > 0 ? Number(gaji_jasalayanan).toLocaleString() : '-');

          $("#kehadiran").html(dataslipgaji[0].gaji_kehadiran);

          var gaji_shift  = parseFloat(dataslipgaji[0].gaji_shift||0);
          $("#shift").html(gaji_shift > 0 ? Number(gaji_shift).toLocaleString() : '-');

          var gaji_transport  = parseFloat(dataslipgaji[0].gaji_transport||0);
          $("#transport").html(gaji_transport > 0 ? Number(gaji_transport).toLocaleString() : '-');

          var gaji_lembur  = parseFloat(dataslipgaji[0].gaji_lembur||0);
          $("#lembur").html(gaji_lembur > 0 ? Number(gaji_lembur).toLocaleString() : '-');

          var gaji_operasional  = parseFloat(dataslipgaji[0].gaji_operasional||0);
          $("#operasional").html(gaji_operasional > 0 ? Number(gaji_operasional).toLocaleString() : '-');

          var gaji_oncall  = parseFloat(dataslipgaji[0].gaji_oncall||0);
          $("#oncall").html(gaji_oncall > 0 ? Number(gaji_oncall).toLocaleString() : '-');

          var gaji_rapel  = parseFloat(dataslipgaji[0].gaji_rapel||0);
          $("#rapelmod").html(gaji_rapel > 0 ? Number(gaji_rapel).toLocaleString() : '-');

          var gaji_uangcuti  = parseFloat(dataslipgaji[0].gaji_uangcuti||0);
          $("#uangcuti").html(gaji_uangcuti > 0 ? Number(gaji_uangcuti).toLocaleString() : '-');
          
          var gaji_potsimwa  = parseFloat(dataslipgaji[0].gaji_potsimwa||0);
          $("#simwa").html(gaji_potsimwa > 0 ? Number(gaji_potsimwa).toLocaleString() : '-');

          var gaji_potbpjstk  = parseFloat(dataslipgaji[0].gaji_potbpjstk||0);
          $("#bpjstk").html(gaji_potbpjstk > 0 ? Number(gaji_potbpjstk).toLocaleString() : '-');

          var gaji_potkoperasi  = parseFloat(dataslipgaji[0].gaji_potkoperasi||0);
          $("#koperasi").html(gaji_potkoperasi > 0 ? Number(gaji_potkoperasi).toLocaleString() : '-');

          var gaji_potbpjspensiun  = parseFloat(dataslipgaji[0].gaji_potbpjspensiun||0);
          $("#Pensiun").html(gaji_potbpjspensiun > 0 ? Number(gaji_potbpjspensiun).toLocaleString() : '-');

          var gaji_potparkir  = parseFloat(dataslipgaji[0].gaji_potparkir||0);
          $("#parkir").html(gaji_potparkir > 0 ? Number(gaji_potparkir).toLocaleString() : '-');

          var gaji_potpajak = parseFloat(dataslipgaji[0].gaji_potpajak||0);
          $("#pajak").html(gaji_potpajak > 0 ? Number(gaji_potpajak).toLocaleString() : '-');

          var gaji_potsimpok = parseFloat(dataslipgaji[0].gaji_potsimpok||0);
          $("#simpok").html(gaji_potsimpok > 0 ? Number(gaji_potsimpok).toLocaleString() : '-');

          var gaji_potsekolah = parseFloat(dataslipgaji[0].gaji_potsekolah||0);
          $("#sekolah").html(gaji_potsekolah > 0 ? Number(gaji_potsekolah).toLocaleString() : '-');

          var gaji_potkesehatan = parseFloat(dataslipgaji[0].gaji_potkesehatan||0);
          $("#kesehatan").html(gaji_potkesehatan > 0 ? Number(gaji_potkesehatan).toLocaleString() : '-');

          var gaji_potlain = parseFloat(dataslipgaji[0].gaji_potlain||0);
          $("#lainlain").html(gaji_potlain > 0 ? Number(gaji_potlain).toLocaleString() : '-');

          var gaji_potabsensi = parseFloat(dataslipgaji[0].gaji_potabsensi||0);
          $("#absensi").html(gaji_potabsensi > 0 ? Number(gaji_potabsensi).toLocaleString() : '-');

          var gaji_potfkk = parseFloat(dataslipgaji[0].gaji_potfkk||0);
          $("#fkk").html(gaji_potfkk > 0 ? Number(gaji_potfkk).toLocaleString() : '-');

          var gaji_potzis = parseFloat(dataslipgaji[0].gaji_potzis||0);
          $("#zis").html(gaji_potzis > 0 ? Number(gaji_potzis).toLocaleString() : '-');

          var gaji_potibi = parseFloat(dataslipgaji[0].gaji_potibi||0);
          $("#ibi").html(gaji_potibi > 0 ? Number(gaji_potibi).toLocaleString() : '-');

          var gaji_potqurban = parseFloat(dataslipgaji[0].gaji_potqurban||0);
          $("#qurban").html(gaji_potqurban > 0 ? Number(gaji_potqurban).toLocaleString() : '-');

          var gaji_potsp = parseFloat(dataslipgaji[0].gaji_potsp||0);
          $("#sp").html(gaji_potsp > 0 ? Number(gaji_potsp).toLocaleString() : '-');

          var gaji_potinfaqmasjid = parseFloat(dataslipgaji[0].gaji_potinfaqmasjid||0);
          $("#masjid").html(gaji_potinfaqmasjid > 0 ? Number(gaji_potinfaqmasjid).toLocaleString() : '-');

          var totalgajitunj = gaji_pokok + gaji_transport + gaji_operasional + gaji_rapel;
          $("#totalgajitunj").html(totalgajitunj > 0 ? Number(totalgajitunj).toLocaleString() : '-');

          var totalinsentif = gaji_jasalayanan + gaji_shift + gaji_lembur + gaji_oncall + gaji_uangcuti;
          $("#totalinsentif").html(totalinsentif > 0 ? Number(totalinsentif).toLocaleString() : '-');

          var totalpotongan =  gaji_potsimwa + gaji_potbpjstk + gaji_potkoperasi + gaji_potbpjspensiun + gaji_potparkir + gaji_potpajak + gaji_potsimpok + gaji_potsekolah + gaji_potkesehatan + gaji_potlain + gaji_potabsensi + gaji_potfkk + gaji_potzis + gaji_potibi + gaji_potqurban + gaji_potsp + gaji_potinfaqmasjid;
          $("#totalpotongan").html(totalpotongan > 0 ? "<b><font color=red>" + Number(totalpotongan).toLocaleString() + "</font></b>" : '-');

          var totalterima = totalgajitunj + totalinsentif - totalpotongan;
          $("#totalterima").html(totalterima > 0 ? "<b><font color=green>" + Number(totalterima).toLocaleString() + "</font></b>" : '-');

          $.get("<?php echo BASE_URL ?>/controllers/C_gajikaryawan.php?action=view&id="+gaji_id, function(data, status){
            console.log(status);
          });
      }

      function listGaji(gaji_id) {
          $("#readgaji").css('display', 'none');
          $("#listgaji").css('display', 'block');
          $("#gajih_id").val(0);
          getGajiKaryawan($('#page').val(), 0, 50);
          resetForm();
      }

      function exportPdf(gaji_id) {
        window.open('<?php echo BASE_URL;?>/controllers/C_gajikaryawan.php?action=exportpdf&id=' + gaji_id);
        $.get("<?php echo BASE_URL ?>/controllers/C_gajikaryawan.php?action=view&id="+gaji_id, function(data, status){
          console.log(status);
        });
      }

      function exportPdf2() {
        var gajih_id = $("#gajih_id").val();
        exportPdf(gajih_id);
      }

      function chevronLeft() {
        var pageprev = $('#page').val() - 1;
        if(pageprev >= 0) {
          getGajiKaryawan(pageprev, 0, 50);
        }
      }

      function chevronRight() {
        var pagenext = parseInt($('#page').val()) + 1;
        pages =  datagaji['num_rows'] < 50 ? 1 : datagaji['num_rows']/50;
        pages = pages % 1 === 0 ? pages : parseInt(pages)+1;
        if(pagenext<=pages){
          getGajiKaryawan(pagenext, 0, 50);
        }
      }
      
    </script>