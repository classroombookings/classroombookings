<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Weeks_model extends CI_Model
{


	protected $table = 'weeks';


	public function __construct()
	{
		parent::__construct();
		$this->load->model('dates_model');
		$this->load->helper('week');
		$this->load->helper('colour');
	}



	public function get_all()
	{
		$query = $this->db->from($this->table)
			->order_by('name', 'ASC')
			->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as &$row) {
				$row = $this->wake_value($row);
			}
			return $result;
		}

		return FALSE;
	}



	public function get($week_id)
	{
		$where = [ 'week_id' => $week_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}



	/**
	 * Add a new week.
	 *
	 */
	public function insert($data)
	{
		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		return $insert ? $this->db->insert_id() : FALSE;
	}


	/**
	 * Update a week with given data.
	 *
	 */
	public function update($week_id, $data)
	{
		$data = $this->sleep_values($data);

		$where = ['week_id' => $week_id];

		$update = $this->db->update($this->table, $data, $where, 1);

		return $update;
	}


	public function wake_value($row)
	{
		$bright = colour_brightness($row->bgcol);
		// Ignore the FG colour; now set automatically
		$row->fgcol = ($bright > 160 ? '#000000' : '#ffffff');

		// Add # so it's always present
		$row->bgcol = '#' . $row->bgcol;

		return $row;
	}


	public function sleep_values($data)
	{
		$data['bgcol'] = ltrim($data['bgcol'] ?? '', '#');
		$data['fgcol'] = ltrim($data['fgcol'] ?? '', '#');
		return $data;
	}


	/**
	 * Delete a single holiday
	 *
	 */
	public function delete($id)
	{
		$delete = $this->db->delete($this->table, ['week_id' => $id]);

		if ($delete) {
			$this->dates_model->clear('week_id', $id);
		}

		return $delete;
	}


}
