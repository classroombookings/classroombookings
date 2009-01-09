<?php

/*
 * Pre-authentication example for Classroombookings in PHP
 */

// User we are authenticating for
$username = 'joe.bloggs';

// Current timestamp
$now = time();

// The key generated in the Authentication configuration page
$preauthkey = 'YOUR-KEY-HERE';

// Generate a string with the values
$str = "$username|$now|$preauthkey";

// Hash it
$key = sha1($str);

// This is the URL of classroombookings
$uri = "http://localhost/projects/crbs2/index.php/account/preauth/%s/%d/%s";

// Replace items in the string with the actual values to get the final URL to navigate to (http:// ... /preauth/user/timestamp/key)
$uri = sprintf($uri, $username, $now, $key);

?>

<a href="<?php echo $uri ?>">Click here to login as <?php echo $username ?></a>.
