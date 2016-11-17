<?php 

/**
* 
*/
class Db_Guru extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		public function db_input_guru($data , $table)
		{
			return $this->db->insert($table, $data);			
		}

		public function db_input_guru_login($data , $table)
		{
			return $this->db->insert($table, $data);	
		}

		public function cek_login($table,$where)
		{				
			return $this->db->get_where($table,$where);
		}

		

		function tampil_data($nign){

			$this->db->select('*');
			$this->db->from('guru');
			$this->db->where('nign',$nign);
			$query=$this->db->get();
			$guru=$query->row();

			return $guru;
		}
		function tampil_data_ngajar($mapel){

			$this->db->select('*');
			$this->db->from('guru');
			$this->db->where('bidang_studi',$mapel);
			$query=$this->db->get();
			$guru=$query->row();

			return $guru;
		}
		public function cek_mapel($table , $nign)
		{
			$this->db->select('bidang_studi');
			$this->db->from($table);
			$this->db->where('nign',$nign);
			$query=$this->db->get();
			$pelajaran=$query->row();

			return $pelajaran->bidang_studi;			
		}
		public function cek_nama($table , $nign)
		{
			$this->db->select('nama_lengkap');
			$this->db->from($table);
			$this->db->where('nign',$nign);
			$query=$this->db->get();
			$pelajaran=$query->row();

			return $pelajaran->nama_lengkap;			
		}
}

 ?>