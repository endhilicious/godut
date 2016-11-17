 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class Parent_Controller extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(array('form', 'url'));		
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');	
		// Load database
		$this->load->model('db_parent');	
	}

	public function input_parent()
	{
		$this->form_validation->set_rules('nama_awal','nama_awal','required');
		$this->form_validation->set_rules('nama_akhir','nama_akhir','required');
		$this->form_