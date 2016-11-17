<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_Controller extends CI_Controller {
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
	
	public function tes()
	{
		$this->load->view('tes');
	}

}
