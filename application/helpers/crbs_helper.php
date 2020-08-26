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


function setting($key, $group = 'crbs')
{
	$CI =& get_instance();
	return $CI->settings_model->get($key, $group);
}



function json_encode_html($value)
{
	return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
}
