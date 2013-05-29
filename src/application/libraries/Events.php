<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Events
 *
 * A simple events system for CodeIgniter.
 *
 * @version		1.0
 * @author		Dan Horrigan <http://dhorrigan.com>
 * @author		Eric Barnes <http://ericlbarnes.com>
 * @license		Apache License v2.0
 * @copyright	2010 Dan Horrigan
 */

/**
 * Events Library
 */
class Events
{

	/**
	 * An array of listeners
	 * 
	 * @var	array
	 */
	protected static $_listeners = array();
	
	
	/**
	 * Register
	 *
	 * Registers a Callback for a given event
	 *
	 * @param string $event The name of the event.
	 * @param array $callback The callback for the event.
	 */
	public static function register($event, array $callback)
	{
		$key = get_class($callback[0]).'::'.$callback[1];
		self::$_listeners[$event][$key] = $callback;
		log_message('debug', 'Events::register() - Registered "'.$key.' with event "'.$event.'"');
	}
	
	
	/**
	 * Triggers an event and returns the results.
	 * 
	 * The results can be returned in the following formats:
	 *  - 'array'
	 *  - 'json'
	 *  - 'serialized'
	 *  - 'string'
	 *
	 * @param string $event The name of the event
	 * @param string $data Any data that is to be passed to the listener
	 * @param string $return_type The return type
	 * @return string|array The return of the listeners, in the return type
	 */
	public static function trigger($event, $data = '', $return_type = 'string')
	{
		log_message('debug', 'Events::trigger() - Triggering event "'.$event.'"');

		$calls = array();

		if (self::has_listeners($event))
		{
			foreach (self::$_listeners[$event] as $listener)
			{
				if (is_callable($listener))
				{
					$calls[] = call_user_func($listener, $data);
				}
			}
		}

		return self::_format_return($calls, $return_type);
	}
	
	
	/**
	 * Format Return
	 *
	 * Formats the return in the given type
	 *
	 * @param array $calls The array of returns
	 * @param string $return_type The return type
	 * @return array|null The formatted return
	 */
	protected static function _format_return(array $calls, $return_type)
	{
		log_message('debug', 'Events::_format_return() - Formating calls in type "'.$return_type.'"');

		switch ($return_type)
		{
			case 'array':
				return $calls;
				break;
			case 'json':
				return json_encode($calls);
				break;
			case 'serialized':
				return serialize($calls);
				break;
			case 'string':
				$str = '';
				foreach ($calls as $call)
				{
					$str .= $call;
				}
				return $str;
				break;
			default:
				return $calls;
				break;
		}

		// Does not do anything, so send NULL. FALSE would suggest an error
		return NULL;
	}
	
	
	/**
	 * Checks if the event has listeners
	 *
	 * @param string $event The name of the event
	 * @return boolean Whether the event has listeners
	 */
	public static function has_listeners($event)
	{
		log_message('debug', 'Events::has_listeners() - Checking if event "'.$event.'" has listeners.');

		if (isset(self::$_listeners[$event]) AND count(self::$_listeners[$event]) > 0)
		{
			return TRUE;
		}

		return FALSE;
	}
	
	
}