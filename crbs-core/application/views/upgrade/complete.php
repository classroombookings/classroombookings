<?php
echo isset($notice) ? $notice : '';
?>

<p>Classroombookings has been upgraded!</p>
<br>
<p>Please check you can log in and that everything works as expected.</p>
<p>If it does, delete the following items that are no longer required:</p>
<ul>
	<li><strong>system</strong> folder</li>
	<li><strong>temp</strong> folder</li>
	<li><strong>webroot</strong> folder</li>
	<li><strong>classroombookings.sql</strong> file</li>
	<li><strong>license.txt</strong> file</li>
</ul>

<?php
echo iconbar(array(
	array('login', 'Click here to log in', 'user_go.png'),
));
