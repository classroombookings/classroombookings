<?php
if(is_array($result)){

	// Users imported
	echo '<table cellpadding="2" cellspacing="2" width="100%">';
	echo '<tr class="heading"><td class="h">Username</td><td class="h">Password</td><td class="h">Status</td></tr>';
	foreach($result as $user){
		echo '<tr>';
		echo '<td>' . $user['username'] . '</td>';
		echo '<td>' . $user['password'] . '</td>';
		$col = (stristr($user['_status'],'Success')) ? 'darkgreen' : 'darkred';
		echo '<td style="font-weight:bold;color:'.$col.'">' . $user['_status'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	
} else {

	// File upload failed
	echo $result;
	
}


$icondata[0] = array('users', 'User list', 'school_manage_users.png' );
$icondata[1] = array('users/import', 'Import more users', 'user_import.png' );
$this->load->view('partials/iconbar', $icondata);
?>
