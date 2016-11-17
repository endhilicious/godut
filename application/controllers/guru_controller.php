 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class Guru_Controller extends CI_Controller
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
		$this->load->model('db_guru');	
	}

	public function input_guru()
	{
		$this->form_validation->set_rules('nama_lengkap','nama_lengkap','required');
		$this->form_validation->set_rules('nama_panggilan','nama_panggilan','required');
		$this->form_validation->set_rules('nign','nign','required');
		$this->form_validation->set_rules('nuptk','nuptk','required');
		$this->form_validation->set_rules('npsn','npsn','required');
		$this->form_validation->set_rules('alamat','alamat','required');
		$this->form_validation->set_rules('email','email','required');
		$this->form_validation->set_rules('password','password','required');
		$this->form_validation->set_rules('tempat_lahir','tempat_lahir','required');
		$this->form_validation->set_rules('tanggal_lahir','tanggal_lahir','required');
		$this->form_validation->set_rules('agama','agama','required');
		$this->form_validation->set_rules('jenis_kelamin','jenis_kelamin','required');
		$this->form_validation->set_rules('bidang_studi','bidang_studi','required');

		$data = array(
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nama_panggilan' => $this->input->post('nama_panggilan'),
				'nign' => $this->input->post('nign'),
				'nuptk' => $this->input->post('nuptk'),
				'npsn' => $this->input->post('npsn'),
				'alamat' => $this->input->post('alamat'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tanggal_lahir' => $this->input->post('tanggal_lahir'),
				'agama' => $this->input->post('agama'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'bidang_studi' => $this->input->post('bidang_studi')
			);

		$data_login = array(
				'nign' => $this->input->post('nign'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password')
			);

			$this->db_guru->db_input_guru($data, 'guru');
			$this->db_guru->db_input_guru_login($data_login, 'guru_login');
			redirect('home_page/login_guru');
	}

	public function login_kembali_guru()		
	{
		$this->load->view('login/login_guru');
	}

	public function input_nilai_siswa()
	{ 
		$this->load->view('guru/input_nilai');
	}

	public function cetak_nilai_siswa()
	{ 
		$this->load->view('guru/cetak_nilai_siswa');
	}


	public function login_guru()
	{
		$nign = $this->input->post('nign');
		$password = $this->input->post('password');
		$where = array(
				'nign' => $nign,
				'password' => $password
			);
		$cek = $this->db_guru->cek_login("guru", $where)->num_rows();
		$mapel = $this->db_guru->cek_mapel("guru", $nign);
		if($cek > 0){

			$data_session = array(
			'nama' => $this->db_guru->cek_nama("guru",$nign),
			'nign' => $nign,
			'bidang_studi' => $mapel,
			'status' => "login"
			); 
		$this->session->set_userdata($data_session);
 			//mengambil data user yang telah login
		$data['guru'] = $this->db_guru->tampil_data($nign);
		$this->load->view('guru/halaman_utama_guru', $data);
			
			//redirect(base_url("index.php/crud_Siswa/halaman_utama_siswa"));

		}else{
			echo '<script type="text/javascript">alert("email atau password salah !!!");</script>';
			echo "<script>window.location = 'login_kembali_guru'</script>";
		}
	}

	function logout(){
		$this->session->sess_destroy();
		redirect(base_url('index.php/home_page/halaman_login'));
	}
	
	public function halaman_utama_guru()
	{
		$this->load->view('guru/halaman_utama_guru');
	}
	function is_logged_in(){
	    $is_logged_in = $this->session->userdata('is_logged_in');
	    if(!isset($is_logged_in) || $is_logged_in != true)
	    {
	        echo 'You don\'t have permission to access this page.';
	        die();
	        //$this->load->view('login_form');
	    }
	}  
}