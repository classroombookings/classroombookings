<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms_model extends CI_Model
{


	const FIELD_CHECKBOX = 'CHECKBOX';
	const FIELD_SELECT = 'SELECT';
	const FIELD_TEXT = 'TEXT';


	protected $table = 'rooms';


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
	 * Get list of bookable rooms based on user's permissions.
	 *
	 * "Bookable" where:
	 * - Room property "can be booked" = yes
	 * - User has permission via Access Control entries.
	 *
	 */
	public function get_bookable_rooms($for_user_id = NULL, $room_id = NULL)
	{
		$out = [];

		$for_user_id = (int) $for_user_id;

		$this->db->reset_query();

		$this->db->select('rooms.*');
		$this->db->select([
			'owner.user_id AS owner__user_id',
			'owner.username AS owner__username',
			'owner.displayname AS owner__displayname'
		], FALSE);

		$this->db->from($this->table);
		$this->db->join('users AS owner', 'user_id', 'LEFT');
		$this->db->join('users AS actor', sprintf('actor.user_id = %d', $for_user_id), 'INNER');

		$permission = Access_control_model::ACCESS_VIEW;
		$subquery = $this->access_control_model->get_rooms_subquery($for_user_id, $permission);
		$this->db->join("({$subquery}) accessible_rooms", 'rooms.room_id = accessible_rooms.target_id', 'LEFT');

		$this->db->where([
			'bookable' => 1,
		]);

		if (strlen($room_id)) {
			$this->db->where([
				'rooms.room_id' => (int) $room_id,
			]);
		}

		$this->db->group_start()
			->where(['actor.authlevel' => ADMINISTRATOR])
			->or_where('accessible_rooms.target_id IS NOT NULL')
			->group_end();

		$this->db->order_by('name ASC');
		$this->db->group_by('room_id');

		$query = $this->db->get();

		// Return row for specific room
		//
		if (strlen($room_id)) {
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




	/**
	 * Retrieve all rooms
	 *
	 * @return				array				All items in table
	 *
	 */
	function Get($room_id = NULL)
	{
		$this->db->select(
			'rooms.*,'
			.'users.user_id,'
			.'users.username,'
			.'users.displayname'
		);
		$this->db->from('rooms');
		$this->db->join('users', 'users.user_id = rooms.user_id', 'left');

		if ($room_id != NULL) {
			// Getting one specific room
			$this->db->where('room_id', $room_id);
			$this->db->limit('1');
			$query = $this->db->get();
			if ($query->num_rows() == 1) {
				return $query->row();
			} else {
				return FALSE;
			}
		} else {
			$this->db->order_by('name asc');
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			} else {
				return FALSE;
			}
		}
	}


	public function room_info($room)
	{
		if ( ! is_object($room)) {
			$room = $this->Get( (int) $room);
		}

		$fields = $this->GetFields();
		$field_values = $this->GetFieldValues($room->room_id);

		$info = [];

		if ($room->location) {
			$info[] = [
				'name' => 'location',
				'label' => 'Location',
				'value' => html_escape($room->location),
			];
		}

		// User
		if ($room->displayname == '' ) {
			$room->displayname = $room->username;
		}
		if ($room->displayname) {
			$info[] = [
				'name' => 'teacher',
				'label' => 'Teacher',
				'value' => html_escape($room->displayname),
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




	function add($data)
	{
		// Run query to insert blank row
		$this->db->insert('rooms', array('room_id' => NULL));

		// Get id of inserted record
		$room_id = $this->db->insert_id();

		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->edit($room_id, $data);

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




	function edit($room_id, $data)
	{
		$this->db->where('room_id', $room_id);
		$result = $this->db->update('rooms', $data);
		// Return bool on success
		if ($result){
			return true;
		} else {
			return false;
		}
	}




	/**
	 * Deletes a room with the given ID
	 *
	 * @param   int   $id   ID of room to delete
	 *
	 */
	function delete($id)
	{
		$this->delete_photo($id);

		$this->access_control_model->delete_where([
			'target' => Access_control_model::TARGET_ROOM,
			'target_id' => $id,
		]);

		$this->db->where('room_id', $id);
		return $this->db->delete('rooms');
	}





	/**
	 * Deletes a photo
	 *
	 */
	function delete_photo($room_id)
	{
		$row = $this->Get($room_id);
		$photo = $row->photo;
		$file_path = FCPATH . "uploads/{$photo}";
		if (file_exists($file_path)) {
			@unlink($file_path);
		}
		$this->db->where('room_id', $room_id);
		$this->db->update('rooms', array('photo' => ''));
		return TRUE;
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
