<style>
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
	-webkit-appearance: none;
		margin: 0;
	}

	html, body {
		height: 100%;
	}
	
	.layer-block {
		display : none;
		position: fixed;
		z-index : 100;
		background-color : #a5a5a566;
		width: 100%; height: 100%;
	}

	.lds-dual-ring {
		display: inline-block;
		width: 80px;
		height: 80px;
		position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
	}
	.lds-dual-ring:after {
		content: " ";
		display: block;
		width: 64px;
		height: 64px;
		margin: 8px;
		border-radius: 50%;
		border: 6px solid #fff;
		border-color: #fff transparent #fff transparent;
		animation: lds-dual-ring 1.2s linear infinite;
	}
	@keyframes lds-dual-ring {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}

	.square {
		margin-bottom : 5px;
		background: #00848a;
		width: 100px;
		height: 100px;
	}
	.square {
		font-size:18px;
		color: #fff;
		text-align : center;
		word-wrap: break-word;
		font-family: arial;
		display: flex;
		justify-content: center;
		align-items: center;
		background-size: 100% 200%;
		background-image: linear-gradient(to bottom, #00848a 50%, #ee4634 50%);
		transition: background-position 1s;
	}

	.square:hover {
		background-position: 0 -100%;
		cursor: pointer;
	}

	.row-btn-menu {
		text-align:center;
		padding : 2px;
		display: none;
	}

	.logo-rs-desktop {
		text-align: center;
	}

	.logo-rs-mobile {
		display: none;
	}

	.displaymode {
		display: block;
	}

	.mobilemode {
		display: none;
	}

	button.displaymode {
		border: 1px #dddee9 solid;
		color: #949496;
	}

	@media only screen and (max-width: 990px) {
		.buttonmenu {
			display: none;
		}

		.row-btn-menu {
			text-align:center;
			padding : 2px;
			display: block;
		}

		#booking {
			top: 20px !important;
		}

		.logo-rs-desktop {
			display: inline-block;
			display: none;
		}

		.logo-rs-mobile {
			display: block;
			text-align: center;
		}

		.displaymode {
			display: none;
		}

		.mobilemode {
			display: block;
		}
	}

	.noselect {
		-webkit-touch-callout: none; /* iOS Safari */
		-webkit-user-select: none; /* Safari */
		-khtml-user-select: none; /* Konqueror HTML */
		-moz-user-select: none; /* Old versions of Firefox */
		-ms-user-select: none; /* Internet Explorer/Edge */
		user-select: none; /* Non-prefixed version, currently
			supported by Chrome, Opera and Firefox */
	}

	.row-btn-menu {
		text-align:center;
		padding : 2px;
	}

	.modal-backdrop {
		display : none;
	}

	tr:hover {
		background-color: aliceblue;
	}

