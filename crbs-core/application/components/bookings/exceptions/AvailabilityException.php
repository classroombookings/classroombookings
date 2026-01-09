<?php

namespace app\components\bookings\exceptions;


class AvailabilityException extends \RuntimeException
{


	public static function forNoWeek()
	{
		$line = lang('exception.AvailabilityException.forNoWeek');
		return new static($line);
	}


	public static function forNoPeriods()
	{
		$line = lang('exception.AvailabilityException.forNoPeriods');
		return new static($line);
	}


	public static function forHoliday($holiday = NULL)
	{
		if ( ! is_object($holiday)) {
			$line = lang('exception.AvailabilityException.forHoliday.unknown');
			return new static($line);
		}

		$line = lang('exception.AvailabilityException.forHoliday');
		// @todo use a short date setting value
		$start = $holiday->date_start->format('d/m/Y');
		$end = $holiday->date_end->format('d/m/Y');
		$str = sprintf($line, $holiday->name, $start, $end);
		return new static($str);
	}


}
