<?php

echo $this->session->flashdata('saved');

echo '<h5 style="margin:14px 0px">';
$img = img(asset_url('assets/images/ui/calendar.png'), FALSE, 'hspace="4" align="top" width="16" height="16"');
echo anchor('bookings', $img . ' ' . lang('booking.bookings'));
echo '</h5>';

echo '<br><br>';

$this->load->view('dashboard/stats');

?>

<div class="block-group has-spacing">

	<?php $this->load->view('dashboard/user_bookings') ?>
	<?php $this->load->view('dashboard/room_bookings') ?>

</div>
