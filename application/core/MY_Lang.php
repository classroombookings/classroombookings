<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Lang extends CI_Lang
{


	protected $CI = NULL;

	protected $db_loaded = array();


	public function __construct()
	{
		parent::__construct();
	}


	public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		$result = parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);

		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
		{
			$config =& get_config();
			$idiom = empty($config['language']) ? 'english' : $config['language'];
		}

		$lang = $this->load_from_db($langfile, $idiom);

		if (is_array($result) && $return === TRUE) {
			$return_value = array_merge($result, $lang);
			return $return_value;
		}

		$this->language = array_merge($this->language, $lang);
		return TRUE;
	}


	public function load_from_db($set, $idiom)
	{
		$lang = [];

		if (isset($this->db_loaded[$idiom]) && $this->db_loaded[$idiom] === $set) {
			return $lang;
		}

		$this->set_instance();

		if ( ! isset($this->CI->db)) {
			return $lang;
		}

		if ( ! $this->CI->db->table_exists('lang')) {
			return $lang;
		}

		$where = [
			'language' => $idiom,
			'set' => $set,
		];

		$query = $this->CI->db->select('key,text')->where($where)->get('lang');
		$result = $query->result();

		foreach ($result as $row) {
			$lang[$row->key] = $row->text;
		}

		return $lang;
	}


	private function set_instance()
	{
		if ( ! $this->CI) {
			$this->CI =& get_instance();
		}
	}

}