</style>
<body>
	<div id="loading" class="layer-block">
		<div class="lds-dual-ring"></div>
	</div>
	<div id="booking" class="section">
		<div class="section-center">
			<div class="container">
				<div class="row">
					<div class="col-md-2">
						<div class="logo-rs-desktop">
							<!-- C:\xampp\htdocs\telemedicineRSHJ\assets\img\rshaji.jpg -->
							<img src="<?php echo BASE_URL; ?>/assets/img/rshaji.jpg" alt="Rshaji-Jakarta-telemedicine" width="170" height="145"
							onclick="window.open('https://www.rshaji-jakarta.com/')">
							<p><font style="font-size: 18px; color: #346d00;">RS. HAJI JAKARTA</font><br>Pelayanan <font style="color: red;">via-Telepon</font></p>
						</div>
						<div class="logo-rs-mobile">
							<!-- C:\xampp\htdocs\telemedicineRSHJ\assets\img\rshaji.jpg -->
							<img src="<?php echo BASE_URL; ?>/assets/img/rshaji.jpg" alt="Rshaji-Jakarta-telemedicine" width="50" height="38"
							onclick="window.open('https://www.rshaji-jakarta.com/')">
							<p><font style="font-size: 18px; color: #346d00;">RS. HAJI JAKARTA</font><br>Pelayanan <font style="color: red;">COVID-19</font></p>
						</div>
					</div>
					<div class="col-md-8">
						<div class="booking-form">
							<form action="<?php echo BASE_URL; ?>/controllers/C_booking.php?action=create" method="post">
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group">
											<span class="form-label">Pilih Layanan</span>
											<div style="display: inline-flex; width: 100%;">
												<input class="form-control" name="m_layanan_kode" type="text" readonly required>
												<button type="button" class="displaymode" data-toggle="modal" data-target="#ModalLayanan">CARI</button>
												<button type="button" class="mobilemode" data-toggle="modal" data-target="#ModalLayanan"><i class="fa fa-search"></i></button>
											<div>
											<!-- <select id="cars" name="carlist" class="form-control">
												<option value="volvo">Volvo</option>
												<option value="saab">Saab</option>
												<option value="opel">Opel</option>
												<option value="audi">Audi</option>
											</select> -->
											<!-- <select class="form-control" name="m_layanan_kode" style="width: 100%;">
												<option value="RJ001">UMUM</option>
												<option value="FS001">REHABILITASI MEDIK</option>
												<option value="FS002">TERAPI WICARA</option>
												<option value="FS003">FISIOTERAPI</option>
												<option value="LAIN1">LAIN-LAIN</option>
												<option value="MCU01">MEDICAL CHECK UP</option>
												<option value="RJ002">BEDAH</option>
												<option value="RJ003">GIGI DAN MULUT</option>
												<option value="RJ004">GIZI</option>
												<option value="RJ005">JANTUNG</option>
												<option value="RJ006">KANDUNGAN & KEBIDANAN</option>
												<option value="RJ007">ANAK</option>
												<option value="RJ008">JIWA</option>
												<option value="RJ009">KULIT/KELAMIN</option>
												<option value="RJ010">MATA</option>
												<option value="RJ011">PARU</option>
												<option value="RJ012">PENYAKIT DALAM</option>
												<option value="RJ013">SYARAF</option>
												<option value="RJ014">THT</option>
												<option value="RJ015">AKUPUNKTUR</option>
												<option value="RJ016">ENDOSCOPY</option>
												<option value="RJ017">PERAWATAN LUKA</option>
												<option value="RJ018">ANASTESI</option>
												<option value="RJ019">EMERGENCY RB</option>
												<option value="RJ020">KLINIK UMMI</option>
												<option value="RJ021">KLINIK SAKINAH</option>
												<option value="RJ022">KLINIK HAJI DAN UMROH</option>
												<option value="RJ023">PSIKOLOG</option>
												<option value="PTK01">P2TKHU</option>
												<option value="VKS01">KLINIK VAKSIN INTERNASIONAL</option>
												<option value="DRS01">DARUSSALAM</option>
												<option value="RJ024">BEDAH PLASTIK</option>
											</select> -->
										</div>
									</div>
								</div>
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group">
											<span class="form-label">Pilih Dokter</span>
											<select class="form-control" name="m_layanan_kode" style="width: 100%;">
												<option value="RJ001">UMUM</option>
												<option value="FS001">REHABILITASI MEDIK</option>
												<option value="FS002">TERAPI WICARA</option>
												<option value="FS003">FISIOTERAPI</option>
												<option value="LAIN1">LAIN-LAIN</option>
												<option value="MCU01">MEDICAL CHECK UP</option>
												<option value="RJ002">BEDAH</option>
												<option value="RJ003">GIGI DAN MULUT</option>
												<option value="RJ004">GIZI</option>
												<option value="RJ005">JANTUNG</option>
												<option value="RJ006">KANDUNGAN & KEBIDANAN</option>
												<option value="RJ007">ANAK</option>
												<option value="RJ008">JIWA</option>
												<option value="RJ009">KULIT/KELAMIN</option>
												<option value="RJ010">MATA</option>
												<option value="RJ011">PARU</option>
												<option value="RJ012">PENYAKIT DALAM</option>
												<option value="RJ013">SYARAF</option>
												<option value="RJ014">THT</option>
												<option value="RJ015">AKUPUNKTUR</option>
												<option value="RJ016">ENDOSCOPY</option>
												<option value="RJ017">PERAWATAN LUKA</option>
												<option value="RJ018">ANASTESI</option>
												<option value="RJ019">EMERGENCY RB</option>
												<option value="RJ020">KLINIK UMMI</option>
												<option value="RJ021">KLINIK SAKINAH</option>
												<option value="RJ022">KLINIK HAJI DAN UMROH</option>
												<option value="RJ023">PSIKOLOG</option>
												<option value="PTK01">P2TKHU</option>
												<option value="VKS01">KLINIK VAKSIN INTERNASIONAL</option>
												<option value="DRS01">DARUSSALAM</option>
												<option value="RJ024">BEDAH PLASTIK</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group">
											<span class="form-label">No. RM (Rekam Medik)</span>
											<input class="form-control" name="pasien_norm" type="select" id="pasien_norm" onKeyUp="javascript:chkRM();" required>
										</div>
									</div>
								</div>
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group">
											<span class="form-label">Nama Pasien</span>
											<input class="form-control" name="pasien_nama" id="pasien_nama" type="text" required readonly>
										</div>
									</div>
								</div>
								<div class="row no-margin">
									<div class="col-sm-6">
										<div class="form-group">
											<span class="form-label">Pesan Tanggal</span>
											<input class="form-control" id="pesan_tanggal" name="pesan_tanggal" type="date" required>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<span class="form-label">Hari</span>
											<input class="form-control" id="pesan_hari" name="pesan_hari" type="text" placeholder="" readonly>		
										</div>
									</div>
								</div>
								<div class="form-group">
									<span class="form-label">Layanan</span>
									<input class="form-control" id="layanan_kode" name="layanan_kode" type="text" placeholder="" style="display: none;">
									<input class="form-control" id="layanan_nama" name="layanan_nama" type="text" placeholder="" readonly>
								</div>
								<div class="form-group">
									<span class="form-label">Email</span>
									<input class="form-control" name="pasien_email" type="email" placeholder="Enter your email">
								</div>
								<div class="form-group">
									<span class="form-label">Phone</span>
									<input class="form-control" name="pasien_tlp" type="tel" placeholder="Enter your phone number" required>
								</div>
								<div class="form-btn">
									<button class="submit-btn">PESAN</button>
								</div>
							</form>
						</div>	
					</div>
					<div class="col-md-2">
						<!-- <div class="buttonmenu noselect">
							<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_jadwaldokter.php';">
								JADWAL DOKTER
							</div>
							<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=list_antrian';">
								ANTRIAN
							</div>
							<div class="square" onclick="noUrut()">
								NOMER URUT
							</div>
						</div> -->
					</div>
				</div>
			</div>
			<!-- <div class="container">
				<div class="row-btn-menu">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0;">
						<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_jadwaldokter.php';">
							JADWAL DOKTER
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0;">
						<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=list_antrian';">
							ANTRIAN
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0;">
						<div class="square">
							NOMER URUT
						</div>
					</div>
				</div>
			</div> -->
		</div>
	</div>
	
	<div class="modal fade" id="ModalLayanan" tabindex="-1" role="dialog" aria-labelledby="ModalLayananLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLayananLabel">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class='table'>
					<thead>
						<tr>
							<th>No.</th>
							<th>Layanan</th>
						</tr>
					</thead>
					<tbody class="selecttable">
						<tr data-kode="RJ001" data-layanan="UMUM"><td>RJ001</td><td>UMUM</td></tr>
						<tr data-kode="FS001" data-layanan="REHABILITASI MEDIK"><td>FS001</td><td>REHABILITASI MEDIK</td></tr>
						<tr data-kode="FS002" data-layanan="TERAPI WICARA"><td>FS002</td><td>TERAPI WICARA</td></tr>
						<tr data-kode="FS003" data-layanan="FISIOTERAPI"><td>FS003</td><td>FISIOTERAPI</td></tr>
						<tr data-kode="LAIN1" data-layanan="LAIN-LAIN"><td>LAIN1</td><td>LAIN-LAIN</td></tr>
						<tr data-kode="MCU01" data-layanan="MEDICAL CHECK UP"><td>MCU01</td><td>MEDICAL CHECK UP</td></tr>
						<tr data-kode="RJ002" data-layanan="BEDAH"><td>RJ002</td><td>BEDAH</td></tr>
						<tr data-kode="RJ003" data-layanan="GIGI DAN MULUT"><td>RJ003</td><td>GIGI DAN MULUT</td></tr>
						<tr data-kode="RJ004" data-layanan="GIZI"><td>RJ004</td><td>GIZI</td></tr>
						<tr data-kode="RJ005" data-layanan="JANTUNG"><td>RJ005</td><td>JANTUNG</td></tr>
						<tr data-kode="RJ006" data-layanan="KANDUNGAN & KEBIDANAN"><td>RJ006</td><td>KANDUNGAN & KEBIDANAN</td></tr>
						<tr data-kode="RJ007" data-layanan="ANAK"><td>RJ007</td><td>ANAK</td></tr>
						<tr data-kode="RJ008" data-layanan="JIWA"><td>RJ008</td><td>JIWA</td></tr>
						<tr data-kode="RJ009" data-layanan="KULIT/KELAMIN"><td>RJ009</td><td>KULIT/KELAMIN</td></tr>
						<tr data-kode="RJ010" data-layanan="MATA"><td>RJ010</td><td>MATA</td></tr>
						<tr data-kode="RJ011" data-layanan="PARU"><td>RJ011</td><td>PARU</td></tr>
						<tr data-kode="RJ012" data-layanan="PENYAKIT DALAM"><td>RJ012</td><td>PENYAKIT DALAM</td></tr>
						<tr data-kode="RJ013" data-layanan="SYARAF"><td>RJ013</td><td>SYARAF</td></tr>
						<tr data-kode="RJ014" data-layanan="THT"><td>RJ014</td><td>THT</td></tr>
						<tr data-kode="RJ015" data-layanan="AKUPUNKTUR"><td>RJ015</td><td>AKUPUNKTUR</td></tr>
						<tr data-kode="RJ016" data-layanan="ENDOSCOPY"><td>RJ016</td><td>ENDOSCOPY</td></tr>
						<tr data-kode="RJ017" data-layanan="PERAWATAN LUKA"><td>RJ017</td><td>PERAWATAN LUKA</td></tr>
						<tr data-kode="RJ018" data-layanan="ANASTESI"><td>RJ018</td><td>ANASTESI</td></tr>
						<tr data-kode="RJ019" data-layanan="EMERGENCY RB"><td>RJ019</td><td>EMERGENCY RB</td></tr>
						<tr data-kode="RJ020" data-layanan="KLINIK UMMI"><td>RJ020</td><td>KLINIK UMMI</td></tr>
						<tr data-kode="RJ021" data-layanan="KLINIK SAKINAH"><td>RJ021</td><td>KLINIK SAKINAH</td></tr>
						<tr data-kode="RJ022" data-layanan="KLINIK HAJI DAN UMROH"><td>RJ022</td><td>KLINIK HAJI DAN UMROH</td></tr>
						<tr data-kode="RJ023" data-layanan="PSIKOLOG"><td>RJ023</td><td>PSIKOLOG</td></tr>
						<tr data-kode="PTK01" data-layanan="P2TKHU"><td>PTK01</td><td>P2TKHU</td></tr>
						<tr data-kode="VKS01" data-layanan="KLINIK VAKSIN INTERNASIONAL"><td>VKS01</td><td>KLINIK VAKSIN INTERNASIONAL</td></tr>
						<tr data-kode="DRS01" data-layanan="DARUSSALAM"><td>DRS01</td><td>DARUSSALAM</td></tr>
						<tr data-kode="RJ024" data-layanan="BEDAH PLASTIK"><td>RJ024</td><td>BEDAH PLASTIK</td></tr>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
			</div>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
