<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{


	protected $_table_name = 'settings';

	private $_cache = array();


	public function __construct()
	{
		parent::__construct();
	}


	public function get($name, $group = 'crbs')
	{
		if ( ! config_item('is_installed')) return FALSE;

		if ( ! array_key_exists($group, $this->_cache) || empty($this->_cache[$group])) {
			$this->get_all($group);
		}

		$value = $this->_cache[$group][$name] ?? FALSE;
		return $value;
	}


	public function get_all($group = null)
	{
		if ( ! config_item('is_installed')) return FALSE;

		$this->db->reset_query();

		$where = array();

		if (!empty($group)) {
			$where = array('group' => $group);
		}

		$query = $this->db->where($where)->get($this->_table_name);

		if ($query->num_rows() == 0) return array();

		$result = $query->result();
		$out = array();
		foreach ($result as $row) {
			$value = $this->wake_value($row->value);
			$out[ $row->group ][ $row->name ] = $value;
			$this->_cache[ $row->group ][ $row->name ] = $value;
		}

		if (!empty($group)) {
			return $out[ $group ];
		}

		return $out;
	}


	/**
	 * Set one or more settings values
	 *
	 */
	public function set($key, $value = null, $group = 'crbs')
	{
		if (is_array($key) && ! empty($key)) {

			// Set multi
			//

			if (!is_null($value)) {
				$group = $value;
			}

			$this->db->where_in('name', array_keys($key));
			$this->db->where('group', $group);
			$this->db->delete($this->_table_name);

			$data = array();

			foreach ($key as $name => $value) {
				$data[] = array(
					'group' => $group,
					'name' => $name,
					'value' => $this->sleep_value($value),
				);
			}

			$this->_cache[$group] = [];

			return $this->db->insert_batch('settings', $data);

		} elseif (is_string($key) && ! empty($key)) {

			// Single key => value
			//

			$value = $this->sleep_value($value);

			$data = array(
				'name' => $key,
				'value' => $value,
				'group' => $group
			);

			$this->_cache[$group] = [];

			return $this->db->replace($this->_table_name, $data);
		}
	}


	public function delete($key, $group = 'crbs')
	{
		$where = array(
			'name' => $key,
			'group' => $group,
		);

		$this->_cache[$group] = [];

		return $this->db->delete($this->_table_name, $where);
	}


	private function wake_value($value)
	{
		if (substr($value, 0, 4) === 'b64:') {
			$value = substr($value, 4);
			$value = base64_decode($value);
		}

		$data = @unserialize($value);

		if ($value === 'b:0;' || $data !== FALSE)
		{
			$value = $data;
		}

		return $value;
	}


	private function sleep_value($value)
	{
		if (is_array($value) || is_object($value))
		{
			$value = serialize($value);
			$value = 'b64:' . base64_encode($value);
		}

		return $value;
	}


}
