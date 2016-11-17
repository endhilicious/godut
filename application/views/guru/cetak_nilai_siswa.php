<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai Siswa</title>

    <script src="<?php echo base_url() ?>/assets/js/jquery-2.2.0.min.js"></script>
	<style type="text/css">

	</style>
    <script type="text/javascript">
    var	nilai;
    var masukan;
    var nign = <?php echo $this->session->userdata("nign"); ?>;

    	$(document).ready(function(e) {		

    		$("#opsi_kelas").click(function(e) {	
			$("#loading").show();
    			nilai = $('#opsi_kelas').val();
    			if (nilai == '---') {  				
					$("#loading").hide();
    			}
    			if (nilai == '1a') {
    				masukan = nilai;
					//meloading option NIK dari database
					$("#status").html("Loading...");
					$("#loading").show();
					
					$("#daftar").load("<?php echo site_url('nilai_controller/nilai_kelas1a'); ?>", "op=kelas_1a&nign="+nign);
					//lakukan pengiriman dan pengambilan data
				
					$("#status").html("");
					$("#loading").hide();

    			}else if(nilai == 'dua'){
    				$("p#daftar").html("dua");
    			}else{
    				$("p#daftar").html("");
    			}
    		});
    	});
    </script>
</head>
<body>
	<h1>Masukkan kelas</h1>

	<select class="opsi" id="opsi_kelas" name="opsi_kelas">
		<option>---</option>
		<option id="1a" value="1a" class="opsi" value="satu">1A</option>
		<option id="1b" value="1b" class="opsi" value="satu">1B</option>
		<option id="2a" value="2a" class="opsi" value="dua">2A</option>
		<option id="2b" value="2b" class="opsi" value="dua">2B</option>
		<option id="3a" value="3a" class="opsi" value="dua">3A</option>
		<option id="3b" value="3b" class="opsi" value="dua">3B</option>
	</select>
</body>
</html>