<?php

namespace app\components\bookings\exceptions;


class AgentException extends \RuntimeException
{


	public static function forInvalidType($types)
	{
		$line = lang('exception.AgentException.forInvalidType');
		$msg = sprintf($line, implode(', ', $types));
		return new static($msg);
	}

	public static function forNoSession()
	{
		$line = lang('exception.AgentException.forNoSession');
		return new static($line);
	}


	public static function forNoPeriod()
	{
		$line = lang('exception.AgentException.forNoPeriod');
		return new static($line);
	}


	public static function forNoRoom()
	{
		$line = lang('exception.AgentException.forNoRoom');
		return new static($line);
	}


	public static function forInvalidDate()
	{
		$line = lang('exception.AgentException.forInvalidDate');
		return new static($line);
	}


	public static function forNoWeek()
	{
		$line = lang('exception.AgentException.forNoWeek');
		return new static($line);
	}


	public static function forNoBooking()
	{
		$line = lang('exception.AgentException.forNoBooking');
		return new static($line);
	}


	public static function forAccessDenied()
	{
		$line = lang('exception.AgentException.forAccessDenied');
		return new static($line);
	}


}
