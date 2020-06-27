<div id="ldap_test_results" style="word-break: break-word;">

<?php
if ( ! empty($errors)) {
	foreach ($errors as $err) {
		$err_msg = $this->lang->line("auth_ldap_{$err}");
		echo msgbox('error', $err_msg ? $err_msg : $err);
	}

	echo "<p><strong>Bind DN:</strong> " . html_escape($user_bind_dn) . "</p>";
	if ( ! empty($config['search_filter'])) {
		echo "<p><strong>Search filter:</strong> " . html_escape($user_search_filter) . "</p>";
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
