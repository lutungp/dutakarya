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

	@media only screen and (max-width: 990px) {
		.buttonmenu {
			display: none;
		}

		.row-btn-menu {
			text-align:center;
			padding : 2px;
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

					</div>
					<div class="col-md-8">
						<div class="booking-form">
							<form action="<?php echo BASE_URL; ?>/controllers/C_booking.php?action=create" method="post">
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group">
											<span class="form-label">No. RM (Rekam Medik)</span>
											<input class="form-control" name="pasien_norm" type="number" id="pasien_norm" onKeyUp="javascript:chkRM();" required>
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
									<input class="form-control" name="pesan_layanan" type="text" placeholder="" value="UMUM" readonly>
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
						<div class="buttonmenu noselect">
							<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_jadwaldokter.php';">
								JADWAL DOKTER
							</div>
							<div class="square" onclick="window.location.href='<?php echo BASE_URL ?>/controllers/C_antrianbooking.php?action=list_antrian';">
								ANTRIAN
							</div>
							<div class="square" onclick="noUrut()">
								NOMER URUT
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
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
</script>