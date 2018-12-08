<?php


/**
 * This function returns the maximum files size that can be uploaded.
 *
 * @return int File size in bytes
 *
 */
function max_upload_file_size()
{
    return min(php_size_to_bytes(ini_get('post_max_size')), php_size_to_bytes(ini_get('upload_max_filesize')));
}


/**
* This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
*
* @param string $sSize
* @return integer The value in bytes
*
*/
function php_size_to_bytes($sSize)
{
    //
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix,array('P','T','G','M','K'))){
        return (int)$sSize;
    }
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
            // Fallthrough intended
        case 'T':
            $iValue *= 1024;
            // Fallthrough intended
        case 'G':
            $iValue *= 1024;
            // Fallthrough intended
        case 'M':
            $iValue *= 1024;
            // Fallthrough intended
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int)$iValue;
}




/**
 * Determine if classroombookings is installed yet or not.
 *
 * If it's installed, a `.installed` file will be present in the `local` dir.
 * If that doesn't exist, further checks are done for the database connection.
 *
 */
function is_installed()
{
	$file_path = FCPATH . 'local/.installed';

	// 1. Do we have a .installed file? (v2)
	if (is_file($file_path)) {
		return TRUE;
	}

	// 2. Check if we have DB connection info
	$CI =& get_instance();

	$has_connection = (strlen($CI->db->dsn) || strlen($CI->db->username));
	$table_exists = $CI->db->table_exists('bookings') && $CI->db->table_exists('users');
	$num_users = (int) $CI->db->count_all('users');

	if ($has_connection && $table_exists && $num_users > 0) {
		// Actually installed, but no file - write it now.
		$CI->load->helper('file');
		write_file($file_path, time());
		return TRUE;
	}

	return FALSE;
}


function setting($key, $group = 'crbs')
{
	$CI =& get_instance();
	return $CI->settings_model->get($key, $group);
}
