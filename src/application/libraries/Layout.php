<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) Craig A Rodway <craig.rodway@gmail.com>
 *
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt. It is also available 
 * through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 */

// -----------------------------------------------------------------------------

/**
 * Layout class to handle page layout, partials, content, navigation, breadcrumbs and assets.
 *
 * @package		Classroombookings
 * @subpackage	Libraries
 * @category	Layout
 * @author		Craig A Rodway
 */

class Layout
{


	private $_CI;
	private $_title;
	private $_js = array();
	private $_views = array(); 
	private $_content = array();
	private $_template;
	
	private $breadcrumb = array();
	
	public $lasterr;	
	
	
	
	function __construct()
	{
		$this->_CI =& get_instance();
	}
	
	
	
	
	public function set_template($template = '')
	{
		if ($template !== '')
		{
			$this->_template = $template;
			return $this;
		}
	}
	
	
	
	
	public function get_template()
	{
		return 'template/' . $this->_template;
	}
	
	
	
	
	/**
	 * Set the current page title
	 */
	public function set_title($text = '')
	{
		if ($text != '')
		{
			$this->title = $text;
		}
	}
	
	
	
	
	/**
	 * Get the full or page title
	 */
	public function get_title($type = 'page')
	{
		if (empty($this->title)) $this->title = '';
		
		if ($type === 'full')
		{
			$titles = array($this->title, 'Classroombookings', option('school_name'));
			$titles = array_filter($titles, 'strlen');
			return implode(' - ', $titles);
		}
		elseif ($type === 'page')
		{
			return $this->title;
		}
	}
	
	
	
	
	public function add_breadcrumb($title = '', $uri = NULL)
	{
		if ($uri === NULL)
		{
			$this->breadcrumb[] = array($title);
		}
		else
		{
			$this->breadcrumb[] = array($title, site_url($uri));
		}
	}
	
	
	
	
	public function get_breadcrumb()
	{
		unset($this->breadcrumb[count($this->breadcrumb)-1][1]);
		return $this->breadcrumb;
	}
	
	
	
	
	/**
	 * Add a javascript file for this page
	 */
	public function set_js($js = '', $dir = 'js/')
	{
		if ( ! is_array($js))
		{
			$js = array($js);
		}
		
		foreach ($js as $src)
		{
			// Check if the file being added is remote or not.
			// Valid remote formats: http://example.com/file.js -OR- //example.com/file.js
			$remote = (preg_match('/^(http:|\/\/)/', $src));
			
			// Add the appropriate path'd file to array
			$this->_js[] = ($remote) ? $src : $dir . $src . '.js';
		}
		return $this;
	}
	
	
	
	
	/**
	 * Returns HTML string of <script> tags used to load javascript files in view
	 */
	public function get_js()
	{
		$html = '';
		
		// loop over each js source
		foreach ($this->_js as $src)
		{
			// set source with local scripts directory
			$html .= '<script src="' . $src . '"></script>' . "\n";
		}
		
		return $html;
	}
	
	
	
	
	public function clear_js()
	{
		$this->_js = array();
		return $this;
	}
	
	
	
	
	/**
	 * Set array element to $_css
	 */
	function set_css($css, $dir = 'css/')
	{
		// If the supplied $css var is not an array, make an array from it first
		if ( ! is_array($css))
		{
			$css = array($css);
		}
		
		// Loop through each $css element and add it to $_css
		foreach ($css as $src)
		{
			// Check if the file being added is remote or not.
			// Valid remote formats: http://example.com/file.js -OR- //example.com/file.js
			$remote = (preg_match('/^(http:|\/\/)/', $src));
			
			// Add the appropriate path'd file to array
			$this->_css[] = ($remote) ? $src : $dir . $src . '.css';
		}
		
		return $this;
	}
	
	
	
	
	/**
	 * Returns html used to load css files in view
	 */
	function get_css()
	{
		$html = '';
		
		foreach ($this->_css as $src)
		{
			$html .= '<link href="' . $src . '" rel="stylesheet" type="text/css">' . "\n";
		}
		
		return $html;
	}
	
	
	
	
	/**
	 * Clears $_css array
	 */
	function clear_css()
	{
		$this->_css = array();
		return $this;
	}

	
	
	
	
	/**
	 * Set a view file for provided section
	 */
	public function set_view($section, $view)
	{
		$this->_views[$section] = $view;
	}
	
	
	
	
	/**
	 * Explicitly set the content for provided section
	 */
	public function set_content($section, $content)
	{
		$this->_content[$section] = $content;
	}
	
	
	
	
	/**
	 * Get the content for a given section. Content overrides views.
	 */
	public function get($section, $data = array())
	{
		if (isset($this->_content[$section]))
		{
			// Have actual content - return this
			$content = $this->_content[$section];
		}
		elseif ( ! empty($this->_views[$section]))
		{
			// View has been set - load the view file into variable
			$content = $this->_CI->load->view($this->_views[$section], $data, TRUE);
		}
		else
		{
			$content = '';	//'No content set.';
		}
		return $content;
	}
	
	
	
	
	/**
	 * Returns if the layout section has been set and/or has content
	 */
	public function has($section)
	{
		$_content = isset($this->_content[$section]);
		$_view = isset($this->_views[$section]);
		return ( $_content OR $_view );
	}
	
	
	
	public function has_view($section)
	{
		return (isset($this->_views[$section]));
	}
	
	
	
	
	public function has_content($section)
	{
		return (isset($this->_content[$section]));
	}
	
	
	
	public function get_nav_level($nav = array(), $level = 0, $nav_current = array())
	{
		$nav_final = array();
		
		// string to match item URI to set as active, based on requested level being "active" item in nav_current 
		$active = element($level, $nav_current, '');
		
		//echo "Active: $active.";
		
		if ($level > 0)
		{
			// build up array from nav_current items
			foreach ($nav_current as $i => $uri)
			{
				$j = $i + 1;
				if ($j > $level) continue;
				if (isset($nav[$uri]['nav']))
				{
					$nav = $nav[$uri]['nav'];
				}
				else
				{
					$nav = array();
				}
			}
		}
		
		//print_r($nav);
		
		foreach ($nav as $uri => $item)
		{
			$p = $item['permission'];
			if ($this->_CI->auth->check($p))
			{
				unset($item['nav']);
				
				if ($active === $uri)
				{
					$item['class'] .= ' active';
				}
				
				$nav_final[$uri] = $item;
			}
		}
		
		return $nav_final;
	}
	
	
	
}

/* End of file: ./application/libraries/Layout.php */