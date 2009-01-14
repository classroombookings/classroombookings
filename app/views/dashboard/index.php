<table width="100%">
	<tr>
		<td width="50%" valign="top">
			<h2>Your bookings</h2>
			<p>
				<ul>
					<li>Room 16, today, period 6</li>
					<li>ICT Suite, tomorrow, period 2</li>
				</ul>
			</p>
			<h2>Your department's bookings</h2>
			<p>
				<ul>
					<li>Room 14, tomorrow, period 4 booked by Joe Bloggs</li>
				</ul>
			</p>
			<h2>Bookings in your room</h2>
			<p>
				<ul>
					<li>Today, period 3 is booked by Mary Smith</li>
				</ul>
			</p>
		</td>
		<td width="50%" valign="top">
			<?php if(count($active_users) > 0){ ?>
			<h2>People currently logged in</h2>
			<ul><?php
				foreach($active_users as $user_id => $display){
					$class = ' ';
					if($user_id == $this->session->userdata('user_id')){
						$class = ' style="color:red"';
						$display .= ' (you)';
					}
					echo sprintf('<li%s>%s</li>', $class, $display);
				}
			?></ul>
			<?php } ?>
		</td>
	</tr>
</table>