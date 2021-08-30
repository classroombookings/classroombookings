<?php

namespace app\components\bookings\exceptions;


class BookingValidationException extends \RuntimeException
{


	public static function forExistingBooking()
	{
		return new static("Another booking already exists.");
	}


	public static function forHoliday()
	{
		return new static("Booking cannot be created on a holiday.");
	}


}
