<?php

namespace app\components\bookings\exceptions;


class AgentException extends \RuntimeException
{


	public static function forInvalidType($types)
	{
		return new static("Unrecognised booking type. Should be one of " . implode(', ', $types));
	}

	public static function forNoSession()
	{
		return new static('Requested date does not belong to a session.');
	}


	public static function forNoPeriod()
	{
		return new static('Requested period could not be found.');
	}


	public static function forNoRoom()
	{
		return new static('Requested room could not be found or is not bookable.');
	}


	public static function forInvalidDate()
	{
		return new static('Requested date is not recognised or is not bookable.');
	}


	public static function forNoWeek()
	{
		return new static('Requested date is not associated with a timetable week.');
	}


	public static function forNoBooking()
	{
		return new static('Requested booking could not be found.');
	}


}
