<?php

abstract class base_model extends CI_Model
{
	private $table_name = '';
	public function __construct()
	{
		$this->load->library('form_validation');
	}

	/**
	 * Dapatkan semua isi table tersebut, $table adalah nama table.
	 * return array / null
	 */
	public function query_all($table)
	{
		// select all
		$query = $this->db->get($table);
		$results = $query->result();
		if (is_null($results)) return null;
		// tampilkan data yang diinginkan
		$data = [];
		foreach ($results as $model) {
			array_push($data, $this->toJson($model));
		}
		return $data;
	}

	/**
	 * Dapatkan satu model dengan id yang sama dari table tertentu,
	 * $table adalah nama table, $id adalah id model.
	 * return model / null
	 */
	public function query_find($table, $id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get($table);
		$row = $query->row();
		if (empty($row)) return null;
		return $row;
	}

	/**
	 * simpan model ke table tertentu,
	 * $table adalah nama table, $arrData adalah nama kolom dan isinya.
	 * return boolean
	 */
	public function query_store($table)
	{
		$sql = $this->db->insert($table, $this);
		return $sql;
	}

	/**
	 * edit model ,
	 * $table adalah nama table, $arrData adalah nama kolom dan isinya, where untuk where clause.
	 * return boolean
	 */
	public function query_edit($table, $arrData, $where)
	{
		$sql = $this->db->update_string($table, $arrData, $where);
		$query = $this->db->query($sql);
		return $query;
	}

	public function query_delete($table, $id)
	{
		$query = $this->db->delete($table, array('id' => $id));
		return $query;
	}

	/**
	 * cek apitoken ada atau tidak
	 * return 0 / id user
	 */
	public function apitoken_exists()
	{
		// cek header authorization
		$token_full = $this->input->get_request_header('Authorization');
		$splited = explode(' ', $token_full);
		// cek array hasil split
		if (count($splited) < 2) return 0;
		$type = $splited[0];
		$token = $splited[1];
		// cek type credential
		if ($type != 'Bearer') return 0;
		//cari api token yang sama
		$this->db->where('api_token', $token);
		$query = $this->db->get('users', 1);
		$row = $query->row();
		if (empty($row)) return 0;
		return $row->id;
	}

	/**
	 * mempermudah mengirimkan return data untuk controller
	 * return ["succes" => ?, "data" => ?]
	 */
	public function returnData($data, $success = true)
	{
		if ($success) return ["success" => $success, "data" => $data];
		return ["success" => $success, "data" => ["error" => $data]];
	}

	/**
	 * abstract untuk toJson 	 
	 * ubah dari model menjadi array json dengan kolom tertentu.
	 * return array / [] / null
	 */
	abstract function toJson($model);
}