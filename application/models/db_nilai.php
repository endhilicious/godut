<?php 
	/**
	* 
	*/
	class Db_Nilai extends CI_Model
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		public function tampil_nilai($nilai)
		{
			$this->db->select('*');
			$this->db->from($nilai);
			$query=$this->db->get();
			$nilai=$query->result();

			return $nilai;
		}
		public function input_nilai($nilai,$data)
		{
			$this->db->select('*');
			$this->db->from($nilai);
			$query=$this->db->get();
			$nilai=$query->result();

			return $nilai;			
		}
		public function update_data($kelas , $mapel , $jumlah_siswa , $nisn , $nilai)
		{
			$this->db->set('matematika' , $nilai);
			$this->db->where('nisn' , $nisn);
			$this->db->update($kelas);
		}
		public function update_siswa_nilai($mapel , $jumlah_siswa , $nisn , $nilai)
		{
			$this->db->set('matematika1' , $nilai);
			$this->db->where('nisn' , $nisn);
			return $this->db->update('siswa_nilai');
		}

		public function ambil_nisn($table)
		{
			$this->db->select('nisn');
			$this->db->from($table);
			$query=$this->db->get();
			$pelajaran=$query->result();

			return $pelajaran;		
		}

		public function ambil_data($table)
		{
			$this->db->select('*');
			$this->db->from($table);
			$query=$this->db->get();
			$pelajaran=$query->row();

			return $pelajaran;		
		}
	}
 ?>