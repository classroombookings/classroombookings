<?php

namespace app\components\bookings\exceptions;


class DateException extends \RuntimeException
{


	public static function invalidDate($date_string)
	{
		$line = lang('exception.DateException.invalidDate');
		return new static(sprintf($line, $date_string));
	}


	public static function forSessionRange($datetime)
	{
		if ($datetime) {
			$dt = $datetime->format('d/m/Y');
		} else {
			$dt = lang('app.none');
		}

		$line = lang('exception.DateException.forSessionRange');
		return new static(sprintf($line, $dt));
	}


}
