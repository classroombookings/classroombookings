<div id="ldap_test_results">

<?php
if ( ! empty($errors)) {
	foreach ($errors as $err) {
		$err_msg = $this->lang->line("auth_ldap_{$err}");
		echo msgbox('error', $err_msg ? $err_msg : $err);
	}
}

if ($user === TRUE || is_array($user)) {

	echo msgbox('info', 'Authentication success!');

	if (is_array($mapping)) {

		$field_labels = [
			'firstname' => 'First Name',
			'lastname' => 'Last Name',
			'displayname' => 'Display Name',
			'email' => 'Email address',
		];

		echo "<dl>";
		foreach ($mapping as $field => $value) {
			$label = $field_labels[$field];
			if ($value === FALSE) {
				$value = '<em>(skipped)</em>';
			}
			echo "<dt>{$label}</dt>";
			echo "<dd>{$value}</dd>";
		}
		echo "</dl>";

	}

}

?>

</div>
