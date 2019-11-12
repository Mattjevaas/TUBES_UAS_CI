<?php

require APPPATH . 'controllers/BREST_Controller.php';

defined('BASEPATH') or exit('No direct script access allowed');

class User extends BREST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model('User_model');
	}

	function index_get()
	{		
		$data = $this->User_model->response_all();

		if($data['success']) 
			return $this->send_success($data['data']);

		return $this->send_error($data['data'], "Gagal mendapatkan users.");
	}

	public function userLogin_post()
	{
		$model = $this->User_model->response_login();

		if (!$model['success']) 
			return $this->send_error($model['data'], "Gagal login.");

		return $this->send_success($model['data']);
	}

	function index_post()
	{
		$model = $this->User_model->response_store();

		if (!$model['success']) 
			return $this->send_error($model['data'], "Gagal menyimpan.");
		
		return $this->send_success($model['data']);
	}

	function index_put()
	{
		$id  = $this->put('id');
		
		if (empty($id)) 
			return $this->send_error(['error' => 'kurang parameter id.'], "Gagal mengedit.");
		
		$model = $this->User_model->response_edit($this->put(), $id);
		
		if (!$model['success']) 
			return $this->send_error($model['data'], "Gagal mengedit.");

		return $this->send_success($model['data']);
	}

	function index_delete()
	{
		$id  = $this->delete('id');
		
		if (empty($id)) 
			return $this->send_error(['error' => 'kurang parameter id.'], "Gagal menghapus.");
		
		$model = $this->User_model->response_delete($id);
		
		if (!$model['success'])
			return $this->send_error($model['data'], "Gagal menghapus.");
		
			return $this->send_success($model['data']);
	}
}