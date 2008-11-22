<?php
$icondata[0] = array('security/users/add', 'Add a new user', 'plus.gif' );
$icondata[1] = array('security/groups', 'Manage groups', 'group.gif' );
$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
$this->load->view('parts/iconbar', $icondata);
?>

<p>viewing users here</p>