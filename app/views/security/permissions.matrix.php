<table class="form a-t" cellpadding="0" cellspacing="0" border="0">

	<tr>
		<td width="300">
			<!-- GENERAL -->
			<?php
			unset($options);
			$options[] = array('dashboard', 'Dashboard', NULL, TRUE);
			$options[] = array('dashboard/viewdept', 'Dashboard - view department bookings', NULL, FALSE);
			$options[] = array('dashboard/viewown', 'Dashboard - view own bookings', NULL, TRUE);
			$options[] = array('myprofile', 'My Profile/change password', NULL, FALSE);
			$this->load->view('security/permissions.checks.php', array('category' => 'General', 'options' => $options));
			?>
		</td>
		<td width="50">&nbsp;</td>
		<td width="300">
			<!-- BOOKINGS -->
			<?php
			unset($options);
			$options[] = array('bookings', 'View bookings page', 'View the main bookings page', TRUE);
			$options[] = array('bookings/createone', 'Create their own one-time bookings', 'Booking days ahead and quota options still apply', FALSE);
			$options[] = array('bookings/createrecur', 'Create recurring bookings', 'Bookings that occur on every timetabled week', TRUE);
			$options[] = array('bookings/deleteone/own', 'Delete their own one-time bookings', NULL, FALSE);
			$options[] = array('bookings/deleteone/ifowner', 'Delete room one-time bookings if room owner', NULL, TRUE);
			$options[] = array('bookings/deleterecur/ifowner', 'Delete room recurring bookings if room owner', NULL, FALSE);
			$options[] = array('bookings/overwriteone', 'Can overwrite other one-time bookings', NULL, TRUE);
			$options[] = array('bookings/overwriterecur', 'Can overwrite other recurring bookings with one-time booking', NULL, FALSE);
			$options[] = array('bookings/overwriteone/ifowner', 'Can overwrite other one-time bookings if room owner', NULL, FALSE);
			$options[] = array('bookings/overwriterecur/ifowner', 'Can overwrite other recurring bookings if room owner', NULL, FALSE);
			$this->load->view('security/permissions.checks.php', array('category' => 'Room booking', 'options' => $options));
			?>
		</td>
	</tr>
	
	<tr>
		<td width="300">
			<!-- ROOMS -->
			<?php
			unset($options);
			$options[] = array('rooms', 'Rooms', 'Has access to the Rooms page', TRUE);
			$options[] = array('rooms/add', 'Add a room', NULL, FALSE);
			$options[] = array('rooms/edit', 'Edit room properties', NULL, TRUE);
			$options[] = array('rooms/delete', 'Delete a room', NULL, FALSE);
			$options[] = array('rooms/changephoto', 'Change photo', NULL, TRUE);
			$options[] = array('rooms/changefields', 'Change fields', NULL, TRUE);
			$options[] = array('rooms/changefieldvalues', 'Change field values', NULL, TRUE);
			$this->load->view('security/permissions.checks.php', array('category' => 'Rooms', 'options' => $options));
			?>
		</td>
		<td width="50">&nbsp;</td>
		<td width="300">
			<!-- PERIODS -->
			<?php
			unset($options);
			$options[] = array('periods', 'Periods', NULL, TRUE);
			$options[] = array('periods/add', 'Add a period', NULL, FALSE);
			$options[] = array('periods/edit', 'Edit a period/change times', NULL, TRUE);
			$options[] = array('periods/delete', 'Delete a period', NULL, FALSE);
			$this->load->view('security/permissions.checks.php', array('category' => 'Periods', 'options' => $options));
			?>
		</td>
	</tr>
	
	<tr>
		<td width="300">
			<!-- WEEKS -->
			<?php
			unset($options);
			$options[] = array('weeks', 'Weeks', 'Has access to the Weeks page', TRUE);
			$options[] = array('weeks/add', 'Add a timetable week', NULL, FALSE);
			$options[] = array('weeks/edit', 'Edit a week and set its dates', NULL, TRUE);
			$options[] = array('weeks/delete', 'Delete a week', NULL, FALSE);
			$options[] = array('weeks/ayears/manage', 'Manage the academic year dates', NULL, FALSE);
			$options[] = array('weeks/ayears/set', 'Set the current academic year', NULL, TRUE);
			$this->load->view('security/permissions.checks.php', array('category' => 'Timetable weeks', 'options' => $options));
			?>
		</td>
		<td width="50">&nbsp;</td>
		<td width="300">
			<!-- HOLIDAYS -->
			<?php
			unset($options);
			$options[] = array('holidays', 'Holidays', NULL, TRUE);
			$options[] = array('holidays/add', 'Add a holiday', NULL, FALSE);
			$options[] = array('holidays/edit', 'Edit school holidays', NULL, TRUE);
			$options[] = array('holidays/delete', 'Delete a holiday', NULL, FALSE);
			$this->load->view('security/permissions.checks.php', array('category' => 'Holidays', 'options' => $options));
			?>
		</td>
	</tr>
	
	<tr>
		<td colspan="3">
			<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
				
				<tr class="h"><td colspan="2">Other options</td></tr>
				
				<tr>
					<td class="caption"><label for="schoolname" title="The number of days ahead users can create a booking. Enter 0 or leave blank to allow bookings at any time in the future.">Booking ahead</label></td>
					<td class="field">
					  <?php
						#$ = @field($this->validation->username);
						echo form_input(array(
							'name' => 'book-ahead',
							'id' => 'book-ahead',
							'size' => '10',
							'maxlenght' => '3',
							'value' => '',
						));
						?>
					</td>
				</tr>
				
				<tr>
					<td class="caption"><label for="schoolname" title="The number of bookings a user in this group can make in a given period of time.">Booking quota</label></td>
					<td class="field">
						<?php
						#$ = @field($this->validation->username);
						echo form_input(array(
							'name' => 'quota',
							'id' => 'quota',
							'size' => '10',
							'maxlenght' => '3',
							'value' => '',
						));
						?>
						<label for="quota-none" class="check">
						<?php
						#$ = @field($this->validation->username);
						echo form_radio(array(
							'name' => 'quota',
							'id' => 'quota-none',
							'value' => 'none',
							'checked' => TRUE,
						));
						?>(None)
						</label>
						<label for="quota-per-week" class="check">
						<?php
						#$ = @field($this->validation->username);
						echo form_radio(array(
							'name' => 'quota',
							'id' => 'quota-per-week',
							'value' => 'week',
							'checked' => FALSE,
						));
						?>Per week
						</label>
						<label for="quota-per-month" class="check">
						<?php
						#$ = @field($this->validation->username);
						echo form_radio(array(
							'name' => 'quota',
							'id' => 'quota-per-month',
							'value' => 'month',
							'checked' => FALSE,
						));
						?>Per month
						</label>
					</td>
				</tr>
				
				<?php
				unset($buttons);
				$buttons[] = array('submit', 'positive', 'Save group permissions', 'disk1.gif', 0);
				#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
				#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('dashboard'));
				$this->load->view('parts/buttons', array('buttons' => $buttons));
				?>
				
			</table>
		</td>
	</tr>
</table>