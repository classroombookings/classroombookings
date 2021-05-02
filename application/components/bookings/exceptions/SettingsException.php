<?php

namespace app\components\bookings\exceptions;


class SettingsException extends \RuntimeException
{


	public static function forDisplayType()
	{
		return new static("The 'Display Type' setting has not been set.");
	}


	public static function forColumns()
	{
		return new static("The 'Display Columns' setting has not been set.");
	}


	public static function forNoRooms()
	{
		return new static("There are no rooms available.");
	}


}