<script>
	var timer;
	function chkRM(){
		clearTimeout(timer);
		var url = "test";

		if($('#pasien_norm').val()==''){
			return false;
		}
		
		timer = setTimeout(function (url){
			document.getElementById("loading").style.display = "block";
			$.get(baseUrl + "/controllers/C_pasien.php?rm=" + $('#pasien_norm').val(), function( data ) {
				document.getElementById("loading").style.display = "none";
				var result = JSON.parse(data);
				if(result.code == '203') {
					swal("Info!", result.message, "info");
					return false;
				}

				
				if(result.code == '200' && result.data != null){
					$("#pasien_nama").val(result.data.FS_NM_PASIEN);
				} else {
					swal({
						title: "No. Rekam Medik tidak dikenali ?",
						text: "Hapus isian No. Rekam Medik!",
						type: "warning",
						showCancelButton: true,
						confirmButtonClass: "btn-danger",
						confirmButtonText: "YA!",
						cancelButtonText: "TIDAK!",
						closeOnConfirm: false,
						closeOnCancel: false
					},
					function(isConfirm) {
						if (isConfirm) {
							swal("Terhapus!", "Isi ulang No. Rekam Medik.", "success");
							$('#pasien_norm').val('');
						} else {
							// swal("Cancel", "Lanjutkan isian No. Rekam Medik", "error");
						}
					});
				}
			});
			
		},1000);
	}

	$('#pesan_tanggal').change(function() {
		var date = new Date($(this).val());
		var hariIndo = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'];
		var hari_numeric = date.getDay();
		$("#pesan_hari").val(hariIndo[hari_numeric-1]);
	});

	function noUrut() {
		swal({
			title: "Masukkan No. RM:",
			// text: "Masukkan No. RM:",
			type: "input",
			showCancelButton: true,
			closeOnConfirm: false,
			inputPlaceholder: "No. Rekam Medik"
		}, function (inputValue) {
			if (inputValue === false) return false;
			if (inputValue === "") {
				swal.showInputError("No. Rekam Medik belum diisi!");
				return false
			}
			console.log(baseUrl + "/controllers/C_booking.php?action=nourut&rm=" + inputValue)
			// swal("Nice!", "You wrote: " + inputValue, "success");
			window.location.replace(baseUrl + "/controllers/C_booking.php?action=nourut&rm=" + inputValue);
		});
	}

	$(document).ready(function () {
		$(".selecttable > tr").on("click", function (elem) {
			var layanan_kode = $(this).attr("data-kode");
			var layanan_nama = $(this).attr("data-layanan");

			$("#layanan_kode").val(layanan_kode);
			$("#layanan_nama").val(layanan_nama);
			$('#ModalLayanan').modal('toggle');
		});
	});
</script>