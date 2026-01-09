<div id="ldap_test_results" style="word-break: break-word;">

<?php
if ( ! empty($errors)) {
	foreach ($errors as $err) {
		$key = sprintf('auth.ldap.error.%s', $err);
		// Use library lang access to avoid logging errors
		$err_msg = $this->lang->line($key, false);
		if ($err_msg === false) {
			$err_msg = $err;	// no lang line, use as-is.
		}
		echo msgbox('error', $err_msg);
	}

	echo "<p><strong>" . lang('auth.ldap.test.bind_dn') . ":</strong> " . html_escape($user_bind_dn) . "</p>";
	if ( ! empty($config['search_filter'])) {
		echo "<p><strong>" . lang('auth.ldap.test.search_filter') . ":</strong> " . html_escape($user_search_filter) . "</p>";
	}
}

if ($user === TRUE || is_array($user)) {

	echo msgbox('info', lang('auth.ldap.test.auth_success'));

	if (is_array($mapping)) {

		$field_labels = [
			'firstname' => lang('user.field.firstname'),
			'lastname' => lang('user.field.lastname'),
			'displayname' => lang('user.field.displayname'),
			'email' => lang('user.field.email'),
		];

		echo "<dl>";
		foreach ($mapping as $field => $value) {
			$label = $field_labels[$field];
			if ($value === FALSE) {
				$value = sprintf('<em>(%s)</em>', strtolower(lang('app.skipped')));
			}
			echo "<dt>{$label}</dt>";
			echo "<dd>{$value}</dd>";
		}
		echo "</dl>";

	}

}

?>

</div>
