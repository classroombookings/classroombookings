<?php

?>


<p>Listed below are the groups that exist within Classroombookings. In each tab, it is possible to configure what users belonging to that group are allowed and not allowed to do.</p>


<div class="tabber" id="tabs-permissions">

	<?php
	foreach($groups as $group){
	
	echo '<div class="tabbertab">';
	//echo '<h2>' . $group['

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
