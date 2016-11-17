<!DOCTYPE html>
<html>
<head>
	<title>Go-Education</title>
</head>
<body>
	<h1>Login berhasil !</h1>
	<h2>Hai, <?php echo $this->session->userdata("nama"); ?></h2>
	<a href="<?php echo base_url('index.php/parent_controller/logout'); ?>">Logout</a>

		<?php echo $parent->nama_akhir; ?>
		<?php echo "<br><br><br>"; ?>
		<?php echo $parent->tanggal_lahir; ?>
		<!--<?php $foto = $siswa->foto; ?>
		<img src="<?php echo base_url('/upload/'.$siswa->foto) ?>">-->
</body>
</html>