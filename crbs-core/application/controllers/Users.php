<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_USERS);

		$this->load->model([
			'crud_model',
			'users_model',
			'departments_model',
			'roles_model',
		]);

		$this->load->helper('number');
		$this->load->helper('result');

		$this->data['max_size_bytes'] = max_upload_file_size();
		$this->data['max_size_human'] = byte_format(max_upload_file_size());
	}


	private function get_icons()
	{
		$items = [
			['users', lang('user.users'), 'school_manage_users.png'],
			['users/import', lang('user.import_from_csv'), 'user_import.png'],
		];

		return $items;
	}


	private function get_constraints_config()
	{
		return [
			'max_active_bookings' => [
				'label' => lang('constraint.max_active_bookings'),
				'hint' => lang('constraint.max_active_bookings.hint'),
			],
			'range_min' => [
				'label' => lang('constraint.range_min'),
				'hint' => lang('constraint.range_min.hint'),
			],
			'range_max' => [
				'label' => lang('constraint.range_max'),
				'hint' => lang('constraint.range_max.hint'),
			],
			'recur_max_instances' => [
				'label' => lang('constraint.recur_max_instances'),
				'hint' => lang('constraint.recur_max_instances.hint'),
			],
		];
	}


	/**
	 * User account listing
	 *
	 */
	function index($page = null)
	{
		$this->load->library('table');

		$filter = [
			'limit' => 25,
			'offset' => $page,
			'search' => $this->input->get('q'),
			'role_id' => $this->input->get('role_id'),
			'department_id' => $this->input->get('department_id'),
			'sort' => $this->input->get('sort'),
		];

		$this->data['filter'] = $filter;

		$user_count = $this->users_model->count($filter);
		$users = $this->users_model->filtered($filter);
		$any = sprintf('(%s)', lang('app.any'));

		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['department_options'] = results_to_assoc($departments, 'department_id', 'name', $any);

		$roles = $this->roles_model->get_all();
		$this->data['role_options'] = results_to_assoc($roles, 'role_id', 'name', $any);

		$pagination_config = array(
			'base_url' => site_url('users/index'),
			'total_rows' => $user_count,
			'per_page' => $filter['limit'],
			'full_tag_open' => '<p class="pagination">',
			'full_tag_close' => '</p>',
		);

		$this->load->library('pagination');
		$this->pagination->initialize($pagination_config);

		$this->data['pagelinks'] = $this->pagination->create_links();

		$this->data['users'] = $users;
		$this->data['title'] = lang('user.users.title');
		$this->data['showtitle'] = $this->data['title'];

		$icons = iconbar($this->get_icons(), 'users');
		$this->data['body'] = $icons . $this->load->view('users/users_index', $this->data, TRUE);

		return up_target() ? $this->render_up() : $this->render();
	}


	/**
	 * Add a new user
	 *
	 */
	function add()
	{
		$none = sprintf('(%s)', lang('app.none'));

		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['department_options'] = results_to_assoc($departments, 'department_id', 'name', $none);

		$roles = $this->roles_model->get_all();
		$this->data['role_options'] = results_to_assoc($roles, 'role_id', 'name', $none);

		$this->data['constraints_config'] = $this->get_constraints_config();

		$this->data['title'] = lang('user.add.title');
		$this->data['showtitle'] = lang('user.users.title');

		if ($this->input->post()) {
			$this->save_user();
		}

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/users_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('users/users_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$title = "<h2>{$this->data['title']}</h2>";

		$icons = iconbar($this->get_icons(), 'users');
		$this->data['body'] = $icons . $title . $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	/**
	 * Edit user account
	 *
	 */
	function edit($id = NULL)
	{
		$this->data['user'] = $this->users_model->Get($id);

		if (empty($this->data['user'])) {
			show_404();
		}

		$this->data['constraints'] = $this->users_model->user_constraints_raw($id);

		$none = sprintf('(%s)', lang('app.none'));

		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['department_options'] = results_to_assoc($departments, 'department_id', 'name', $none);

		$roles = $this->roles_model->get_all();
		$this->data['role_options'] = results_to_assoc($roles, 'role_id', 'name', $none);

		$this->data['role'] = $this->roles_model->get($this->data['user']->role_id);

		$this->data['constraints_config'] = $this->get_constraints_config();

		$this->data['title'] = lang('user.edit.title');
		$this->data['showtitle'] = lang('user.users.title');

		if ($this->input->post()) {
			$this->save_user($id);
		}

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/users_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('users/users_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$title = "<h2>{$this->data['title']}</h2>";

		$icons = iconbar($this->get_icons(), 'users');
		$this->data['body'] = $icons . $title . $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	/**
	 * Save user details
	 *
	 */
	private function save_user($user_id = null)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('user_id', 'lang:app.id', 'integer');
		$this->form_validation->set_rules('role_id', 'lang:role.role', 'integer');
		$this->form_validation->set_rules('username', 'lang:user.field.username', 'required|max_length[32]|regex_match[/^[A-Za-z0-9-_.@]+$/]');
		$this->form_validation->set_rules('enabled', 'lang:user.field.enabled', 'required|integer');
		$this->form_validation->set_rules('email', 'lang:user.field.email', 'valid_email|max_length[255]');
		$this->form_validation->set_rules('force_password_reset', 'lang:user.field.force_password_reset', 'in_list[0,1]');

		$use_ldap = (setting('ldap_enabled', 'auth') == '1');

		if (is_null($user_id) && !$use_ldap) {
			// New users with no LDAP - require a password
			$this->form_validation->set_rules('password1', 'lang:user.field.password', 'trim|required');
			$this->form_validation->set_rules('password2', 'lang:user.field.password2', 'trim|matches[password1]');
		} else {
			if ($this->input->post('password1')) {
				$this->form_validation->set_rules('password1', 'lang:user.field.password', 'trim');
				$this->form_validation->set_rules('password2', 'lang:user.field.password2', 'trim|matches[password1]');
			}
		}

		$this->form_validation->set_rules('firstname', 'lang:user.field.firstname', 'max_length[20]');
		$this->form_validation->set_rules('lastname', 'lang:user.field.lastname', 'max_length[20]');
		$this->form_validation->set_rules('displayname', 'lang:user.field.displayname', 'max_length[20]');
		$this->form_validation->set_rules('department_id', 'lang:department.department', 'integer');
		$this->form_validation->set_rules('ext', 'lang:user.field.ext', 'max_length[10]');


		foreach ($this->get_constraints_config() as $id => $config) {
			extract($config);
			$field = "constraints[{$id}_type]";
			$this->form_validation->set_rules($field, "$label", 'required|in_list[R,X,U]');
			if ($this->input->post($field) == 'U') {
				$field = "constraints[{$id}_value]";
				$l = sprintf('%s (%s)', $label, strtolower(lang('app.value')));
				$this->form_validation->set_rules($field, $l, 'required|is_natural');
			}
		}

		if ($this->form_validation->run() == FALSE) {
			return false;
		}

		$user_data = array(
			'username' => $this->input->post('username'),
			'role_id' => $this->input->post('role_id'),
			'enabled' => $this->input->post('enabled'),
			'email' => $this->input->post('email'),
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'displayname' => $this->input->post('displayname'),
			'department_id' => $this->input->post('department_id'),
			'ext' => $this->input->post('ext'),
			'force_password_reset' => $this->input->post('force_password_reset'),
		);

		if ($this->input->post('password1') && $this->input->post('password2')) {
			$user_data['password'] = password_hash((string) $this->input->post('password1'), PASSWORD_DEFAULT);
		}

		if (is_null($user_id)) {

			$user_id = $this->users_model->insert($user_data);

			if ($user_id) {
				$line = sprintf(lang('user.create.success'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = lang('user.create.error');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->users_model->update($user_id, $user_data)) {
				$line = sprintf(lang('user.update.success'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = lang('user.update.error');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->users_model->save_constraints($user_id, $this->input->post('constraints'));

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('users/edit/'.$user_id);
	}


	/**
	 * Delete a user
	 *
	 */
	function delete($id)
	{
		$user = $this->users_model->Get($id);

		if ($id == $_SESSION['user_id']) {
			$flashmsg = msgbox('error', lang('user.error.no_delete_own_account'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		if ($this->input->post('id') == $id) {
			$ret = $this->users_model->Delete($this->input->post('id'));
			$line = sprintf(lang('user.delete.success'), $user->username);
			$flashmsg = msgbox('info', $line);
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		$this->data['action'] = 'users/delete/'.$id;
		$this->data['id'] = $id;
		$this->data['cancel'] = 'users';
		$this->data['text'] = lang('user.delete.warning');

		$this->data['title'] = sprintf(lang('user.delete.title'), $user->username);
		$this->data['showtitle'] = lang('user.users.title');

		$icons = iconbar($this->get_icons(), 'users');
		$title = "<h2>{$this->data['title']}</h2>";
		$this->data['body'] = $icons . $title . $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * First page of import.
	 * If GET, show the form. If POST, handle CSV upload + import.
	 *
	 */
	public function import()
	{
		if ($this->input->post('action') == 'import') {
			$this->process_import();
		}

		unset($_SESSION['import_results']);

		$this->data['title'] = lang('user.import.title');
		$this->data['showtitle'] = lang('user.users.title');

		$none = sprintf('(%s)', lang('app.none'));

		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['department_options'] = results_to_assoc($departments, 'department_id', 'name', $none);

		$roles = $this->roles_model->get_all();
		$this->data['role_options'] = results_to_assoc($roles, 'role_id', 'name', $none);

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/import/stage1', $this->data, TRUE),
				'width' => '40%',
			),
			'c2' => array(
				'content' => $this->load->view('users/import/stage1_side', $this->data, TRUE),
				'width' => '60%',
			),
		);

		$icons = iconbar($this->get_icons(), 'users/import');
		$this->data['body'] = $icons . $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	/**
	 * Show the results of the import.
	 *
	 * The results are stored in a temporary file, the filename
	 * of which is stored in the session.
	 *
	 */
	public function import_results()
	{
		if ( ! array_key_exists('import_results', $_SESSION)) {
			$flashmsg = msgbox('error', lang('user.import.error.no_data'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
		}

		$result = $_SESSION['import_results'] ?? null;
		if (empty($result)) {
			$flashmsg = msgbox('error', lang('user.import.error.no_results'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
		}

		$result = json_decode(base64_decode((string) $result));
		if ( ! is_array($result)) {
			$flashmsg = msgbox('error', lang('user.import.error.resuts_format'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
			unset($_SESSION['import_results']);
		}

		$this->data['result'] = $result;

		$this->data['title'] = lang('user.import.results.title');
		$this->data['showtitle'] = $this->data['title'];

		$icons = iconbar($this->get_icons(), 'users/import');
		$this->data['body'] = $icons . '<br>' . $this->load->view('users/import/stage2', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * When the CSV form is submitted, this is called to handle the file
	 * and process the lines.
	 *
	 */
	private function process_import()
	{
		$has_csv = (isset($_FILES['userfile'])
		              && isset($_FILES['userfile']['name'])
		              && ! empty($_FILES['userfile']['name']));

		if ( ! $has_csv) {
			$notice = msgbox('exclamation', lang('user.import.error.no_file'));
			$this->data['notice'] = $notice;
			return FALSE;
		}

		$this->load->helper('file');
		$this->load->helper('string');

		$upload_config = array(
			'upload_path' => config_item('temp_path'),
			'allowed_types' => 'csv',
			'max_size' => $this->data['max_size_bytes'],
			'encrypt_name' => true,
		);

		$this->load->library('upload', $upload_config);

		// Default values supplied in form
		$defaults = array(
			'password' => $this->input->post('password'),
			'role_id' => $this->input->post('role_id'),
			'department_id' => $this->input->post('department_id'),
			'enabled' => $this->input->post('enabled'),
			'force_password_reset' => $this->input->post('force_password_reset'),
		);

		if ( ! $this->upload->do_upload()) {
			$error = $this->upload->display_errors('','');
			$this->data['notice'] = msgbox('error', $error);
			return FALSE;
		}

		$data = $this->upload->data();

		$file_path = $data['full_path'];
		$results = array();
		$handle = fopen($file_path, 'r');
		$line = 0;

		// Flip lookups
		//
		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$departments_by_name = results_to_assoc($departments, 'name', 'department_id');

		$roles = $this->roles_model->get_all();
		$roles_by_name = results_to_assoc($roles, 'name', 'role_id');

		// Parse CSV file
		//
		while (($row = fgetcsv($handle, filesize($file_path), ',', escape: '\\')) !== FALSE) {

			$first = strtolower((string) $row[0]) ?? '';

			if ($first == 'username' || $first == strtolower(lang('user.field.username'))) {
				$line++;
				continue;
			}

			$first_name = $this->get_value($row[1] ?? '');
			$last_name = $this->get_value($row[2] ?? '');
			$full_name = trim("{$first_name} {$last_name}");

			$role_id = null;
			if (!empty($defaults['role_id'])) {
				$role_id = $defaults['role_id'];
			}
			$role = $this->get_value($row[5] ?? null);
			if (!is_null($role) && $role !== '' && array_key_exists($role, $roles_by_name)) {
				$role_id = $roles_by_name[$role];
			}

			$department_id = null;
			if (!empty($defaults['department_id'])) {
				$department_id = $defaults['department_id'];
			}
			$department = $this->get_value($row[6] ?? null);
			if (!is_null($department) && $department !== '' && array_key_exists($department, $departments_by_name)) {
				$department_id = $departments_by_name[$department];
			}

			$force_password_reset = $this->get_value($row[5] ?? null) ?? $defaults['force_password_reset'] ?? 0;
			$force_password_reset_value = filter_var($force_password_reset, FILTER_VALIDATE_BOOLEAN);

			$user = array(
				'username' => $this->get_value($row[0] ?? null),
				'firstname' => $first_name,
				'lastname' => $last_name,
				'email' => $this->get_value($row[3] ?? null),
				'password' => $this->get_value($row[4] ?? null),
				'enabled' => $defaults['enabled'],
				'role_id' => $role_id,
				'department_id' => $department_id,
				'ext' => null,
				'displayname' => $full_name ?? null,
				'force_password_reset' => ($force_password_reset_value == true ? 1 : 0),
			);

			if (is_null($user['password']) || $user['password'] == '') {
				$user['password'] = $defaults['password'];
			}

			$status = $this->add_user($user);

			$results[] = array(
				'line' => $line,
				'status' => $status,
				'user' => $user,
			);

			$line++;

		}

		// Finish with CSV
		fclose($handle);
		@unlink($file_path);

		$_SESSION['import_results'] = base64_encode(json_encode($results));

		return redirect('users/import_results');
	}


	private function get_value($value)
	{
		if ($value === null) return $value;
		$value = trim($value);
		if ( ! strlen($value)) return null;
		return $value;
	}


	private function validate_import_user($user = array())
	{
		$this->load->library('form_validation');

		$rules = [
			[
				'field' => 'username',
				'label' => 'lang:user.field.username',
				'rules' => 'trim|required|max_length[32]|regex_match[/^[A-Za-z0-9-_.@]+$/]',
			],
			[
				'field' => 'firstname',
				'label' => 'lang:user.field.firstname',
				'rules' => 'trim|max_length[20]',
			],
			[
				'field' => 'lastname',
				'label' => 'lang:user.field.lastname',
				'rules' => 'trim|max_length[20]',
			],
			[
				'field' => 'email',
				'label' => 'lang:user.field.email',
				'rules' => 'valid_email|max_length[255]',
			],
			[
				'field' => 'password',
				'label' => 'lang:user.field.password',
				'rules' => 'required',
			],
			[
				'field' => 'role_id',
				'label' => 'lang:role.role',
				'rules' => 'is_natural_no_zero',
			],
			[
				'field' => 'department_id',
				'label' => 'lang:department.department',
				'rules' => 'is_natural_no_zero',
			],
		];

		$this->form_validation->reset_validation();
		$this->form_validation->set_data($user);
		$this->form_validation->set_rules($rules);

		return $this->form_validation->run();
	}


	/**
	 * Add a user row from the imported CSV file
	 *
	 * @return  string		Description of the status of adding the given user
	 *
	 */
	private function add_user($data = array())
	{
		if (empty($data['username'])) {
			return 'username_empty';
		}

		if (empty($data['password'])) {
			return 'password_empty';
		}

		if ( ! $this->validate_import_user($data)) {
			return 'invalid';
		}

		if ($this->_userexists($data['username'])) {
			return 'username_exists';
		}

		$data['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);

		$res = $this->users_model->insert($data);

		if ($res) {
			return 'success';
		} else {
			return 'db_error';
		}
	}


	private function _userexists($username)
	{
		$sql = "SELECT user_id FROM users WHERE username = ? LIMIT 1";
		$query = $this->db->query($sql, $username);
		if ($query->num_rows() == 1) {
			return true;
		} else {
			return false;
		}
	}


}
