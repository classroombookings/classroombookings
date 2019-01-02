<br />
<p style="text-align:center">
	<label for="notes">Notes:</label> <input type="text" name="notes" id="notes" size="20" maxlength="100" value="<?php echo html_escape($this->session->userdata('notes')) ?>" />
	<label for="user_ud">User:</label>
	<?php
	$userlist['0'] = '(None)';
	foreach($users as $user){
		if( $user->displayname == '' ){ $user->displayname = $user->username; }
  		$userlist[$user->user_id] = html_escape($user->displayname);		#@field($user->displayname, $user->username);
	}
	$user_id = $this->userauth->user->user_id;
	echo form_dropdown('user_id', $userlist, $user_id, 'id="user_id"');
	?>
	<button type="submit" name="action" value="recurring">Make Recurring Booking</button>
</p>
