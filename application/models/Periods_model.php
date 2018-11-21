<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once(APPPATH . 'third_party/bitmask.php');
require_once(APPPATH . 'third_party/simple_bitmask.php');

class Periods_model extends CI_Model
{


	public $days = array();
	public $days_bitmask;


	public function __construct()
	{
		parent::__construct();

		$this->days[1] = 'Monday';
		$this->days[2] = 'Tuesday';
		$this->days[3] = 'Wednesday';
		$this->days[4] = 'Thursday';
		$this->days[5] = 'Friday';
		$this->days[6] = 'Saturday';
		$this->days[7] = 'Sunday';
	}




	function Get($period_id = NULL)
	{
		if ($period_id == NULL) {
			$rows = $this->crud_model->Get('periods', NULL, NULL, NULL, 'time_start asc');
			foreach ($rows as &$row) {
				$this->populate_row($row);
			}
			return $rows;
		} else {
			$row = $this->crud_model->Get('periods', 'period_id', $period_id);
			return $this->populate_row($row);
		}
	}



	public function GetBookable($day_num = NULL)
	{
		$out = array();

		$sql = "SELECT * FROM periods WHERE bookable = 1 ORDER BY time_start ASC";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {

			$result = $query->result();

			foreach ($result as &$row) {

				$this->populate_row($row);

				if ($day_num === NULL) {
					$out[ $row->period_id ] = $row;
					continue;
				}

				if ($day_num !== NULL && $row->days_array[ $day_num ]) {
					$out[ $row->period_id ] = $row;
					continue;
				}
			}
		}

		return $out;
	}



	public function populate_row($row)
	{
		$bitmask = new SimpleBitmask(array_keys($this->days));
		$row->days_array = $bitmask->getOptions($row->days);
		return $row;
	}




	function Add($data)
	{
		if (is_array($data['days'])) {
			$bitmask = new SimpleBitmask(array_keys($this->days));
			foreach ($data['days'] as $num) {
				$bitmask->options[ $num ] = TRUE;
			}
			$data['days'] = $bitmask->toBitmask();
		}

		return $this->crud_model->Add('periods', 'period_id', $data);
	}




	function Edit($period_id, $data)
	{
		if (is_array($data['days'])) {
			$bitmask = new SimpleBitmask(array_keys($this->days));
			foreach ($data['days'] as $num) {
				$bitmask->options[ $num ] = TRUE;
			}
			$data['days'] = $bitmask->toBitmask();
		}

		return $this->crud_model->Edit('periods', 'period_id', $period_id, $data);
	}




	/**
	 * Deletes a period with the given ID
	 *
	 * @param   int   $id   ID of period to delete
	 *
	 */
	function Delete($id)
	{
		return $this->crud_model->Delete('periods', 'period_id', $id);
	}




}
