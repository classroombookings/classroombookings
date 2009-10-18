<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>




<?php
$tabs[] = array('details', 'Main Details', $this->load->view('rooms/addedit.details.php', $room, TRUE));
if($room_id != NULL && $this->auth->check('rooms.permissions', TRUE)){
	$tabs[] = array('permissions', 'Booking permissions', $this->load->view('rooms/addedit.permissions.php', $room, TRUE));
}
if($room_id != NULL && $this->auth->check('rooms.attrs.values', TRUE) && !empty($attrs)){
	$tabs[] = array(
		'attrs', 
		'Room attributes', 
		$this->load->view('rooms/addedit.attrs.php', array('room' => $room, 'attrs' => $attrs), TRUE),
	);
}

$this->load->view('parts/pagetabs', array('tabs' => $tabs));
