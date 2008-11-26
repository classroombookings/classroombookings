<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>

<div class="tabber" id="tabs-configure">

	<div class="tabbertab">
		<h2>Main settings</h2>
		<?php $this->load->view('configure/conf.main.php', $conf); ?>
	</div>

	<div class="tabbertab">
		<h2>Authentication</h2>
		<?php $this->load->view('configure/conf.auth.php', $conf); ?>
	</div>

</div>
