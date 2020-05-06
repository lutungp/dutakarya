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
    $data = $_GET;
?>
<body>
    <div id="booking" class="section">
		<div class="section-center">
			<div class="container">
                <div class="row">
                    <div class="center">
                        <div class="col-xs-12 col-sm-3">
                            
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                <tr>
                                    <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        <p style="margin: 0;">
                                            <p>Yth. <?php echo $data["pasien_nama"] ?></p>
                                            <p>Anda telah berhasil mendaftar pada antrian POLI UMUM</p>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            
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
                                        <button class="submit-btn">KEMBALI</button>
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