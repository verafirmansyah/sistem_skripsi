<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sistem Skripsi Online Berbasis Web
 * @version    1
 * @author     Devi Adi Nufriana | https://facebook.com/mysilkyheart
 * @copyright  (c) 2018
 * @email       deanheart09@gmail.com
 *
 * PERINGATAN :
 * 1. TIDAK DIPERKENANKAN MEMPERJUALBELIKAN APLIKASI INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 2. TIDAK DIPERKENANKAN MENGHAPUS KODE SUMBER APLIKASI.
 * 3. TIDAK MENYERTAKAN LINK KOMERSIL (JASA LAYANAN HOSTING DAN DOMAIN) YANG MENGUNTUNGKAN SEPIHAK.
 */

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('home');
	}

	function formDaftar()
	{
		$data['jurusan'] = $this->M_data->find('jurusan');
		$this->load->view('formDaftar', $data);
	}

	function filterKonsentrasi(){

		$IDJurusan = $this->input->post('IDJurusan');
		$where = array('IDJurusanKsn' => $IDJurusan );
		$data = $this->M_data->find('konsentrasi', $where);
		$lists = "<option value=''>Pilih</option>";

		foreach($data->result() as $u){
			$lists .= "<option value='".$u->IDKonsentrasi."'>".$u->Konsentrasi."</option>"; 
		}

		$callback = array('list'=> $lists); 
		echo json_encode($callback);
	}

	function daftarMahasiswa()
	{
		$ID = $this->input->post('nim');
		$Nama = $this->input->post('nama');
		$Jurusan = $this->input->post('jurusan');
		$Konsentrasi = $this->input->post('konsentrasi');
		$NoHP = $this->input->post('nohp');
		$Email = $this->input->post('email');

		$filename = "file_".time('upload');

		$config['upload_path'] = './assets/images/User/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']	= $filename;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('foto'))
		{
			$error = array('error' => $this->upload->display_errors());
			echo 0;

		}
		else {

			$foto = $this->upload->data();
			
			$data = array(
				'ID' => $ID,
				'Nama' => $Nama,
				'IDJurusanUser' => $Jurusan,
				'IDKonsentrasiUser' => $Konsentrasi,
				'NoHP' => $NoHP,
				'Email' => $Email, 
				'Foto' => $foto['file_name'],
				'Status' => 'Daftar'
			);
			$this->M_data->save($data, 'users');
			echo 1;
		}

	}

	function session()
	{
		$username = $this->input->post('nim');
		$password = md5($this->input->post('password'));

		$where = "ID='$username' AND Password='$password'";

		$where_admin = "username='$username' AND Password='$password'";

		$users = $this->M_data->find('users', $where, '', '', '', '', 'konsentrasi','konsentrasi.IDKonsentrasi = users.IDKonsentrasiUser');

		$admin = $this->M_data->find('admin', $where_admin);

		if ($users->num_rows() > 0) {

			foreach ($users->result() as $u) {

				$data = array(
					'ID' => $u->ID,
					'Status' => $u->Status,
					'Nama' => $u->Nama,
				);
				
				$status = $u->Status;
				if ($u->ID === $u->IDDosen) {
					$data['Kaprodi'] = 1;
					echo 3;
				} elseif($status === 'Dosen') {
					$data['Kaprodi'] = 0;
					echo 1;
				} else {
					echo 2;
				}

				$this->session->set_userdata($data);

			}

		} elseif ($admin->num_rows() > 0) {

			foreach ($admin->result() as $b) {

				$data = array(
					'id_admin' => $b->id_admin,
					'username' => $b->username,
					'password' => $b->Password,
					'status' => 'Admin',
				);

				$this->session->set_userdata($data);
				echo 4;
			}
		} else {
			redirect('Home');
		}
	}

	public function Logout()
	{
		$this->session->sess_destroy();
		redirect('Home');
	}
}
