<?php
require APPPATH . 'models/base_model.php';

class User_model extends base_model
{
	public $id;
	public $name;
	public $password;
	public $email;
	public $alamat;
	public $no_telp;
	public $stat_admin;

	private $table_name = 'users';
	
	private $column = ['id', 'name', 'password', 'email', 'alamat' , 'no_telp' , 'stat_admin'];

	private function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	private function rules()
	{
		// rule validasi
		$rules = [
			[
				'field' => $this->column[1],
				'label' => 'Name',
				'rules' => 'required',
				'errors' => array(
					'required' => 'Anda melupakan %s.',
				),
			],
			[
				'field' => $this->column[2],
				'label' => 'Password',
				'rules' => 'required|min_length[2]',
			],
			[
				'field' => $this->column[3],
				'label' => 'Email',
				'rules' => 'required|valid_email|is_unique[users.email]',
			],
			[
				'field' => $this->column[4],
				'label' => 'Alamat',
				'rules' => 'required',
			],
			[
				'field' => $this->column[5],
				'label' => 'No_telp',
				'rules' => 'required',
			],
		];

		return $rules;
	}

	private function rulesLogin()
	{
		// rule validasi
		$rules = [
			[
				'field' => $this->column[2],
				'label' => 'Password',
				'rules' => 'required',
			],
			[
				'field' => $this->column[3],
				'label' => 'Email',
				'rules' => 'required|valid_email',
			],
		];
		return $rules;
	}

	public function response_login()
	{
		// cek hasil validasi
		$this->form_validation->set_rules($this->rulesLogin());
		$validator = $this->form_validation->run();

		if (!$validator) {
			return $this->returnData($this->form_validation->error_array(), false);
		}

		// ambil input
		$post = $this->input->post();
		$this->password = $post[$this->column[2]];
		$this->email = $post[$this->column[3]];

		//cari email 
		$this->db->where($this->column[3], $this->email);
		$query = $this->db->get($this->table_name, 1);

		$row = $query->row();

		if (empty($row)) 
			return $this->returnData(['message' => 'kredensial tidak ditemukan.'], false);
		
		if (password_verify($this->password, $row->password)) 
			return $this->returnData($this->returnData($model));
			
		return $this->returnData(['message' => 'kredensial tidak ditemukan.'], false);
	}

	/**
	 * dapatkan semua model di table
	 * return array / [] / null
	 */
	public function response_all()
	{
		return $this->returnData($this->query_all($this->table_name));
	}

	/**
	 * simpan model ke table
	 * return array / [] / null
	 */
	public function response_store()
	{
		// cek hasil validasi
		$this->form_validation->set_rules($this->rules());

		$validator = $this->form_validation->run();

		if (!$validator) {
			return $this->returnData($this->form_validation->error_array(), false);
		}

		// ambil input
		$post = $this->input->post();
		$this->name = $post[$this->column[1]];
		$this->password = $this->hash_password($post[$this->column[2]]);
		$this->email = $post[$this->column[3]];
		$this->alamat = $post[$this->column[4]];
		$this->no_telp = $post[$this->column[5]];
		$this->stat_admin = 0;

		//gas coba store
		if ($this->query_store($this->table_name)) 
			return $this->returnData($this->toJson($this));
		
		return $this->returnData(['message' => 'gagal menambahkan user'], false);
	}

	/**
	 * edit model dari table
	 * return array / [] / null
	 */
	public function response_edit($arrValue, $id)
	{
		//cari model ada tidak
		$model = $this->query_find($this->table_name, $id);

		if(empty($model)) 
			return $this->returnData(['message' => 'model tidak ditemukan.'], false);
		
		// cek hasil validasi
		$this->form_validation->set_data($arrValue);

		$this->form_validation->set_rules($this->rules());

		$validator = $this->form_validation->run();

		if (!$validator) {
			return $this->returnData($this->form_validation->error_array(), false);
		}

		// isi model
		$this->name = $arrValue[$this->column[1]];
		$this->email = $arrValue[$this->column[3]];
		$this->alamat = $arrValue[$this->column[4]];
		$this->no_telp = $arrValue[$this->column[5]];

		//gas coba edit
		if ($this->query_edit($this->table_name, $arrValue, $this->column[0] . "=" . $id)) 
			return $this->returnData($this->toJson($merged));
		
		return $this->returnData(['message' => 'gagal meyimpan user.'], false);
	}

	/**
	 * delete model dari table
	 * return array / [] / null
	 */
	public function response_delete($id)
	{
		// cari model ada tidak
		$model = $this->query_find($this->table_name, $id);
		if (empty($model)) 
			return $this->returnData(['message' => 'model tidak ditemukan.'], false);
		
		// gas hapus model
		if ($this->query_delete($this->table_name, $id)) 
			return $this->returnData($this->toJson($model));
		
		return $this->returnData(['message' => 'gagal menghapus user.'], false);
	}


	/**
	 * ubah dari model menjadi array json dengan kolom tertentu.
	 * return array / [] / null
	 */
	public function toJson($model)
	{
		$data = [
			$this->column[0] => empty($this->id) ? $model->id : $this->id,
			$this->column[1] => empty($this->name) ? $model->name : $this->name,
			$this->column[3] => empty($this->email) ? $model->email : $this->email,
			$this->column[4] => empty($this->alamat) ? $model->alamat : $this->alamat,
			$this->column[5] => empty($this->no_telp) ? $model->no_telp : $this->no_telp,
			$this->column[6] => empty($this->stat_admin) ? $model->stat_admin : $this->stat_admin,
		];
		return $data;
	}
}