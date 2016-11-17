<?php 

/**
* penghubung ke database
*/
class Db_Parent extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		public function db_input_parent($data , $table)
		{
			return $this->db->insert($table, $data);			
		}

		public function db_input_parent_login($data , $table)
		{
			return $this->db->insert($table, $data);			
		}

		public function cek_login($table,$where)
		{				
			return $this->db->get_where($table,$where);
		}

		function tampil_data($no_identitas){

			$this->db->select('*');
			$this->db->from('parent');
			$this->db->where('no_identitas',$no_identitas);
			$query=$this->db->get();
			$parent=$query->row();

			return $parent;
		}
}

 ?>