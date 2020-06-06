<?php echo $db ?>

<p>You have successfully set up <?php echo stripslashes($school['name']) ?>!</p>

<p>
<?php
$icondata[0] = array('login', 'Click here to login', 'user_go.png' );
$this->load->view('partials/iconbar', $icondata);
?>
</p>
