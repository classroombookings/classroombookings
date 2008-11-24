<?php
$foo = validation_errors();
if($foo){
	echo $this->msg->err('<ul>' . $foo . '</ul>', 'Configuration values could not be validated.');
}
?>


<div class="tabber" id="tabs-configure">

	<div class="tabbertab">
		<h2>Main Settings</h2>
		<?php $this->load->view('configure/conf.main.php'); ?>
	</div>

	<div class="tabbertab">
		<h2>LDAP authentication</h2>
		<?php $this->load->view('configure/conf.ldap.php'); ?>
	</div>

</div>