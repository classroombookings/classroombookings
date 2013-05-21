<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Full URL to your installation
$config['base_url'] = 'http://localhost/projects/crbs1/';


// Empty string if using .htaccess mod_rewrite
#$config['index_page'] = '';
$config['index_page'] = 'index.php';

// You shouldn't need to change any of these.
$config['layout'] = 'layout';
$config['language']	= 'english';
$config['uri_protocol']	= 'auto';
$config['url_suffix'] = '';
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['time_reference'] = 'local';
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['cache_path'] = '';
$config['encryption_key'] = '';
$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= '';
$config['sess_match_ip']		= TRUE;
$config['sess_match_useragent']	= TRUE;
$config['cookie_prefix']	= '';
$config['cookie_domain']	= ''; 
$config['cookie_path']		= '/';
$config['global_xss_filtering'] = TRUE;
$config['enable_hooks'] = False;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_-';
$config['compress_output'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['rewrite_short_tags'] = FALSE;

?>
