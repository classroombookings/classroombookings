<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms_model extends CI_Model
{


	const FIELD_CHECKBOX = 'CHECKBOX';
	const FIELD_SELECT = 'SELECT';
	const FIELD_TEXT = 'TEXT';


	protected $table = 'rooms';
	protected $primary_key = 'room_id';


	public $options = array();


	public function __construct()
	{
		parent::__construct();

		$this->load->model('access_control_model');

		$this->options[self::FIELD_TEXT] = 'Text';
		$this->options[self::FIELD_CHECKBOX] = 'Checkbox';
		$this->options[self::FIELD_SELECT] = 'Dropdown list';
	}


	/**
	 * Get list of bookable rooms based on criteria in $params
	 *
	 * Params should have:
	 * - $user_id [required]
	 * - $room_group_id [optional, only rooms in this group]
	 * - $room_id [to filter for one room]
	 *
	 * "Bookable" where:
	 * - Room property "can be booked" = yes
	 * - User has permission via Access Control entries.
	 *
	 */
	public function get_bookable_rooms(array $params)
	{
		$for_user_id = (isset($params['user_id']))
			? (int) $params['user_id']
			: NULL;

		$room_group_id = (isset($params['room_group_id']))
			? (int) $params['room_group_id']
			: NULL;

		$room_id = (isset($params['room_id']))
			? (int) $params['room_id']
			: NULL;

		$out = [];

		// Get the access control EXISTS query to filter the rooms
		$permission = Access_control_model::ACCESS_VIEW;
		$exists_sql = $this->access_control_model->get_rooms_exists($for_user_id, $permission, 'rooms.room_id');
		$where_exists = sprintf('EXISTS (%s)', $exists_sql);

		$this->db->reset_query();

		$this->db->select('rooms.*');
		$this->db->select([
			'owner.user_id AS owner__user_id',
			'owner.username AS owner__username',
			'owner.displayname AS owner__displayname'
		], FALSE);

		$this->db->select([
			'rooms.room_group_id',
			'rg.name AS group__name',
			'rg.description AS group__description',
		], FALSE);

		$this->db->from($this->table);
		$this->db->join('users AS owner', 'user_id', 'LEFT');
		$this->db->join('users AS actor', sprintf('actor.user_id = %d', $for_user_id), 'INNER');
		$this->db->join('room_groups AS rg', 'room_group_id', 'LEFT');

		$this->db->where($where_exists);
		$this->db->where('bookable', 1);

		if ($room_id !== NULL) {
			$this->db->where('rooms.room_id', $room_id);
		}

		if ($room_group_id !== NULL) {
			$this->db->where('rooms.room_group_id', $room_group_id);
		} else {
			// Need to ensure that the room has a group - for loading the schedule.
			// Otherwise, it wouldn't be bookable anyway.
			$this->db->where('rooms.room_group_id IS NOT NULL');
		}

		$this->db->order_by('rg.pos', 'asc');
		$this->db->order_by('rooms.pos', 'asc');
		$this->db->order_by('rooms.name', 'asc');

		$this->db->group_by('room_id');

		$query = $this->db->get();

		// Return row for specific room
		//
		if (!empty($room_id)) {
			if ($query->num_rows() == 0) return FALSE;
			return nest_object_keys($query->row());
		}

		// Return for all rooms
		//

		if ($query->num_rows() == 0) return $out;

		$result = $query->result();
		foreach ($result as &$row) {
			$out[ $row->room_id] = nest_object_keys($row);
		}

		return $out;
	}


	public function get_all_grouped()
	{
		$out = [];

		$all_rooms = $this->get_all();
		if ( ! is_array($all_rooms)) return $out;

		foreach ($all_rooms as $room) {
			$key = empty($room->room_group_id)
				? 'ungrouped'
				: $room->room_group_id;

			$out[$key][] = $room;
		}

		return $out;
	}


	public function get_all()
	{
		$this->db->reset_query();
		$this->build_rooms_query();

		if ($this->db->field_exists('pos', 'rooms')) {
			$this->db->order_by("{$this->table}.pos", 'ASC');
		}

		$this->db->order_by("{$this->table}.name", 'ASC');

		$query = $this->db->get();
		if ($query->num_rows() == 0) return FALSE;

		$result = $query->result();
		foreach ($result as &$row) {
			$row = $this->wake_value($row);
		}

		return $result;
	}


	public function get_in_group($room_group_id)
	{
		$this->db->reset_query();
		$this->build_rooms_query();
		$this->db->where($this->table.'.room_group_id', $room_group_id);
		$this->db->order_by("{$this->table}.pos", 'ASC');
		$this->db->order_by("{$this->table}.name", 'ASC');

		$query = $this->db->get();
		if ($query->num_rows() == 0) return FALSE;

		$result = $query->result();
		foreach ($result as &$row) {
			$row = $this->wake_value($row);
		}

		return $result;
	}


	public function get_by_id($id)
	{
		$this->db->reset_query();
		$this->build_rooms_query();
		$this->db->where($this->primary_key, $id);
		$this->db->limit(1);

		$query = $this->db->get();
		if ($query->num_rows() == 0) return FALSE;

		$row = $query->first_row();
		$row = $this->wake_value($row);
		return $row;
	}


	public function update_pos($data)
	{
		return $this->db->update_batch($this->table, $data, 'room_id');
	}


	public function update_batch(array $values, $index)
	{
		return $this->db->update_batch($this->table, $values, $index);
	}


	private function build_rooms_query()
	{
		$this->db->select([
			'rooms.*',
		]);

		$this->db->select([
			"u.user_id AS 'owner.user_id'",
			"u.username AS 'owner.username'",
			"u.displayname AS 'owner.displayname'",
		]);

		if ($this->db->table_exists('room_groups')) {
			$this->db->select([
				"rg.name AS 'group.name'",
				"rg.description AS 'group.description'",
				"rg.pos AS 'group.pos'",
			]);
			$this->db->join('room_groups rg', 'rg.room_group_id = rooms.room_group_id', 'left');
		}


		$this->db->from('rooms');

		$this->db->join('users u', 'u.user_id = rooms.user_id', 'left');

		return $this;
	}


	public function wake_value($row)
	{
		$row = nest_object_keys($row);
		return $row;
	}


	public function sleep_values($data)
	{
		return $data;
	}


	public function room_info($room)
	{
		if ( ! is_object($room)) {
			$room = $this->get_by_id( (int) $room);
		}

		$fields = $this->GetFields();
		$field_values = $this->GetFieldValues($room->room_id);

		$info = [];

		if (feature('room_groups') && $room->room_group_id) {
			$info[] = [
				'name' => 'group',
				'label' => 'Group',
				'value' => html_escape($room->group->name),
			];
		}


		if ($room->location) {
			$info[] = [
				'name' => 'location',
				'label' => 'Location',
				'value' => html_escape($room->location),
			];
		}

		// User
		if ( ! empty($room->user_id)) {
			$info[] = [
				'name' => 'teacher',
				'label' => 'Teacher',
				'value' => html_escape($room->owner->displayname),
			];
		}

		if ($room->notes) {
			$info[] = [
				'name' => 'notes',
				'label' => 'Notes',
				'value' => html_escape($room->notes),
			];
		}

		if (empty($fields)) {
			return $info;
		}

		foreach ($fields as $field) {

			$field_value = NULL;

			switch ($field->type) {
				case 'TEXT':
					$field_value = html_escape($field_values[$field->field_id]);
				break;

				case 'CHECKBOX':
					$val = boolval($field_values[$field->field_id]);
					if ($val) {
						$img_src = base_url('assets/images/ui/enabled.png');
						$alt = 'Yes';
					} else {
						$img_src = base_url('assets/images/ui/no.png');
						$alt = 'No';
					}
					$field_value = "<img src='{$img_src}' alt='{$alt}' up-tooltip='{$alt}' width='16' height='16'>";
				break;

				case 'SELECT':
					foreach ($field->options as $option) {
						if ($option->option_id == $field_values[$field->field_id]) {
							$field_value = html_escape($option->value);
							break;
						}
					}
				break;
			}

			$info[] = [
				'name' => "field_{$field->name}",
				'label' => $field->name,
				'value' => $field_value,
			];

		}

		return $info;
	}




	/**
	 * Gets all information on the room - joins all the fields as well
	 *
	 */
	function GetInfo($room_id)
	{
		$data = array();

		// Query for room
		//

		$this->db->select(array(
			'rooms.*',
			'users.user_id',
			'users.username',
			'users.displayname',
		));

		$this->db->from('rooms');
		$this->db->join('users', 'users.user_id = rooms.user_id', 'left');
		$this->db->where('rooms.room_id', $room_id);

		$query = $this->db->get();

		$data['room'] = $query->row();

		// Query for fields data
		//

		$this->db->select(array(
			'roomfields.*',
			'roomoptions.*',
			'roomvalues.*',
		));

		$this->db->from('roomvalues');
		$this->db->join('roomoptions', 'roomoptions.field_id = roomvalues.field_id', 'left');
		$this->db->join('roomfields', 'roomfields.field_id = roomvalues.field_id');
		$this->db->where('roomvalues.room_id', $room_id);

		$query = $this->db->get();

		$data['fields'] = $query->result();

		return $data;
	}




	/**
	 * Gets room ID and name of one room owned by the given user id
	 *
	 * @param	int	$school_id	School ID
	 * @param	int	$user_id	ID of user to lookup
	 * @return	mixed	object if result, false on no results
	 *
	 */
	function GetByUser($user_id)
	{
		$user_id = (int) $user_id;

		$sql = "SELECT room_id, name
				FROM rooms
				WHERE user_id={$user_id}
				ORDER BY name
				LIMIT 1";

		$query = $this->db->query($sql);

		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return FALSE;
		}
	}




	function insert($data)
	{
		// Run query to insert blank row
		$this->db->insert('rooms', $data);

		// Get id of inserted record
		$room_id = $this->db->insert_id();

		// Add initial access control
		$access_control_data = array(
			'target' => Access_control_model::TARGET_ROOM,
			'target_id' => $room_id,
			'actor' => Access_control_model::ACTOR_AUTHENTICATED,
			'actor_id' => NULL,
			'permission' => Access_control_model::ACCESS_VIEW,
		);

		$entry_id = $this->access_control_model->add_entry($access_control_data);

		return $room_id;
	}




	function update($room_id, $data)
	{
		$where = ['room_id' => $room_id];
		return $this->db->update('rooms', $data, $where);
	}




	/**
	 * Deletes a room with the given ID
	 *
	 * @param   int   $id   ID of room to delete
	 *
	 */
	function delete($room_id)
	{
		$this->delete_photo($room_id);

		$this->access_control_model->delete_where([
			'target' => Access_control_model::TARGET_ROOM,
			'target_id' => $room_id,
		]);

		return $this->db->delete('rooms', ['room_id' => $room_id]);
	}





	/**
	 * Deletes a photo
	 *
	 */
	function delete_photo($room_id)
	{
		$row = $this->get_by_id($room_id);
		if ( ! $row) return false;

		$photo = $row->photo;
		if (empty($photo)) return false;

		$res = delete_user_file($photo);
		if ( ! $res) return false;

		return $this->update($room_id, ['photo' => null]);
	}





	/**
	 * Get room fields
	 *
	 */
	function GetFields($field_id = NULL)
	{
		$this->db->select('*');
		$this->db->from('roomfields');

		if ($field_id != NULL) {

			// Getting one specific field
			$this->db->where('roomfields.field_id', $field_id);
			$this->db->limit('1');
			$query = $this->db->get();

			if ($query->num_rows() == 1) {
				// One row, match!
				$row = $query->row();
				$row->options = $this->GetOptions($field_id);
				return $row;
			} else {
				// None
				return FALSE;
			}

		} else {

			// Getting all
			$this->db->order_by('roomfields.type asc, roomfields.name asc');
			$query = $this->db->get();

			if ($query->num_rows() > 0) {

				// Got some rooms, return result
				$result = $query->result();

				foreach ($result as $item) {
					if ($item->type == self::FIELD_SELECT) {
						$item->options = $this->GetOptions($item->field_id);
					}
				}

				return $result;
			} else {
				// No rooms!
				return FALSE;
			}
		}
	}




	function field_add($data)
	{
		// Run query to insert blank row
		$this->db->insert('roomfields', array('field_id' => NULL));
		// Get id of inserted record
		$field_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->field_edit($field_id, $data);
		return $field_id;
	}




	function field_edit($field_id, $data)
	{
		// We don't add the options column to the roomfields table, so get it then remove it from the array that gets added
		$options = $data['options'];
		unset($data['options']);

		$this->db->where('field_id', $field_id);
		$result = $this->db->update('roomfields', $data);

		// Delete row options of the field
		//		We don't yet know if the type is a SELECT, but we delete
		//		them first anyway incase they changed it
		//		from a SELECT to something else. The new options get inserted next.
		$this->delete_field_options($field_id);

		if ($data['type'] == self::FIELD_SELECT) {

			// Explode at newline into array
			$arr_options = explode("\n", $options);

			// Loop through options and insert a new row for each one
			foreach ($arr_options as $key => $value) {
				$this->db->insert('roomoptions', array(
					'field_id' => $field_id,
					'value' => $value,
				));
			}

		}

		return ($result ? TRUE : FALSE);
	}




	/**
	 * Deletes a field with the given ID
	 *
	 * @param   int   $id   ID of field to delete
	 *
	 */
	function field_delete($id)
	{
		$this->db->where('field_id', $id);
		$this->db->delete('roomfields');
		$this->db->where('field_id', $id);
		$this->db->delete('roomvalues');
	}




	/**
	 * Get options for a field
	 *
	 */
	function GetOptions($field_id)
	{
		$this->db->select('*');
		$this->db->from('roomoptions');
		$this->db->order_by('value asc');
		$this->db->where('field_id', $field_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}




	/**
	 * Delete all options for a given field
	 *
	 */
	function delete_field_options($field_id)
	{
		$this->db->where('field_id', $field_id);
		return $this->db->delete('roomoptions');
	}





	function save_field_values($room_id, $data)
	{
		$this->db->where('room_id', $room_id);
		$this->db->delete('roomvalues');

		foreach ($data as $field_id => $value) {
			$this->db->insert('roomvalues', array(
				'room_id' => $room_id,
				'field_id' => $field_id,
				'value' => $value,
			));
		}

		return TRUE;
	}




	function GetFieldValues($room_id)
	{
		$this->db->select('field_id, value');
		$this->db->from('roomvalues');
		$this->db->order_by('value_id asc');
		$this->db->where('room_id', $room_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			$values = array();
			foreach ($result as $item) {
				$values[$item->field_id] = $item->value;
			}
			return $values;
		} else {
			return array();
		}
	}




}
