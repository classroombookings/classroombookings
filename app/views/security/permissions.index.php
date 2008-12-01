<?php

?>


<p>Listed below are the groups that exist within Classroombookings. In each tab, it is possible to configure what users belonging to that group are allowed and not allowed to do.</p>


<div class="tabber" id="tabs-permissions">

	<?php
	foreach($groups as $group_id => $group_name){
	
		echo '<div class="tabbertab">';
		echo '<h2>' . $group_name . '</h2>';
		//$this->load->view('security/permissions.matrix.php', array('group_id' => $group_id));
		echo form_open('security/permissions/save', NULL, array('group_id' => $group_id));
	?>
			
			<table class="form a-t" cellpadding="0" cellspacing="0" border="0">
				
				<tr>
					<td width="250">
						<!-- GENERAL -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['general'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'General';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td width="350">
						<!-- BOOKINGS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['bookings'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Bookings';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
				</tr>
				
				<tr>
					<td>
						<!-- ROOMS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['rooms'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Rooms';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>
						<!-- PERIODS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['periods'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Periods';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
				</tr>
				
				<tr>
					<td>
						<!-- WEEKS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['weeks'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Weeks / Academic year';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>
						<!-- HOLIDAYS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['holidays'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Holidays';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
				</tr>
				
				<tr>
					<td>
						<!-- DEPARTMENTS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['departments'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Departments';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>
						<!-- REPORTS -->
						<?php
						unset($checks);
						$checks['options'] = $permissions['reports'];
						$checks['group_id'] = $group_id;
						$checks['category'] = 'Reports';
						$this->load->view('security/permissions.checks.php', $checks);
						?>
					</td>
				</tr>
				
				<tr>
					<td colspan="3">
						<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
							
							<tr class="h"><td colspan="2">Other options</td></tr>
							
							<tr>
								<td class="caption"><label for="p_<?php echo $group_id ?>_daysahead" title="The number of days ahead users can create a booking. Leave blank to allow bookings at any time in the future.">Booking ahead</label></td>
								<td class="field">
									<?php
								  	unset($input);
									$input['name'] = 'daysahead';
									$input['id'] = "p_{$group_id}_daysahead";
									$input['size'] = '10';
									$input['maxlength'] = '3';
									$input['value'] = set_value('days-ahead', $group_permissions[$group_id]->daysahead);
									echo form_input($input);
									?>
								</td>
							</tr>
							
							<tr>
								<td class="caption"><label for="p_<?php echo $group_id ?>_quota" title="The number of bookings a user in this group can make in a given period of time.">Booking quota</label></td>
								<td class="field">
									<?php
									unset($input);
									$input['name'] = 'quota';
									$input['id'] = "p_{$group_id}_quota";
									$input['size'] = '10';
									$input['maxlength'] = '3';
									$input['value'] = set_value('quota', $group_permissions[$group_id]->quota);
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
							$this->load->view('parts/buttons', array('buttons' => $buttons));
							?>
							
						</table>
					</td>
				</tr>
			</table>
			</form>
			
	<?php
		
		echo '</div>';
	}
	?>

	<?php
	/* <div class="tabbertab">
		<h2>Guests</h2>
        <?php $this->load->view('security/permissions.matrix.php'); ?>
	</div>


	<div class="tabbertab">
		<h2>Administrators</h2>
		<?php $this->load->view('security/permissions.matrix.php'); ?>
	</div>


	<div class="tabbertab">
		<h2>Teachers</h2>
		<?php $this->load->view('security/permissions.matrix.php'); ?>
	</div> */
	?>

</div>
