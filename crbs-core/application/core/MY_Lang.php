<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'third_party/MX/Lang.php';

class MY_Lang extends MX_Lang
{


	protected $CI = NULL;

	protected string $db_table = 'lang';
	protected array $db_lang = [];
	protected array $db_loaded = [];
	protected $db_lang_exists = null;
	protected $db_lang_count = null;


	public function __construct()
	{
		parent::__construct();
	}


	public function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '', $_module = '')
	{
		if (is_array($langfile)) {
			foreach ($langfile as $value) {
				$this->load($value, $idiom, $return, $add_suffix, $alt_path);
			}
			return;
		}

		$result = parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);

		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom)) {
			$config =& get_config();
			$idiom = empty($config['language']) ? 'english' : $config['language'];
		}

		$db_result = $this->load_from_db($langfile, $idiom);

		if (is_array($result) && $return === true) {
			$return_value = array_merge($result, $db_result);
			return $return_value;
		}

		$this->language = array_merge($this->language, $db_result);
		return true;
	}


	public function load_from_db($langfile, $idiom)
	{
		$lang = [];

		$this->set_instance();

		// No DB to load
		if ( ! isset($this->CI->db)) return $lang;
		if ( ! $this->CI->db) return $lang;

		// Not sure if table exists?
		if (is_null($this->db_lang_exists)) {
			$this->db_lang_exists = $this->CI->db->table_exists($this->db_table);
		}
		// DB table not present.
		if ($this->db_lang_exists === false) return $lang;
		// Check count of DB language entries
		if (is_null($this->db_lang_count)) {
			$sql = "SELECT COUNT(id) AS `total` FROM {$this->db_table}";
			$query = $this->CI->db->query($sql);
			$row = $query->row();
			$this->db_lang_count = $row->total;
		}
		// No DB langs to load
		if ($this->db_lang_count == 0) return $lang;

		$this->populate_from_db($idiom);

		return $this->db_lang[$idiom][$langfile] ?? $lang;
	}


	private function populate_from_db($idiom)
	{
		// Already loaded
		if (isset($this->db_lang[$idiom])) return;

		$sql = "SELECT `set`, `key`, `text` FROM `{$this->db_table}` WHERE `language` = ?";
		$query = $this->CI->db->query($sql, [ $idiom ]);
		if ( ! $query) return;
		if ($query->num_rows() === 0) return;

		foreach ($query->result_array() as $row) {
			$this->db_lang[ $idiom ][ $row['set'] ][ $row['key'] ] = html_escape($row['text']);
		}

		return;
	}


	public function get_languages()
	{
		$langs = ['english'];

		$core_path = APPPATH.'language';

		$custom_paths = [];

		if (CRBS_MANAGED) {
			$custom_paths[] = ROOTPATH.'crbs-managed/language';
		}
		if (!CRBS_MANAGED) {
			$custom_paths[] = ROOTPATH.'local/language';
		}

		foreach ($custom_paths as $path) {

			if ( ! $fp = @opendir($path)) {
				continue;
			}

			while (false !== ($entry = readdir($fp))) {
				if ($entry === '.' OR $entry === '..' OR ($entry[0] === '.')) {
					continue;
				}

				// Ensure we only allow languages that are in core + the custom path
				$core_exists = is_dir($core_path.DIRECTORY_SEPARATOR.$entry);
				$custom_exists = is_dir($path.DIRECTORY_SEPARATOR.$entry);
				if ($core_exists && $custom_exists) {
					$langs[] = $entry;
					continue;
				}
			}

		}

		return $langs;
	}


	private function set_instance()
	{
		if ( ! $this->CI) {
			$this->CI =& get_instance();
		}
	}

}
