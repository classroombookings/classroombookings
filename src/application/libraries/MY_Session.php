<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session {
	
	
	/**
	 * Set the special value of the return URI useful for redirecting to an index page after editing/deleting an item.
	 *
	 * Userdata 'return_uri' is set to current URI + whatever query string there is.
	 */
	public function set_return_uri($key = '')
	{
		$uri = $this->CI->uri->uri_string();
		if ( ! empty($_GET)) $uri .= '?' . http_build_query($this->CI->input->get());
		
		return $this->set_userdata('return_uri_' . $key, $uri);
	}
	
	
	/**
	 * Get the return URI that was already set. Optionally supply a different default value if none set.
	 */
	public function get_return_uri($key = '', $default = NULL)
	{
		return ($this->userdata('return_uri_' . $key) ? $this->userdata('return_uri_' . $key) : $default);
	}
	
	
	/**
	 * Clear any previously-set return URI values by removing the item from the session
	 */
	public function clear_return_uri()
	{
		return $this->session->unset_userdata('return_uri');
	}


}