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
		/* text-align:center; */
		padding : 2px;
		display: none;
		margin: auto;
		width: 50%;
		padding: 10px;
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
	
	button:focus {outline:0;}

	@media only screen and (max-width: 990px) {
		.buttonmenu {
			display: none;
		}

		.row-btn-menu {
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

		button.mobilemode {
			background-color: #e8e7e7;
			border: none;
			width: 40px;
		}

		.square {
			margin-bottom: 5px;
			background: #00848a;
			width: 140px;
			height: 45px;
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

	.selecttable>tr:hover {
		background-color: aliceblue;
		cursor : pointer;
	}

	.selecttable {
		font-size: 12px;
	}

	#tbody-dokter.selecttable>tr>td:hover {
		background-color: #b2efb4;
		cursor : pointer;
	}

	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding: 4px;
		line-height: 1.42857143;
		vertical-align: top;
		border-top: 1px solid #ddd;
		border-right: 1px solid #ddd;
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
							<p><font style="font-size: 18px; color: #346d00;">RS. HAJI JAKARTA</font><br>Pelayanan <font style="color: red;">Telemedicine</font></p>
						</div>
						<div class="logo-rs-mobile">
							<!-- C:\xampp\htdocs\telemedicineRSHJ\assets\img\rshaji.jpg -->
							<img src="<?php echo BASE_URL; ?>/assets/img/rshaji.jpg" alt="Rshaji-Jakarta-telemedicine" width="50" height="38"
							onclick="window.open('https://www.rshaji-jakarta.com/')">
							<p><font style="font-size: 18px; color: #346d00;">RS. HAJI JAKARTA</font><br>Pelayanan <font style="color: red;">Telemedicine</font></p>
						</div>
					</div>
					<div class="col-md-8">
						<div class="booking-form">
							<form action="<?php echo BASE_URL; ?>/controllers/C_booking.php?action=create" method="post">
								<div class="row no-margin">
									<div class="col-sm-12">
										<div class="form-group" style="display: inline-flex; width: 100%;">
											<span class="form-label">Pilih Dokter</span>
												<input class="form-control" id="m_pegawai_kode" name="m_pegawai_kode" type="text" readonly required style="display: none;">
												<input class="form-control" id="m_pegawai_nama" name="m_pegawai_nama" type="text" readonly required>
												<input class="form-control" id="kode_smf" name="kode_smf" type="text" readonly required style="display: none;">
												<button type="button" class="displaymode" data-toggle="modal" data-target="#ModalDokter">CARI</button>
												<button type="button" class="mobilemode" data-toggle="modal" data-target="#ModalDokter"><i class="fa fa-search"></i></button>
										</div>
									</div>
								</div>
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
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">Pesan Tanggal</span>
											<input class="form-control" id="pesan_tanggal" name="pesan_tanggal" type="date" required>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">Hari</span>
											<input class="form-control" id="pesan_hari" name="pesan_hari" type="text" placeholder="" readonly>		
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">Jam</span>
											<input class="form-control" id="pesan_jam" name="pesan_jam" type="text" placeholder="" readonly>		
										</div>
									</div>
								</div>
								<div class="row no-margin">
									<div class="form-group">
										<span class="form-label">Email</span>
										<input class="form-control" name="pasien_email" type="email" placeholder="Enter your email">
									</div>
								</div>
								<div class="row no-margin">
									<div class="form-group">
										<span class="form-label">Phone</span>
										<input class="form-control" name="pasien_tlp" type="tel" placeholder="Enter your phone number" required>
									</div>
								</div>
								<div class="form-btn">
									<button class="submit-btn">DAFTAR</button>
								</div>
							</form>
						</div>	
					</div>
					<div class="col-md-2">
						<div class="buttonmenu noselect">
							<!-- <div class="square" onclick="window.location.href='<?php // echo BASE_URL ?>/controllers/C_jadwaldokter.php';">
								JADWAL DOKTER
							</div>
							<div class="square" onclick="window.location.href='<?php // echo BASE_URL ?>/controllers/C_antrianbooking.php?action=list_antrian';">
								ANTRIAN
							</div> -->
							<div class="square" onclick="noUrut()">
								NOMER URUT
							</div>
						</div>
					</div>
					<div class="container">
						<div class="row-btn-menu">
							<!-- <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0;">
								<div class="square" onclick="window.location.href='<?php // echo BASE_URL ?>/controllers/C_jadwaldokter.php';">
									JADWAL DOKTER
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0;">
								<div class="square" onclick="window.location.href='<?php // echo BASE_URL ?>/controllers/C_antrianbooking.php?action=list_antrian';">
									ANTRIAN
								</div>
							</div> -->
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4"">
								
								<div class="square" onclick="noUrut()">
									NOMER URUT
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="ModalLayanan" tabindex="-1" role="dialog" aria-labelledby="ModalLayananLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLayananLabel">Daftar Layanan</h5>
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
						<tr data-kode="RJ001" data-nama="UMUM"><td>RJ001</td><td>UMUM</td></tr>
						<tr data-kode="FS001" data-nama="REHABILITASI MEDIK"><td>FS001</td><td>REHABILITASI MEDIK</td></tr>
						<tr data-kode="FS002" data-nama="TERAPI WICARA"><td>FS002</td><td>TERAPI WICARA</td></tr>
						<tr data-kode="FS003" data-nama="FISIOTERAPI"><td>FS003</td><td>FISIOTERAPI</td></tr>
						<!-- <tr data-kode="LAIN1" data-nama="LAIN-LAIN"><td>LAIN1</td><td>LAIN-LAIN</td></tr> -->
						<tr data-kode="MCU01" data-nama="MEDICAL CHECK UP"><td>MCU01</td><td>MEDICAL CHECK UP</td></tr>
						<tr data-kode="RJ002" data-nama="BEDAH"><td>RJ002</td><td>BEDAH</td></tr>
						<tr data-kode="RJ003" data-nama="GIGI DAN MULUT"><td>RJ003</td><td>GIGI DAN MULUT</td></tr>
						<tr data-kode="RJ004" data-nama="GIZI"><td>RJ004</td><td>GIZI</td></tr>
						<tr data-kode="RJ005" data-nama="JANTUNG"><td>RJ005</td><td>JANTUNG</td></tr>
						<tr data-kode="RJ006" data-nama="KANDUNGAN & KEBIDANAN"><td>RJ006</td><td>KANDUNGAN & KEBIDANAN</td></tr>
						<tr data-kode="RJ007" data-nama="ANAK"><td>RJ007</td><td>ANAK</td></tr>
						<tr data-kode="RJ008" data-nama="JIWA"><td>RJ008</td><td>JIWA</td></tr>
						<tr data-kode="RJ009" data-nama="KULIT/KELAMIN"><td>RJ009</td><td>KULIT/KELAMIN</td></tr>
						<tr data-kode="RJ010" data-nama="MATA"><td>RJ010</td><td>MATA</td></tr>
						<tr data-kode="RJ011" data-nama="PARU"><td>RJ011</td><td>PARU</td></tr>
						<tr data-kode="RJ012" data-nama="PENYAKIT DALAM"><td>RJ012</td><td>PENYAKIT DALAM</td></tr>
						<tr data-kode="RJ013" data-nama="SYARAF"><td>RJ013</td><td>SYARAF</td></tr>
						<tr data-kode="RJ014" data-nama="THT"><td>RJ014</td><td>THT</td></tr>
						<tr data-kode="RJ015" data-nama="AKUPUNKTUR"><td>RJ015</td><td>AKUPUNKTUR</td></tr>
						<tr data-kode="RJ016" data-nama="ENDOSCOPY"><td>RJ016</td><td>ENDOSCOPY</td></tr>
						<tr data-kode="RJ017" data-nama="PERAWATAN LUKA"><td>RJ017</td><td>PERAWATAN LUKA</td></tr>
						<tr data-kode="RJ018" data-nama="ANASTESI"><td>RJ018</td><td>ANASTESI</td></tr>
						<tr data-kode="RJ019" data-nama="EMERGENCY RB"><td>RJ019</td><td>EMERGENCY RB</td></tr>
						<tr data-kode="RJ020" data-nama="KLINIK UMMI"><td>RJ020</td><td>KLINIK UMMI</td></tr>
						<tr data-kode="RJ021" data-nama="KLINIK SAKINAH"><td>RJ021</td><td>KLINIK SAKINAH</td></tr>
						<tr data-kode="RJ022" data-nama="KLINIK HAJI DAN UMROH"><td>RJ022</td><td>KLINIK HAJI DAN UMROH</td></tr>
						<tr data-kode="RJ023" data-nama="PSIKOLOG"><td>RJ023</td><td>PSIKOLOG</td></tr>
						<tr data-kode="PTK01" data-nama="P2TKHU"><td>PTK01</td><td>P2TKHU</td></tr>
						<tr data-kode="VKS01" data-nama="KLINIK VAKSIN INTERNASIONAL"><td>VKS01</td><td>KLINIK VAKSIN INTERNASIONAL</td></tr>
						<tr data-kode="DRS01" data-nama="DARUSSALAM"><td>DRS01</td><td>DARUSSALAM</td></tr>
						<tr data-kode="RJ024" data-nama="BEDAH PLASTIK"><td>RJ024</td><td>BEDAH PLASTIK</td></tr>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="ModalDokter" tabindex="-1" role="dialog" aria-labelledby="ModalDokterLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalDokterLabel">Daftar Dokter</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class='table'>
						<thead>
							<tr>
								<th>No.</th>
								<th width='20%'>Nama Dokter</th>
								<th>Spesialis</th>
								<th>Senin</th>
								<th>Selasa</th>
								<th>Rabu</th>
								<th>Kamis</th>
								<th>Jumat</th>
								<th>Sabtu</th>
								<th>Minggu</th>
							</tr>
						</thead>
						<tbody id="tbody-dokter" class="selecttable">
							<tr><td colspan=9 align=center>Jadwal Tidak Tersedia</td></tr>
						<tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
		// $("#pesan_hari").val(hariIndo[hari_numeric-1]);
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
		var dtToday = new Date();
    
		var month = dtToday.getMonth() + 1;
		var day = dtToday.getDate();
		var year = dtToday.getFullYear();
		if(month < 10)
			month = '0' + month.toString();
		if(day < 10)
			day = '0' + day.toString();
		
		var maxDate = year + '-' + month + '-' + day;
		// alert(maxDate);
		$('#pesan_tanggal').attr('min', maxDate);
		$('#pesan_tanggal')

		var jadwaldokter = [];
		$.get(baseUrl + "/controllers/C_jadwaldokter.php?action=getjadwaldokter", function( data ) {
			jadwaldokter = JSON.parse(data);

			var htmljadwal = "";
			var jadwaldokterfilter = jadwaldokter;
			var nomor = 1;
			jadwaldokterfilter.forEach(function(datajadwal) {
				htmljadwal += "<tr>";
				htmljadwal += "<td align=center>"+nomor+"</td><td>"+datajadwal.FS_NM_PEG+"</td><td>"+datajadwal.FS_NM_SMF+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SENIN' data-value='"+datajadwal.SENIN+"'>"+datajadwal.SENIN+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SELASA' data-value='"+datajadwal.SELASA+"'>"+datajadwal.SELASA+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='RABU' data-value='"+datajadwal.RABU+"'>"+datajadwal.RABU+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='KAMIS' data-value='"+datajadwal.KAMIS+"'>"+datajadwal.KAMIS+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='JUMAT' data-value='"+datajadwal.JUMAT+"'>"+datajadwal.JUMAT+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SABTU' data-value='"+datajadwal.SABTU+"'>"+datajadwal.SABTU+"</td>";
				htmljadwal += "<td class=selected-col align=center  data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='MINGGU' data-value='"+datajadwal.MINGGU+"'>"+datajadwal.MINGGU+"</td>";
				htmljadwal += "<tr>";
				nomor++;
				$("#tbody-dokter").html(htmljadwal);
			});
			$(".selected-col").on("click", function (elem) {
				selectTime(this);
			});

		});

		$(".selecttable > tr").on("click", function (elem) {
			var layanan_kode = $(this).attr("data-kode");
			var layanan_nama = $(this).attr("data-nama");

			$("#m_layanan_kode").val(layanan_kode);
			$("#m_layanan_nama").val(layanan_nama);
			$('#ModalLayanan').modal('toggle');
			$('#tbody-dokter').html("<tr><td colspan=3 align=center>Jadwal Tidak Tersedia</td></tr>");
			$("#ModalDokterLabel").html("Daftar Dokter " + $("#m_layanan_nama").val());
			
			var htmljadwal = "";
			var jadwaldokterfilter = jadwaldokter.filter(p=>p.FS_KD_LAYANAN==layanan_kode);
			
			var nomor = 1;
			jadwaldokterfilter.forEach(function(datajadwal) {
				htmljadwal += "<tr>";
				htmljadwal += "<td align=center>"+nomor+"</td><td>"+datajadwal.FS_NM_PEG+"</td><td>"+datajadwal.FS_NM_SMF+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SENIN' data-value='"+datajadwal.SENIN+"'>"+datajadwal.SENIN+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SELASA' data-value='"+datajadwal.SELASA+"'>"+datajadwal.SELASA+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='RABU' data-value='"+datajadwal.RABU+"'>"+datajadwal.RABU+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='KAMIS' data-value='"+datajadwal.KAMIS+"'>"+datajadwal.KAMIS+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='JUMAT' data-value='"+datajadwal.JUMAT+"'>"+datajadwal.JUMAT+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='SABTU' data-value='"+datajadwal.SABTU+"'>"+datajadwal.SABTU+"</td>";
				htmljadwal += "<td class=selected-col align=center data-kodesmf='"+datajadwal.FS_KD_SMF+"' data-kodedokter='"+datajadwal.FS_KD_DOKTER+"' data-namadokter='"+datajadwal.FS_NM_PEG+"' data-hari='MINGGU' data-value='"+datajadwal.MINGGU+"'>"+datajadwal.MINGGU+"</td>";
				htmljadwal += "<tr>";
				nomor++;
			});
			$("#tbody-dokter").html(htmljadwal);
			$(".selected-col").on("click", function (elem) {
				selectTime(this);
			});
		});

		function selectTime(elem){
			var kode_dokter = $(elem).attr("data-kodedokter");
			var nama_dokter = $(elem).attr("data-namadokter");
			var kode_smf = $(elem).attr("data-kodesmf");
			var hari = $(elem).attr("data-hari");
			var time = $(elem).attr("data-value");
			$("#m_pegawai_kode").val(kode_dokter);
			$("#m_pegawai_nama").val(nama_dokter);
			$("#kode_smf").val(kode_smf);
			
			$('#ModalDokter').modal('toggle');
			$("#pesan_hari").val(hari);
			$("#pesan_jam").val(time);
		}
	});
</script>