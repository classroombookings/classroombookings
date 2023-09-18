<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{




	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('users_model');
		$this->load->model('departments_model');
		$this->load->helper('number');

		$this->data['max_size_bytes'] = max_upload_file_size();
		$this->data['max_size_human'] = byte_format(max_upload_file_size());
	}




	/**
	 * User account listing
	 *
	 */
	function index($page = NULL)
	{
		$pp = 25;

		$q = $this->input->get('q');

		if (!empty($q)) {
			$users = $this->users_model->search($q);
			$user_count = $this->users_model->search($q, 'count');
		} else {
			$users = $this->users_model->Get(NULL, $pp, $page);
			$user_count = $this->crud_model->Count('users');
		}

		$pagination_config = array(
			'base_url' => site_url('users/index'),
			'total_rows' => $user_count,
			'per_page' => $pp,
			'full_tag_open' => '<p class="pagination">',
			'full_tag_close' => '</p>',
		);

		$this->load->library('pagination');
		$this->pagination->initialize($pagination_config);

		$this->data['pagelinks'] = $this->pagination->create_links();

		$this->data['users'] = $users;
		$this->data['title'] = 'Manage Users';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('users/users_index', $this->data, TRUE);

		return up_target() ? $this->render_up() : $this->render();
	}




	/**
	 * Add a new user
	 *
	 */
	function add()
	{
		$this->data['departments'] = $this->departments_model->Get(NULL, NULL, NULL);

		$this->data['title'] = 'Add User';
		$this->data['showtitle'] = $this->data['title'];

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

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

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

		$this->data['departments'] = $this->departments_model->Get(NULL, NULL, NULL);

		$this->data['title'] = 'Edit User';
		$this->data['showtitle'] = $this->data['title'];

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

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}





	/**
	 * Save user details
	 *
	 */
	function save()
	{
		$user_id = $this->input->post('user_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('user_id', 'ID', 'integer');
		$this->form_validation->set_rules('username', 'Username', 'required|max_length[32]|regex_match[/^[A-Za-z0-9-_.@]+$/]');
		$this->form_validation->set_rules('authlevel', 'Type', 'required|integer');
		$this->form_validation->set_rules('enabled', 'Enabled', 'required|integer');
		$this->form_validation->set_rules('email', 'Email address', 'valid_email|max_length[255]');

		if (empty($user_id)) {
			$this->form_validation->set_rules('password1', 'Password', 'trim|required');
			$this->form_validation->set_rules('password2', 'Password (confirm)', 'trim|matches[password1]');
		} else {
			if ($this->input->post('password1')) {
				$this->form_validation->set_rules('password1', 'Password', 'trim');
				$this->form_validation->set_rules('password2', 'Password (confirm)', 'trim|matches[password1]');
			}
		}

		$this->form_validation->set_rules('firstname', 'First name', 'max_length[20]');
		$this->form_validation->set_rules('lastname', 'Last name', 'max_length[20]');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[20]');
		$this->form_validation->set_rules('department_id', 'Department', 'integer');
		$this->form_validation->set_rules('ext', 'Extension', 'max_length[10]');

		if ($this->form_validation->run() == FALSE) {
			return (empty($user_id) ? $this->add() : $this->edit($user_id));
		}

		$department_id = $this->input->post('department_id')
			? $this->input->post('department_id')
			: NULL;

		$user_data = array(
			'username' => $this->input->post('username'),
			'authlevel' => $this->input->post('authlevel'),
			'enabled' => $this->input->post('enabled'),
			'email' => $this->input->post('email'),
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'displayname' => $this->input->post('displayname'),
			'department_id' => $department_id,
			'ext' => $this->input->post('ext'),
		);

		if ($this->input->post('password1') && $this->input->post('password2')) {
			$user_data['password'] = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
		}

		if (empty($user_id)) {

			$user_id = $this->users_model->Add($user_data);

			if ($user_id) {
				$line = sprintf($this->lang->line('crbs_action_added'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->users_model->Edit($user_id, $user_data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('users');
	}





	/**
	 * Delete a user
	 *
	 */
	function delete($id = NULL)
	{
		if ($this->input->post('id')) {
			$ret = $this->users_model->Delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		if ($id == $_SESSION['user_id']) {
			$flashmsg = msgbox('error', "You cannot delete your own user account.");
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		$this->data['action'] = 'users/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'users';
		$this->data['text'] = 'If you delete this user, all of their past and future bookings will also be deleted, and their rooms will no longer be owned by them.';

		$row = $this->users_model->Get($id);

		$this->data['title'] = 'Delete User ('.html_escape($row->username).')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

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

		$this->data['title'] = 'Import Users';
		$this->data['showtitle'] = $this->data['title'];
		// $this->data['body'] = $this->load->view('users/import/stage1', NULL, TRUE);

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/import/stage1', $this->data, TRUE),
				'width' => '50%',
			),
			'c2' => array(
				'content' => $this->load->view('users/import/stage1_side', $this->data, TRUE),
				'width' => '50%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

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
			$flashmsg = msgbox('error', "No import data found.");
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
		}

		$result = $_SESSION['import_results'] ?? null;
		if (empty($result)) {
			$flashmsg = msgbox('error', "Import results not found.");
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
		}

		$result = json_decode(base64_decode($result));
		if ( ! is_array($result)) {
			$flashmsg = msgbox('error', "Could not parse results.");
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users/import');
			unset($_SESSION['import_results']);
		}

		$this->data['result'] = $result;

		$this->data['title'] = 'Imported Users';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('users/import/stage2', $this->data, TRUE);

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
			$notice = msgbox('exclamation', "No CSV file uploaded");
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
			'authlevel' => $this->input->post('authlevel'),
			'enabled' => $this->input->post('enabled'),
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

		// Parse CSV file
		while (($row = fgetcsv($handle, filesize($file_path), ',')) !== FALSE) {

			if (strtolower($row[0]) == 'username') {
				$line++;
				continue;
			}

			$first_name = $this->get_value($row[1]);
			$last_name = $this->get_value($row[2]);
			$full_name = trim("{$first_name} {$last_name}");

			$user = array(
				'username' => $this->get_value($row[0]),
				'firstname' => $this->get_value($row[1]),
				'lastname' => $this->get_value($row[2]),
				'email' => $this->get_value($row[3]),
				'password' => $this->get_value($row[4]),
				'authlevel' => $defaults['authlevel'],
				'enabled' => $defaults['enabled'],
				'department_id' => null,
				'ext' => null,
				'displayname' => $full_name ?? null,
			);

			if (is_null($user['password'])) {
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
				'label' => 'Username',
				'rules' => 'trim|required|max_length[32]|regex_match[/^[A-Za-z0-9-_.@]+$/]',
			],
			[
				'field' => 'firstname',
				'label' => 'First name',
				'rules' => 'trim|max_length[20]',
			],
			[
				'field' => 'lastname',
				'label' => 'Last name',
				'rules' => 'trim|max_length[20]',
			],
			[
				'field' => 'email',
				'label' => 'Email address',
				'rules' => 'valid_email|max_length[255]',
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required',
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

		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

		$res = $this->users_model->Add($data);

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
