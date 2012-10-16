<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>

<div class="tabber" id="tabs-configure">

	<div class="tabbertab<?php echo ($tab == 'details') ? ' tabbertabdefault' : ''; ?>">
		<h2>Main details</h2>
		<?php $this->load->view('rooms/addedit.details.php', $room); ?>
	</div>

	<?php if($room_id != NULL && $this->auth->check('rooms.permissions', TRUE)){ ?>
	<div class="tabbertab<?php echo ($tab == 'permissions') ? ' tabbertabdefault' : ''; ?>">
		<h2>Booking permissions</h2>
		<?php $this->load->view('rooms/addedit.permissions.php', $room); ?>
	</div>
	<?php } ?>
	
	<?php if($room_id != NULL && $this->auth->check('rooms.attrs.values', TRUE) && !empty($attrs)){ ?>
	<div class="tabbertab<?php echo ($tab == 'attrs') ? ' tabbertabdefault' : ''; ?>">
		<h2>Room attributes</h2>
		<?php $this->load->view('rooms/addedit.attrs.php', array('room' => $room, 'attrs' => $attrs)); ?>
	</div>
	<?php } ?>

</div>