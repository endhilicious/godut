 <h1>DAFTAR NILAI KELAS <?php echo $this->data['kelas']; ?></h1>
	<form method="POST" action="<?php echo base_url('index.php/nilai_controller/input_nilai'); ?>">
	
	<table border="1">
		 <input type="text" name="kelas" value="<?php echo 'kelas_'.$this->data['kelas']; ?>">
		 <input type="text" name="pelajaran" value="<?php echo $this->session->userdata("bidang_studi"); ?>">
		
	 	<tr>
	 		<td>Nama</td>
	 		<td>NISN</td>
	 		<td>NILAI</td>
	 	</tr>
	 		<?php 
	 			$i = 0;
	 			$urutan = 0;
	 			foreach ($nilai as $value) {
	 				$i++;
	 				$nisn[$i] = $value->nisn;
	 		?>
	 	<tr>

	 			<td>
	 				<?php echo $value->nama_awal.' '.$value->nama_akhir;?>
	 				
	 			</td>
	 			<td>
					<input type="hidden" name="nisn-<?php echo $i;?>" value="<?php echo $value->nisn; ?>" readonly>		
					<p><?php echo $value->nisn; ?></p>			
				</td>
	 			<td>
	 				<input type="text" name="nilai<?php
	 			echo $i;?>">
	 			</td>
	 	</tr>
	 		<?php
	 		$urutan++;
	 			}

	 		 ?>
	 		<input type="text" name="total_siswa" value="<?php echo $i; ?>">
	</table>
	<input type="submit" name="submit" value="input data">
	</form>