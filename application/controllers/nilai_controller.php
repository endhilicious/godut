 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_Page extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));

		// Load form validation library
		$this->load->library('form_validation');

		// Load session library
		$this->load->library('session');		
	}

	public function index()
	{
		$this->load->view('halaman_awal');
	}

	public function registrasi()
	{
		$this->load->view('registrasi/registrasi');
	}

	public function registrasi_siswa()
	{
		$this->load->view('registrasi/registrasi_siswa');
	}

	public function login_siswa()
	{
		$this->load->view('login/login_siswa');
	}

	public function registrasi_parent()
	{
		$this->load->view('registrasi/registrasi_parent');
	}

	public function login_parent()
	{
		$this->load->view('login/login_parent');
	}

	public function registrasi_guru()
	{
		$this->load->view('registrasi/registrasi_guru');
	}

	public function login_guru()
	{
		$this->load->view('login/login_guru');
	}

	public function halaman_login()
	{
		$this->load->view('login/login');
	}

	public function cetak()
	{ 
		$data = [];
        //load the view and saved it into $html variable
        $html=$this->load->view('welcome_message', $data, true);
 
        //this the the PDF filename that user will get to download
        $pdfFilePath = "cetak.pdf";
 
        //load mPDF library
        $this->load->library('m_pdf');
 
       //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);
 
        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");  		
	}
}
                                                                                                                                                                                                                                                                                                                                               