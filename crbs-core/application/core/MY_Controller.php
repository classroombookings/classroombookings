<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Controller extends CI_Controller
{


	/**
	 * Global data for view
	 *
	 * @var array
	 *
	 */
	public $data = array();

	public $js = [];
	public $hs = [];


	public function __construct()
	{
		parent::__construct();

		$this->crbs_startup();
		$this->init_assets();
		// $this->init_events();
		$this->load_main();
		$this->init_lang();
		$this->check_password_reset();
		$this->profiler();
	}


	protected function require_logged_in($msg = TRUE)
	{
		// Check loggedin status
		if ( ! $this->userauth->logged_in()) {
			if ($msg) {
				$this->session->set_flashdata('auth', msgbox('error', lang('auth.login_required')));
			}
			$_SESSION['post_login_uri'] = $this->uri->uri_string().'?'.http_build_query($this->input->get());
			redirect('login');
		}
	}


	protected function require_permission(string $permission)
	{
		$this->require_logged_in();

		if ( ! $this->permission->can($permission)) {
			$this->output->set_status_header(403);
			$msg = msgbox('error', lang('auth.permission_required'));
			$this->render_error('Access denied', $msg);
		}
	}


	protected function require_any_permission(array $permissions)
	{
		foreach ($permissions as $permission) {
			if ($this->permission->can($permission)) {
				return true;
			}
		}

		$this->output->set_status_header(403);
		$msg = msgbox('error', lang('auth.permission_required'));
		$this->render_error('Access denied', $msg);
	}


	protected function render_error($title, $message): never
	{
		$this->data['title'] = $title;
		$this->data['showtitle'] = $this->data['title'];
		$this->data['message'] = $message;
		$this->data['body'] = $this->load->view('partials/error', $this->data, TRUE);
		$this->render();
		$this->output->_display();
		exit();
	}


	protected function render($view_name = 'layout')
	{
		$this->load->view($view_name, $this->data);
	}


	protected function render_up()
	{
		$this->load->view('unpoly', $this->data);
	}


	private function crbs_startup()
	{
		if ( ! CRBS_MANAGED) {
			$this->load->add_package_path(ROOTPATH.'local');
			$this->load->driver('cache', [
				'adapter' => 'file',
			]);
			return;
		}

		$this->benchmark->mark('startup_start');

		$package_path = (CRBS_MANAGED)
			? ROOTPATH.'crbs-managed'
			: ROOTPATH.'packages';

		$this->load->add_package_path($package_path);
		$this->load->library('startup');

		$this->benchmark->mark('startup_end');
	}



	private function init_assets()
	{
		$this->data['js'] = [];
		$this->data['hs'] = [];
		$this->data['css'] = [];

		$all_js = array_merge([
			'htmx',
			'hyperscript',
			'datepicker',
			'es6-promise',
			'unpoly',
			'toastify',
		], $this->js, ['main']);

		$all_js = array_unique($all_js);

		$this->load->config('assets', true);
		$js_config = $this->config->item('js', 'assets');

		foreach ($all_js as $script_name) {
			if ( ! isset($js_config[$script_name])) {
				$msg = sprintf("Javascript asset '%s' not registered.", $script_name);
				throw new Exception($msg);
			}
			$this->data['js'][] = $js_config[$script_name];
		}

		$this->load->config('assets', true);
		$hs_config = $this->config->item('hs', 'assets');

		foreach ($this->hs as $script_name) {
			if ( ! isset($hs_config[$script_name])) {
				$msg = sprintf("hyperscript asset '%s' not registered.", $script_name);
				throw new Exception($msg);
			}
			$this->data['hs'][] = $hs_config[$script_name];
		}

		$this->data['css'][] = [
			'media' => 'screen',
			'path' => ENVIRONMENT === 'development'
				? 'assets/css/main.css'
				: 'assets/css/main.min.css'
		];

		$this->data['css'][] = [
			'media' => 'print',
			'path' => ENVIRONMENT === 'development'
				? 'assets/css/print.css'
				: 'assets/css/print.min.css'
		];
	}


	private function profiler()
	{
		$this->output->enable_profiler(config_item('show_profiler') === TRUE);

		if (CRBS_MANAGED && ENVIRONMENT !== 'production' && $this->input->get('profiler') == 1) {
			$this->output->enable_profiler(true);
		}
	}


	private function load_main()
	{
		$this->load->library('changelog');

		if (static::class == 'Install' || static::class == 'Upgrade') return;

		if ( ! CRBS_MANAGED && ! config_item('is_installed')) {
			redirect('install');
		}

		$this->load->database();

		$tz = setting('timezone');
		if (!empty($tz)) {
			date_default_timezone_set($tz);
		}

		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('userauth');

		if ( ! CRBS_MANAGED) {
			$this->load->library('migration');
			$this->migration->latest();
		}

		$this->load->helper([
			'user_file',
		]);
	}


	private function init_lang()
	{
		$this->benchmark->mark('lang_init_start');

		$all_languages = $this->lang->get_languages();
		$lang_settings = $this->settings_model->get_all('lang');
		$enabled_langs = $lang_settings['languages'] ?? [];

		$this->load->helper('cookie');
		$cookie_lang = get_cookie('crbs_lang');
		$session_lang = $_SESSION['crbs_lang'] ?? null;
		$setting_lang = setting('default_language', 'lang');
		$default = 'english';
		$idiom = $cookie_lang ?? $session_lang ?? $setting_lang ?: $default;
		if ( ! in_array($idiom, $enabled_langs) && ! in_array($idiom, $all_languages)) {
			$idiom = $default;
		}
		// echo var_export(compact('cookie_lang', 'session_lang', 'setting_lang', 'default', 'idiom'));

		$this->benchmark->mark('lang_init_end');

		$this->load_language($idiom);

		$this->load->library('dates');
	}


	protected function load_language(string $idiom)
	{
		$this->benchmark->mark('lang_load_start');

		$this->config->set_item('language', $idiom);

		$lang_files = [
			'account',
			'acl',
			'app',
			'auth',
			'booking',
			'cal',
			'calendar',
			'constraint',
			'custom_field',
			'department',
			'exception',
			'export',
			'holiday',
			'language',
			'organisation',
			'period',
			'permission',
			'role',
			'room',
			'room_group',
			'schedule',
			'session',
			'settings',
			'setup',
			'user',
			'validation',
			'week',
		];

		$this->lang->load($lang_files, $idiom);

		$this->load->helper('language');

		$this->benchmark->mark('lang_load_end');
	}


	/**
	 * Check for force-password-reset conditions.
	 *
	 */
	private function check_password_reset()
	{
		if (!config_item('is_installed')) return;
		if ( ! isset($this->userauth)) return;
		if ( ! $this->userauth->logged_in()) return;

		$uri_allowlist = [
			'profile/new_password',
			'logout',
		];

		$auth_method = $_SESSION['auth_method'] ?? 'none';
		$needs_password_reset = $_SESSION['force_password_reset'] ?? 0;

		if ($needs_password_reset == 1
		    && $auth_method == 'local'
		    && ! in_array($this->uri->uri_string, $uri_allowlist)
		) {
			redirect('profile/new_password');
		}
	}


}


if (is_file(ROOTPATH . 'crbs-managed/core/API_Controller.php')) {
	require_once(ROOTPATH . 'crbs-managed/core/API_Controller.php');
}
