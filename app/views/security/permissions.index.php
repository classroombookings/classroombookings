<?php
$icondata[0] = array('security/users', 'Manage users', 'user_orange.gif' );
$icondata[1] = array('security/groups', 'Manage groups', 'group.gif' );
#$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
$this->load->view('parts/iconbar', $icondata);
?>


<p>Listed below are the groups that exist within Classroombookings. In each tab, it is possible to configure what users belonging to that group are allowed and not allowed to do.</p>


<div class="tabber" id="tabs-permissions">

	<div class="tabbertab">
		<h2>Guests</h2>
        <?php $this->load->view('security/permissions.matrix.php'); ?>
	</div>


	<div class="tabbertab">
		<h2>Administrators</h2>
		<?php $this->load->view('security/permissions.matrix.php'); ?>
	</div>


	<div class="tabbertab">
		<h2>Teachers</h2>
		<?php $this->load->view('security/permissions.matrix.php'); ?>
	</div>

</div>