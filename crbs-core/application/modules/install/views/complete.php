<?php
echo isset($notice) ? $notice : '';
?>

<p>Classroombookings has been installed!</p>

<?php
echo iconbar(array(
	array('login', 'Click here to log in', 'user_go.png'),
));
