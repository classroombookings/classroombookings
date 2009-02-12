<?php
$t = 0;
?>
<!-- Current permissions -->

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Who">Who</td>
		<td class="h" title="Permissions">Permissions</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	
	<?php
	if(!empty($room->permissions)){
		foreach($room->permissions as $permission){
			?>
			<tr>
				<td class="x" valign="top">Everyone</td>
				<td class="x" valign="top"><p>Make a single booking</p><p>Make recurring bookings</p></td>
				<td class="il">
				<?php
				unset($actiondata);
				$actiondata[] = array('rooms/delete/'.$room->room_id, 'Delete', 'cross_sm.gif' );
				$this->load->view('parts/listactions', $actiondata);
				?>	
				</td>
			</tr>
			<?php
			$i++;
		}
	} else {
		?>
		<tr>
			<td colspan="3">No permission entries have been created yet.</td>
		</tr>
		<?php
	}
	?>
	
	</tbody>
	
</table>



<?php echo form_open_multipart('rooms/save_permissions', NULL, array('room_id' => $room_id)); ?>
<!-- Add a new permission entry -->
<!-- main table -->
<table class="form" cellpadding="0" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">Add permission entry</td></tr>
	
	<tr>
		
		<!-- left col : objects -->
		<td valign="top" width="40%">
			
			<!-- table for object selection -->
			<table class="form rp list" cellpadding="0" cellspacing="0" border="0" width="100%">
				
				<!-- row : object : everyone -->
				<tr>
					<td width="20" align="center">
						<?php
						unset($radio);
						$radio['name'] = 'object';
						$radio['id'] = 'object_everyone';
						$radio['value'] = 'everyone';
						$radio['tabindex'] = $t;
						echo form_radio($radio);
						$t++;
						?>
					</td>
					
					<td width="100" class="caption">
						<label for="object_everyone">Everyone</label>
					</td>
					
					<td width="250">&nbsp;</td>
				</tr>
				
				<!-- row : object : room owner -->
				<tr>
					<td width="20" align="center">
						<?php
						unset($radio);
						$radio['name'] = 'object';
						$radio['id'] = 'object_roomowner';
						$radio['value'] = 'roomowner';
						$radio['tabindex'] = $t;
						echo form_radio($radio);
						$t++;
						?>
					</td>
					
					<td width="100" class="caption">
						<label for="object_roomowner">Room owner</label>
					</td>
					
					<td width="250">&nbsp;</td>
				</tr>
				
				<!-- row : object : user -->
				<tr>
					<td width="20" align="center">
						<?php
						unset($radio);
						$radio['name'] = 'object';
						$radio['id'] = 'object_user';
						$radio['value'] = 'user';
						$radio['tabindex'] = $t;
						echo form_radio($radio);
						$t++;
						?>
					</td>
					
					<td width="100" class="caption">
						<label for="object_user">User</label>
					</td>
					
					<td width="250" class="field">
					<?php
					unset($users[-1]);
					echo form_dropdown('user_id', $users, -1, 'tabindex="'.$t.'"');
					?></td>
				</tr>
				
				<!-- row : object : group -->
				<tr>
					<td width="20" align="center">
						<?php
						unset($radio);
						$radio['name'] = 'object';
						$radio['id'] = 'object_group';
						$radio['value'] = 'group';
						$radio['tabindex'] = $t;
						echo form_radio($radio);
						$t++;
						?>
					</td>
					
					<td width="100" class="caption">
						<label for="object_group">Group</label>
					</td>
					
					<td width="250" class="field">
					<?php
					echo form_dropdown('group_id', $groups, -1, 'tabindex="'.$t.'"');
					?></td>
				</tr>
				
				<!-- row : object : department -->
				<tr>
					<td width="20" align="center">
						<?php
						unset($radio);
						$radio['name'] = 'object';
						$radio['id'] = 'object_department';
						$radio['value'] = 'department';
						$radio['tabindex'] = $t;
						echo form_radio($radio);
						$t++;
						?>
					</td>
					
					<td width="100" class="caption">
						<label for="object_department">Department</label>
					</td>
					
					<td width="250" class="field">
					<?php
					echo form_dropdown('department_id', $departments, -1, 'tabindex="'.$t.'"');
					?></td>
				</tr>
				
			</table>
			<!-- // table for object selection -->
			
		</td>
		
		<td width="50">&nbsp;</td>
		
		<!-- right col : permissions -->
		<td valign="top">
			<?php
			unset($checks);
			$checks['options'] = $permissions['room'];
			$checks['group_id'] = $room_id;
			$checks['category'] = NULL;
			$this->load->view('security/permissions.checks.php', $checks);
			?>
		</td>
		
	</tr>
	
</table>
<!-- // main table -->

	
<?php
unset($buttons);
$buttons[] = array('submit', 'positive', 'Add permission entry', 'disk1.gif', $t);
#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('rooms'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>


</form>