<?php

namespace app\components\bookings\exceptions;


class DateException extends \RuntimeException
{


	public static function invalidDate($date_string)
	{
		return new static("No date selected or not valid ({$date_string}).");
	}


	public static function forSessionRange($datetime)
	{
		if ($datetime) {
			$dt = $datetime->format('d/m/Y');
		} else {
			$dt = 'Unknown';
		}

		return new static("The selected date ({$dt}) is not within the current Session.");
	}


}
