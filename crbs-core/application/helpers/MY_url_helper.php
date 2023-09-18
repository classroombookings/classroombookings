<?php

/**
 * classroombookings redirect():
 *
 * Same as CodeIgniter's, but strip out the legacy IIS detection that enforces
 * the $method to 'refresh'. This causes a few issues with mainly unpoly
 * interactions and the bookings page.
 *
 *
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @param	string	$uri	URL
 * @param	string	$method	Redirect method
 *			'auto', 'location' or 'refresh'
 * @param	int	$code	HTTP Response status code
 * @return	void
 */
function redirect($uri = '', $method = 'auto', $code = NULL)
{
	if ( ! preg_match('#^(\w+:)?//#i', $uri))
	{
		$uri = site_url($uri);
	}

	if ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
	{
		if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
		{
			$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
				? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
				: 307;
		}
		else
		{
			$code = 302;
		}
	}

	switch ($method)
	{
		case 'refresh':
			header('Refresh:0;url='.$uri);
			break;
		default:
			header('Location: '.$uri, TRUE, $code);
			break;
	}
	exit;
}
