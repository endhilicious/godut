<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai_Controller extends CI_Controller {
	public function __construct()
	{		
		parent::__construct();
		$this->load->database();
		$this->load->helper(array('form', 'url'));		
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');	
		// Load database
		$this->load->model('db_nilai');
	}
	
	public function nilai_kelas1()
	{
		$op = $_GET['op'];

		switch ($op) {
			case 'kelas_1a':
				$kelas = '1A';
				break;
			case 'kelas_1b':
				$kelas = '1B';
				break;
			case 'kelas_2a':
				$kelas = '2A';
				break;
			case 'kelas_2b':
				$kelas = '2B';
				break;		
			case 'kelas_3a':
				$kelas = '3A';
				break;
			case 'kelas_3b':
				$kelas = '3B';
				break;			
			default:
				echo "";
				break;
		}
		//$nign = $_GET['nign'];
		//$mapel = $_GET['mapel'];
		//$this->$data['guru'] = $this->db_guru->tampil_data_ngajar($mapel);
		$this->data['nilai'] = $this->db_nilai->tampil_nilai('kelas_'.$kelas);
		$this->data['kelas'] = $kelas;

		$this->load->view('nilai/nilai_kelas1' , $this->data);
	}	

	//setting disini !!!!!!
	//ambil nilai dari database dengan rowCount
	//$_GET[] masukkan ke perulangan 
	public function input_nilai()
	{
		$kelas = $_POST['kelas'];
		$jumlah_siswa = $_POST['total_siswa'];
		$mapel = $_POST['pelajaran'];
		$nisn['nisn'] = $this->db_nilai->ambil_nisn($kelas);
		/*==============
		digunakan untuk menginput data nilai kedalam database dengan cara 'multiple'
		==============*/
		for ($i=0; $i < $jumlah_siswa; $i++) { 
			$data[$i] = $_POST['nisn-'.($i+1)];
			$nilai[$i] = $_POST['nilai'.($i+1)];
			array(
				$mapel = $nilai[$i]
				);
			$this->db_nilai->update_data($kelas , $mapel , $jumlah_siswa ,$data[$i] , $nilai[$i]);
			$this->db_nilai->update_siswa_nilai($mapel , $jumlah_siswa ,$data[$i] , $nilai[$i]);
		}
		$this->load->view('guru/input_nilai');
	}




}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  