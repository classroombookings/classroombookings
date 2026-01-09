<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dates
{

	// private $CI;
	private array $lang_map;
	private array $lang_map_locale;
	private string $lang;
	private array $formatters;

	const FORMAT_LONG = 'long';
	const FORMAT_WEEKDAY = 'weekday';
	const FORMAT_TIME = 'time';

	public function __construct()
	{
		$this->lang_map = config_item('lang_map');
		$this->lang_map_locale = config_item('lang_map_locale');
		$this->lang = config_item('language');
		$this->init_formatters();
	}


	public function get_locale(): string
	{
		return $this->lang_map_locale[$this->lang] ?? 'en_GB';
	}


	public function init_formatters()
	{
		$config = [
			self::FORMAT_LONG => [
				'date' => IntlDateFormatter::FULL,
				'time' => IntlDateFormatter::NONE,
			],
			self::FORMAT_WEEKDAY => [
				'date' => IntlDateFormatter::MEDIUM,
				'time' => IntlDateFormatter::NONE,
			],
			self::FORMAT_TIME => [
				'date' => IntlDateFormatter::NONE,
				'time' => IntlDateFormatter::SHORT,
			],
		];

		foreach ($config as $type => $params) {
			$formatter = new IntlDateFormatter($this->get_locale(), $params['date'], $params['time']);
			$setting = setting("pattern_{$type}", 'dates');
			if ( ! empty($setting)) {
				$formatter->setPattern($setting);
			}
			$this->formatters[$type] = $formatter;
		}
	}


	public function format(string|DateTime $date, $format = self::FORMAT_LONG): string
	{
		if ( ! $date instanceof DateTime) {
			$date = datetime_from_string($date);
		}

		if ( ! $date instanceof DateTime) {
			return '';
		}

		return $this->formatters[$format]->format($date);
	}


	public function date_pattern_options()
	{
		$dt_str = sprintf('%04d-04-16', date('Y'));
		$dt = DateTime::createFromFormat('!Y-m-d', $dt_str);

		$formatter = new IntlDateFormatter($this->get_locale(), IntlDateFormatter::FULL, IntlDateFormatter::NONE);

		$patterns = [
			'yyyy-MM-dd',	// 	2025-04-16
			'dd/MM/yyyy',	// 16/04/2025
			'dd.MM.yyyy',	// 16/04/2025
			'MM/dd/yyyy',	// 04/16/2025

			'',

			'd MMM yyyy',	// 16 Apr 2025
			'd MMMM yyyy',	// 16 April 2025
			'EEE d MMM yyyy',	// Wed 16 Apr 2025
			'EEE d MMMM yyyy',	// Wed 16 April 2025
			'EEEE d MMMM yyyy',	// Wednesday 16 April 2025
			'EEEE, d MMMM yyyy',	// Wednesday, 16 April 2025
			'd MMM',	// 16 Apr
			'EEE d MMM',	// Wed 16 Apr
			'EEE d MMMM',	// Wed 16 April
			'EEEE d MMM',	// Wednesday 16 Apr
			'EEEE d MMMM',	// Wednesday 16 April

			'',

			'MMM d yyyy',	// 	Apr 16 2025
			'MMMM d yyyy',	// 	April 16 2025
			'MMMM d, yyyy',	// 	April 16, 2025
			'EEE MMM d yyyy',	// Wed Apr 16 2025
			'EEE MMMM d yyyy',	// Wed April 16 2025
			'EEEE MMMM d yyyy',	// Wednesday, April 16 2025
			'EEEE, MMMM d yyyy',	// Wednesday, April 16 2025
			'MMM d',	// Apr 16
			'EEE MMM d',	// Wed Apr 16
			'EEE MMMM d',	// Wed April 16
			'EEEE MMM d',	// Wednesday Apr 16
			'EEEE MMMM d',	// Wednesday April 16
		];

		$out = [];

		foreach ($patterns as $pattern) {
			if ($pattern === '') {
				$out['s_'.uniqid()] = '---';
				continue;
			}
			$formatter->setPattern($pattern);
			$out[ $pattern ] = $formatter->format($dt);
		}

		return $out;
	}


	public function time_pattern_options()
	{
		$dt = DateTime::createFromFormat('!H:i', '9:30');

		$formatter = new IntlDateFormatter($this->get_locale(), IntlDateFormatter::FULL, IntlDateFormatter::NONE);

		$patterns = [
			'HH:mm',	// 09:30
			'hh:mma',	// 09:30AM
			'hh:mm a',	// 09:30 AM
			'h:mma',	// 9:30AM
			'h:mm a',	// 9:30 AM

		];

		$out = [];

		foreach ($patterns as $pattern) {
			if ($pattern === '') {
				$out['s_'.uniqid()] = '---';
				continue;
			}
			$formatter->setPattern($pattern);
			$out[ $pattern ] = $formatter->format($dt);
		}

		return $out;
	}



}
