<!DOCTYPE html>
<html>
<head>
	<title>Go-Education</title>
</head>
<body>
	<h1>Login berhasil !</h1>
	<h2>Hai, <?php echo $this->session->userdata("nama"); ?></h2>
	<a href="<?php echo base_url('index.php/crud_siswa/logout'); ?>">Logout</a>

<?php 
	echo $siswa->nama_akhir; 
	echo "<br><br><br>"; 
	echo $siswa->tanggal_lahir; 
	echo "<br><br><br>"; 
	$foto = $siswa->foto; 
?>
<style type="text/css">
	img#profil{
		width: 150px;
		height: 200px;
	}
</style>
		<img id="profil" src="<?php echo base_url('assets/img/upload/'.$siswa->foto) ?>">
</body>
</html>