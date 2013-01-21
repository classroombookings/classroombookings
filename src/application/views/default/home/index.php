<div class="grid_6">
	<h4 class="sub-heading">Your bookings</h4>
		<ul>
			<li>Room 16, today, period 6</li>
			<li>ICT Suite, tomorrow, period 2</li>
		</ul>
	<h4 class="sub-heading">Your department&apos;s bookings</h4>
		<ul>
			<li>Room 14, tomorrow, period 4 booked by Joe Bloggs</li>
		</ul>
	<h4 class="sub-heading">Bookings in your room</h4>
		<ul>
			<li>Today, period 3 is booked by Mary Smith</li>
		</ul>
</div>

<div class="grid_6">
	<?php if (count($active_users) > 0): ?>
	<h4 class="sub-heading">People currently logged in</h4>
	<ul>
		<?php foreach ($active_users as $u): ?>
		<li><?php echo $u['u_display'] ?>
			<?php if ($u['u_id'] === $this->session->userdata('u_id')) echo '<span class="orange label">you</span>'; ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>