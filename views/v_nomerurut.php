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
</style>
<?php
    $data = json_decode($dataparse);
    $urutan = str_pad($data->bookinghosp_urutan, 3, "0", STR_PAD_LEFT);
?>
<body>
    <div id="booking" class="section">
		<div class="section-center">
			<div class="container">
                <div class="row">
                    <div class="center">
                        <div class="col-xs-12 col-sm-2">
                            
                        </div>
                        <div class="col-xs-12 col-sm-8" style="padding:0;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                <tr>
                                    <td bgcolor="#ffffff" align="left" style="padding: 20px 0px 0px 0px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                            <p style="margin: 0;">
                                            <p>Yth. <b><?php echo $data->pasien_nama ?></b></p>
                                            <p>Anda telah berhasil mendaftar pada antrian POLI UMUM, <br>pada tangal <b><?php echo date("d-m-Y", strtotime($data->bookinghosp_tanggal)) ?></b></p>
                                            <center>No. Antrian :</center>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <center>
                                            <span style="font-size: 90px; line-height : 1;">
                                                <?php echo $urutan; ?>
                                            </span>
                                        </center>
                                        <br>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            
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