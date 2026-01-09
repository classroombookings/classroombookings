<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changelog
{


	private MY_Controller $CI;

	private bool $enabled = false;
	private string $channel;
	private $changelog_ts;
	private $last_refreshed_at;

	protected string $settings_group = 'changelog';


	public function __construct()
	{
		$this->CI =& get_instance();
		$this->channel = config_item('changelog_channel');
		$this->enabled = !empty($this->channel);

		$this->init();
	}


	private function init()
	{
		if ( ! $this->enabled) return;

		if ( ! isset($this->CI->db)) return;

		$this->changelog_ts = $this->CI->settings_model->get('changelog_ts', $this->settings_group);
		if ($this->changelog_ts === false) $this->changelog_ts = 0;
		$this->last_refreshed_at = $this->CI->settings_model->get('refreshed_at', $this->settings_group);
		if ($this->last_refreshed_at === false) $this->last_refreshed_at = 0;
	}


	public function public_url()
	{
		if ( ! $this->enabled) return null;

		$fmt = 'https://www.classroombookings.com/go/changelog/%s/';
		return sprintf($fmt, $this->channel);
	}


	private function ts_url()
	{
		$fmt = 'https://www.classroombookings.com/changelog/%s/';
		return sprintf($fmt, $this->channel);
	}


	public function touch()
	{
		if ( ! $this->enabled) return;
		if ( ! $this->CI->userauth->logged_in()) return;

		$user = $this->CI->userauth->get_user();

		$settings_group = sprintf('user.%d', $user->user_id);
		$settings_key = 'changelog_viewed_at';
		$this->CI->settings_model->set($settings_key, time(), $settings_group);
	}


	public function get_indicator_markup()
	{
		if ( ! $this->enabled) return '';
		if ( ! $this->CI->userauth->logged_in()) return '';

		$now = time();
		$diff = ( (int) $now - (int) $this->last_refreshed_at);

		// log_message('debug', "changelog->get_indicator_icon(): last_refreshed_at={$this->last_refreshed_at}");
		// log_message('debug', "changelog->get_indicator_icon(): changelog_ts={$this->changelog_ts}");
		// log_message('debug', "changelog->get_indicator_icon(): now={$now}");
		// log_message('debug', "changelog->get_indicator_icon(): (now-last_refreshed_at)={$diff}");

		if ($diff > TIME_DAY || $this->last_refreshed_at === 0) {
			return $this->get_indicator_lazy();
		}

		// No changelog time stored
		if ($this->changelog_ts === 0) {
			return $this->get_indicator_lazy();
		}

		// Unread updates
		$user_last_viewed = $this->get_last_viewed();
		if ($user_last_viewed < $this->changelog_ts) {
			return $this->get_indicator_icon();
		}

		return '<!-- no_updates -->';
	}


	public function get_indicator_lazy()
	{
		if ( ! $this->enabled) return '';

		$url = site_url('changelog/status');
		return "<span hx-post='{$url}' hx-trigger='load delay:1s'></span>";
	}


	public function get_indicator_icon()
	{
		if ( ! $this->enabled) return '<!-- not_enabled -->';
		if ( ! $this->CI->userauth->logged_in()) return '<!-- not_logged_in -->';

		$now = time();
		$diff = ( (int) $now - (int) $this->last_refreshed_at);
		if ($diff > TIME_DAY) {
			$this->fetch_latest();
		}

		// log_message('debug', "changelog->get_indicator_icon(): last_refreshed_at={$this->last_refreshed_at}");
		// log_message('debug', "changelog->get_indicator_icon(): changelog_ts={$this->changelog_ts}");
		// log_message('debug', "changelog->get_indicator_icon(): now={$now}");
		// log_message('debug', "changelog->get_indicator_icon(): diff={$diff}");

		$user_last_viewed = $this->get_last_viewed();
		if ($user_last_viewed < $this->changelog_ts) {
			// log_message('debug', 'changelog->get_indicator_icon(): alert dot.');
			$is_unread = '<span class="alert-dot"></span>';
			return $is_unread;
		}

		// log_message('debug', 'changelog->get_indicator_icon(): no output.');

		return '<!-- n/a -->';
	}


	private function fetch_latest()
	{
		$opts = [
			'http' => [
				'method' => "GET",
				'timeout' => 3,
				'ignore_errors' => true,
			],
		];

		$now = time();
		$this->CI->settings_model->set('refreshed_at', $now, $this->settings_group);

		$url = $this->ts_url();
		$context = stream_context_create($opts);
		$value = file_get_contents($url, false, $context, 0, 10);

		// log_message('debug', "changelog->fetch_latest(): url={$url}");
		// log_message('debug', "changelog->fetch_latest(): value={$value}");

		if ($value === false) {
			return null;
		}

		$value = trim($value);
		if ( ! is_numeric($value)) {
			return null;
		}

		// Save the changelog timestamp
		$this->CI->settings_model->set('changelog_ts', $value, $this->settings_group);

		// Refresh the values
		$this->init();
	}


	public function get_last_viewed()
	{
		if ( ! $this->enabled) return 0;
		if ( ! $this->CI->userauth->logged_in()) return 0;

		$user = $this->CI->userauth->get_user();

		$settings_group = sprintf('user.%d', $user->user_id);
		$settings_key = 'changelog_viewed_at';
		$value = $this->CI->settings_model->get($settings_key, $settings_group);
		if ($value === false) return 0;
		return $value;
	}


}
