<?php 
	/**
	* 
	*/
	class Db_Siswa extends CI_Model
	{
		
		function __construct()
		{
			$this->load->database();
		}

		public function db_input_siswa($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_matematika($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_fisika($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_kimia($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_biologi($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_indonesia($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_inggris($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_input_pkn($data , $table)
		{
			return $this->db->insert($table, $data);	
		}
		public function db_siswa_total($data , $table)
		{
			return $this->db->insert($table, $data);	
		}


		public function db_nilai_kelas_siswa($data , $table)
		{
			return $this->db->insert($table, $data);	
		}

		public function db_input_siswa_login($data , $table)
		{
			return $this->db->insert($table, $data);	
		}

		public function cek_login($table,$where)
		{				
			return $this->db->get_where($table,$where);
		}

		function tampil_data($email){

			$this->db->select('*');
			$this->db->from('siswa');
			$this->db->where('email',$email);
			$query=$this->db->get();
			$user=$query->row();

			return $user;
			//return $this->db->get('siswa');
		}
	}
?>