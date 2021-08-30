<?php

namespace app\components\bookings\exceptions;


class AvailabilityException extends \RuntimeException
{


	public static function forNoWeek()
	{
		return new static("The selected date is not assigned to a timetable week.");
	}


	public static function forNoPeriods()
	{
		return new static("There are no periods available for the selected date.");
	}


	public static function forHoliday($holiday = NULL)
	{
		if ( ! is_object($holiday)) {
			return new static('The date you selected is during a holiday.');
		}

		$format = 'The date you selected is during a holiday: %s: %s - %s';
		$start = $holiday->date_start->format('d/m/Y');
		$end = $holiday->date_end->format('d/m/Y');
		$str = sprintf($format, $holiday->name, $start, $end);
		return new static($str);
	}


}
