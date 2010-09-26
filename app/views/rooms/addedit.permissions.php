<?php
$t = 0;

$t_letter = array('e', 'o', 'u', 'g', 'd');
$t_word = array('Everyone', 'Room owner', 'User', 'Group', 'Department');
?>
<!-- Current permissions -->

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Who">Who</td>
		<td class="h" title="Who">&nbsp;</td>
		<td class="h" title="Permissions">Permissions</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	
	<?php
	if(!empty($entries)){
		foreach($entries as $entry_id => $entry){
			?>
			<tr>
				<td valign="top"><strong><?php echo $this->types[$entry['type']] ?></strong></td>
				<td valign="top"><?php if(isset($entry['object_name'])){ echo $entry['object_name']; } ?></td>
				<td valign="top"><?php
					foreach($entry['permissions'] as $p){
						echo '<span>' . $room_permissions[$p] . '</span><br />';
					}
				?></td>
				<td class="il">
				<?php
				unset($actiondata);
				$actiondata[] = array('rooms/manage/delete_permission/'.$entry_id, ' ', 'cross_sm.gif', 'Delete this permission');
				$this->load->view('parts/linkbar', $actiondata);
				?>	
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="3">No permission entries have been added yet.</td>
		</tr>
		<?php
	}
	?>
	
	</tbody>
	
</table>




<?php echo form_open('rooms/manage/save_permission', NULL, array('room_id' => $room_id)); ?>
<!-- Add a new permission entry -->
<!-- main table -->
<div class="grey"><div>
<table class="form" cellpadding="0" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="3">Add permission entry</td></tr>
	
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
						$radio['checked'] = set_radio($radio['name'], $radio['value']);
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
						$radio['checked'] = set_radio($radio['name'], $radio['value']);
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
						$radio['checked'] = set_radio($radio['name'], $radio['value']);
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
						$radio['checked'] = set_radio($radio['name'], $radio['value']);
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
						$radio['checked'] = set_radio($radio['name'], $radio['value']);
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
		<td valign="top" id="permchecks">
			<div id="toggleall" style="display:none">(<a href="javascript:toggle()" id="toggleall">Toggle All</a>)</div>
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
</div></div>

	
<?php
unset($buttons);
$buttons[] = array('submit', 'ok', 'Add permission entry', $t);
#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
$buttons[] = array('link', 'cancel', 'Cancel', $t+1, site_url('rooms/manage'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>


</form>



<script type="text/javascript">
$('#toggleall').show();
ticked = false;
function toggle(){
	$("#permchecks input:checkbox").attr('checked', !ticked);
	ticked = !ticked;
}
</script>