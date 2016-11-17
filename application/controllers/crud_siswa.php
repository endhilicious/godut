 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud_Siswa extends CI_Controller {

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
		$this->load->model('db_siswa');	
	}

	public function login_kembali()
	{
		$this->load->view('login/login_siswa');
	}

	public function login_siswa()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$where = array(
				'email' => $email,
				'password' => $password
			);
		$cek = $this->db_siswa->cek_login("siswa_login", $where)->num_rows();
		if($cek > 0){
			$data_session = array(
			'nama' => $email,
			'status' => "login"
			);
 
		$this->session->set_userdata($data_session);
 			//mengambil data user yang telah login
		$data['siswa'] = $this->db_siswa->tampil_data($email);
		$this->load->view('siswa/halaman_utama_siswa', $data);
			
			//redirect(base_url("index.php/crud_Siswa/halaman_utama_siswa"));

		}else{
			echo '<script type="text/javascript">alert("email atau password salah !!!");</script>';
			echo "<script>window.location = 'login_kembali'</script>";
		}
	}

	public function halaman_utama_siswa()
	{
		$this->load->view('siswa/halaman_utama_siswa');
	}
 
	function logout(){
		$this->session->sess_destroy();
		redirect(base_url('index.php/home_page/halaman_login'));
	}

	

	public function input_siswa()
	{
		$size 		= $_FILES['foto']['size'];
		if ($size < 1000000) {		
			$nama_foto 	= $_FILES['foto']['name'];
			$asal_foto 	= $_FILES['foto']['tmp_name'];
			$error 		= $_FILES['foto']['error'];
			$format 	= $_FILES['foto']['type'];	
			move_uploaded_file($asal_foto, 'assets/img/upload/'.$nama_foto);			
		}else{
			echo '
<script type="text/javascript">
	alert("file terlalu besar");
</script>';
die();
		}
		//set_rules Fungsi ini memiliki 3 parameter. Parameter pertama adalah nama field yang akan diberi role. Parameter kedua adalah label dari field tersebut. Label disini digunakan ketika terjadi error maka label tersebut akan tampil sebagai error message. 

		$this->form_validation->set_rules('nama_awal','nama_awal','required');
		$this->form_validation->set_rules('nama_akhir','nama_akhir','required');
		$this->form_validation->set_rules('email','email','required');
		$this->form_validation->set_rules('password','password','required');
		$this->form_validation->set_rules('foto','foto','required');
		$this->form_validation->set_rules('nisn','nisn','required');
		$this->form_validation->set_rules('nis','nis','required');
		$this->form_validation->set_rules('kelas','kelas','required');
		$this->form_validation->set_rules('alamat','alamat','required');
		$this->form_validation->set_rules('tempat_lahir','tempat_lahir','required');
		$this->form_validation->set_rules('tanggal_lahir','tanggal_lahir','required');
		$this->form_validation->set_rules('agama','agama','required');
		$this->form_validation->set_rules('jenis_kelamin','jenis_kelamin','required');
		$this->form_validation->set_rules('anak_ke','anak_ke','required');
		$this->form_validation->set_rules('tanggal_diterima','tanggal_diterima','required');
		$this->form_validation->set_rules('nomor_sktb','nomor_sktb','required');
		$this->form_validation->set_rules('alamat_parent','alamat_parent','required');
		$this->form_validation->set_rules('nama_ayah','nama_ayah','required');
		$this->form_validation->set_rules('pekerjaan_ayah','pekerjaan_ayah','required');
		$this->form_validation->set_rules('pendidikan_ayah','pendidikan_ayah','required');
		$this->form_validation->set_rules('nama_ibu','nama_ibu','required');
		$this->form_validation->set_rules('pekerjaan_ibu','pekerjaan_ibu','required');
		$this->form_validation->set_rules('pendidikan_ibu','pendidikan_ibu','required');



		/*========================================================*/
		/*untuk memasukkan ke dalam table kelas*/
			$kelas = $this->input->post('kelas');
		/*=========================*/

		$data = array(
			'nisn' => $this->input->post('nisn'),
			'nis' => $this->input->post('nis'),
			'nama_awal' => $this->input->post('nama_awal'),
			'nama_akhir' => $this->input->post('nama_akhir'),
			'kelas' => $this->input->post('kelas'),
			'email' => $this->input->post('email'),	
			'password' => $this->input->post('password'),
			'foto' => $nama_foto,	
			'alamat' => $this->input->post('alamat'),
			'tempat_lahir' => $this->input->post('tempat_lahir'),
			'tanggal_lahir' => $this->input->post('tanggal_lahir'),
			'agama' => $this->input->post('agama'),
			'jenis_kelamin' => $this->input->post('jenis_kelamin'),
			'anak_ke' => $this->input->post('anak_ke'),
			'tanggal_diterima' => $this->input->post('tanggal_diterima'),
			'nomor_sktb' => $this->input->post('nomor_sktb'),
			'alamat_parent' => $this->input->post('alamat_parent'),
			'nama_ayah' => $this->input->post('nama_ayah'),
			'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah'),
			'pendidikan_ayah' => $this->input->post('pendidikan_ayah'),
			'nama_ibu' => $this->input->post('nama_ibu'),
			'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu'),
			'pendidikan_ibu' => $this->input->post('pendidikan_ibu')
			);
		$data_login = array(
				'nisn' => $this->input->post('nisn'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password')
			);
		$data_nilai = array(
				'nisn' => $this->input->post('nisn'),
				'nama_awal' => $this->input->post('nama_awal'),
				'nama_akhir' => $this->input->post('nama_akhir'),
				'kelas' => $this->input->post('kelas')
			);
		$data_nilai_kelas = array(
				'nisn' => $this->input->post('nisn'),
				'nama_awal' => $this->input->post('nama_awal'),
				'nama_akhir' => $this->input->post('nama_akhir')
			);
		$data_semua_mapel = array(
				'nisn' => $this->input->post('nisn'),
				'nama_awal' => $this->input->post('nama_awal'),
				'nama_akhir' => $this->input->post('nama_akhir'),
				'kelas' => $this->input->post('kelas')
			);
		$this->db_siswa->db_input_matematika($data_semua_mapel,'nilai_matematika');
		$this->db_siswa->db_input_fisika($data_semua_mapel,'nilai_fisika');
		$this->db_siswa->db_input_kimia($data_semua_mapel,'nilai_kimia');
		$this->db_siswa->db_input_biologi($data_semua_mapel,'nilai_biologi');
		$this->db_siswa->db_input_indonesia($data_semua_mapel,'nilai_indonesia');
		$this->db_siswa->db_input_inggris($data_semua_mapel,'nilai_inggris');
		$this->db_siswa->db_input_pkn($data_semua_mapel,'nilai_pkn');
		//$this->db_siswa->db_siswa_total($data_semua_mapel,'siswa_nilai');
		$this->db_siswa->db_input_siswa($data, 'siswa');
		$this->db_siswa->db_nilai_kelas_siswa($data_nilai_kelas, 'kelas_'.$kelas);			
		$this->db_siswa->db_input_siswa_login($data_login, 'siswa_login');
			redirect('home_page/login_siswa');
	}

}
