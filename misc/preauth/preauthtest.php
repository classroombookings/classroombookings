<?php

/**
 * Pre-authentication example for Classroombookings in PHP
 */

define('CRBS_PREAUTH_KEY', '');
define('CRBS_PREAUTH_URL', '');

// User to log in
$username = 'admin';

// Current timestamp
$now = time();

// Specify if account should be created if it doesn't exist
$create = 1;

// Hash the values with the key
$preauth = hash_hmac('sha1', "$username|$now|$create", CRBS_PREAUTH_KEY);

// Generate array of query string parameters that are passed to CRBS
$params = array(
	'u' => $username,
	'ts' => $now,
	'create' => $create,
	'preauth' => $preauth,
);

// This is the URL of classroombookings
$url = CRBS_PREAUTH_URL . '?' . http_build_query($params);

?>

<a href="<?php echo $url ?>">Click here to login as <?php echo $username ?></a>.