<div id="check_access_result">
	<?php
	if (isset($result)) {
		$legend = sprintf(lang('acl.result.title'), $user->displayname ?: $user->username, $room->name);
		$legend = html_escape($legend);
		echo "<fieldset>";
		echo "<legend>{$legend}</legend>";
		$this->load->view('setup/access_checker/_result');
		echo "</fieldset>";
	}
	?>
</div>
