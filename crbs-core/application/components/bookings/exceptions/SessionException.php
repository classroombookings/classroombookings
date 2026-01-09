<?php

namespace app\components\bookings\exceptions;


class SessionException extends \RuntimeException
{


	public static function notSelected()
	{
		$line = lang('exception.SessionException.notSelected');
		return new static($line);
	}


}
