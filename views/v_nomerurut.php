<style>
    html, body {
        width : 100%;
        height : 100%;
    }

    .urutan.panel.panel-default {
        padding : 5px;
    }

    .center {
        margin: 0 auto;
        width: 80%;
    }

    .section .section-center {
        top: 45%;
    }
</style>
<?php
    if ($dataparse != 'null') {
        $data = json_decode($dataparse);
        $urutan = str_pad($data->bookinghosp_urutan, 3, "0", STR_PAD_LEFT);
    }
    
?>
<body>
    <div id="booking" class="section">
		<div class="section-center">
			<div class="container">
                <div class="row">
                    <div class="center">
                        <div class="col-xs-12 col-sm-1">
                            
                        </div>
                        <div class="col-xs-12 col-sm-10" style="padding:0;">
                            <?php
                                if($dataparse != 'null'){
                            ?>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td bgcolor="#ffffff" align="left" style="padding: 20px 0px 0px 0px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                                <p style="margin: 0;">
                                                <p>Yth. <b><?php echo $data->pasien_nama ?></b></p>
                                                <p>Anda telah berhasil mendaftar pada antrian TELEMEDICINE, pada tangal <b><?php echo date("d-m-Y", strtotime($data->bookinghosp_tanggal)) ?></b></p>
                                                <center>No. Antrian :</center>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <center>
                                                <span style="font-size: 80px; line-height : 1;">
                                                    <?php echo $urutan; ?>
                                                </span>
                                            </center>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                            <p>Silahkan lakukan pembayaran sebesar Rp. <?php echo number_format($data->total) ?>. Dengan cara transfer ke rekening : <br><b>BANK BNI Syariah No. Rekening 888-999-8949-70</b>.</p>
                                            <?php
                                                $time = strtotime($data->bookinghosp_created_date) + 7200; // Add 1 hour
                                                $time = date('H:i:s', $time);
                                            ?>
                                            <p align="center">Pembayaran akan berakhir 2 jam setelah pendaftaran. pada pukul <?php echo $time ?> :</p>
                                            <center>
                                                <span id="timer" style="font-size: 80px; line-height : 1;">
                                                    00:00:00
                                                </span>
                                            </center>
                                        </td>
                                    </tr>
                                </table>
                            <?php } else { ?>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td bgcolor="#ffffff" align="left" style="padding: 20px 0px 0px 0px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                                <p><b>Mohon Maaf.</b></p>
                                                <p>No. Rekam Medik anda tidak terdaftar pada antrian kami atau sudah expired, coba cek kembali No. Rekam Medik anda</p>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-sm-1">
                            
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="center">
                        <div class="col-xs-12 col-sm-3">
                            
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="urutan panel panel-default">
                                <div class="booking-form">
                                    <div class="form-btn">
                                        <button class="submit-btn" onclick="window.location.href='<?php echo BASE_URL ?>'">KEMBALI</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    Date.prototype.addHours= function(h){
        this.setHours(this.getHours()+h);
        return this;
    }
    // Set the date we're counting down to
    var daftartime = new Date("<?php echo $data->bookinghosp_created_date ?>").getTime();
    
    // // Update the count down every 1 second
    var x = setInterval(function() {
        var now = new Date().getTime();
        var selisihwaktu = now - daftartime;
        
        var akhirwaktu = new Date(daftartime).addHours(2).getTime();
        var selisihwaktudaftar = akhirwaktu - now;

        var hoursselisih = Math.floor((selisihwaktudaftar % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var str = "" + hoursselisih;
        var pad = "00"
        var ansh = pad.substring(0, pad.length - str.length) + str

        var minutesselisih = Math.floor((selisihwaktudaftar % (1000 * 60 * 60)) / (1000 * 60));
        var str = "" + minutesselisih;
        var pad = "00"
        var ansm = pad.substring(0, pad.length - str.length) + str

        var secondsselisih = Math.floor((selisihwaktudaftar % (1000 * 60)) / 1000);
        var str = "" + secondsselisih;
        var pad = "00"
        var anss = pad.substring(0, pad.length - str.length) + str

        document.getElementById("timer").innerHTML = ansh + ":" + ansm + ":" + anss;
        
        if (selisihwaktudaftar < 60) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "EXPIRED";

            $.ajax({
                url: baseUrl + "/controllers/C_booking.php?action=expiredbayar",
                type: "post",
                data: {
                    pasien_norm : '<?php echo $data->pasien_norm ?>'
                },
                success : function (data) {
                    console.log(data);
                }
            });
        }
    }, 1000);
</script>