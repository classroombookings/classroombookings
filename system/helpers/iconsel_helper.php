<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * This helper by Craig Rodway, craig.rodway@gmail.com
 *
 * Makes it easy to include an icon-choosing box within forms from a specified folder of icons
 */

// ------------------------------------------------------------------------

// Requires the use of the following helpers:
#$this->load->helper('directory');
#$this->load->helper('form');

$CI =& get_instance();
#return $CI->input->cookie($index, $xss_clean);
#$CI->load->helper('directory');
#$CI->load->helper('form');

/**
 * Show an icon selection form element
 *
 * @access	public
 * @param	string	$name	Name of the drop-down element
 * @param	string	$folder		Folder under /webroot/img/ to get list of images from
 * @return	string		HTML fragment containing dropdown box list of icons and associated preview icon.
 */	
function iconsel($name, $folder, $selected, $attrs = ''){
	$folder = "webroot/images/".$folder;
	$folder_array = directory_map( $folder, True );
	$icons_array[''] = "None";
	for( $i=0; $i<count($folder_array); $i++){
		if( preg_match('/(.png|.jpg|.jpeg|.gif)$/i', $folder_array[$i] ) ){
			$nicename = explode( ".", $folder_array[$i] );
			$icons_array[$folder_array[$i]] = $nicename[0];
			$nicename = "";
		}
	}
	unset( $folder_array );
	asort( $icons_array );

	if( !$selected ){
		$image = 'webroot/images/blank.png';
	} else {
		$image = $folder.'/'.$selected;
	}

	$html  = form_dropdown( $name, $icons_array, $selected, " id=\"".$name."\" ".$attrs." onchange=\"iconsel('".$name."','".$folder."');\" onkeyup=\"iconsel('".$name."','".$folder."');\" ");
	$html .= '<img src="'.$image.'" id="preview_'.$name.'" style="height:16px;width:16px;padding:1px;border:1px solid #ccc;" hspace="6" align="top" width="16" height="16" alt=" " />';
	return $html;
}



function iconbox($name, $folder, $selected, $attrs = ''){

	$folder = 'webroot/images/'.$folder;
	$folder_array = directory_map( $folder, True );
	for( $i=0; $i<count($folder_array); $i++){
		if( preg_match('/(.png|.jpg|.jpeg|.gif)$/', $folder_array[$i] ) ){
			$nicename = explode( ".", $folder_array[$i] );
			$icons_array[$i+1] = $folder_array[$i];	//$nicename[0];
			$nicename = "";
		}
	}
	unset( $folder_array );
	asort( $icons_array );

	$html = '';
	#$html .= '<div class="iconbox">';
	foreach($icons_array as $icon){
		$checked = ($icon == $selected) ? true : false;
		$data = array(
									'name' => $name,
									'id' => 'icon'.$icon,
									'value' => $icon,
									'checked' => $checked,
									);
		$html .= '<div class="g'."$checked".'">';
		$html .= '<label class="ni" for="icon'.$icon.'"><img src="'.$folder.'/'.$icon.'" alt="'.$icon.'" title="'.$icon.'" width="16" height="16" /></label>';
		$html .= '<p>'."\n".form_radio($data, NULL, NULL, $attrs).'</p>';
		$html .= '</div>'."\n";
	}
	#$html .= '</div>';
	
	return $html;
}


?>
