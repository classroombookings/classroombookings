<?php

namespace app\components\bookings\exceptions;


class SettingsException extends \RuntimeException
{


	public static function forDisplayType()
	{
		$line = lang('exception.SettingsException.forDisplayType');
		return new static($line);
	}


	public static function forColumns()
	{
		$line = lang('exception.SettingsException.forColumns');
		return new static($line);
	}


	public static function forNoRooms()
	{
		$line = lang('exception.SettingsException.forNoRooms');
		return new static($line);
	}


	public static function forNoSchedule()
	{
		$line = lang('exception.SettingsException.forNoSchedule');
		return new static($line);
	}


}
