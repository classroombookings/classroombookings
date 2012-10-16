<?php echo form_open($action, null, array('id' => $id)) ?>

<h5><?php echo $title ?></h5>

<?php if (isset($text)) { echo $this->msg->notice($text); } ?>

<br>

<?php
$t = 1;
unset($buttons);
$buttons[] = array('submit', 'red', "Delete", $t);
$buttons[] = array('link', '', "Cancel", $t + 1, site_url($cancel));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>

</form>
