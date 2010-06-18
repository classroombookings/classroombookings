<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>

<!--
<ul id="pagetabs" class="subsection_tabs">
<?php
foreach($groups as $group_id => $group_name){
	echo '<li class="tab"><a href="'.current_url().'#g'.$group_id.'">'.$group_name.'</a></li>';
}
?>
</ul>
-->

<!-- <div class="tabber" id="tabs-permissions"> -->

<!-- #tabs -->
<div id="tabs">

	<ul>
		<?php
		foreach($groups as $group_id => $group_name){
			$class = '';
			if($tab == $group_id){ $class = ' class="ui-tabs-selected"'; }
			echo '<li' . $class . '><a href="'.current_url().'#g'.$group_id.'">'.$group_name.'</a></li>';
		}
		?>
	</ul>

	<?php
	foreach($groups as $group_id => $group_name){
		#echo '<div class="tabbertab' . (("$tab" == "$group_id") ? ' tabbertabdefault' : '') . '">';
		echo '<div id="g'.$group_id.'">';
		#echo '<h2>' . $group_name . '</h2>';
		echo form_open('security/permissions/save', NULL, array('group_id' => $group_id));
	?>
			
		<table class="form a-t" cellpadding="0" cellspacing="0" border="0">
			
			<tr>
				<td width="400">
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
				<td width="400">
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
					<!-- ACADEMIC -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['academic'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Academic setup';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- PERIODS -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['periods'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Academic - Periods';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
				<td width="50">&nbsp;</td>
				<td>
					<!-- WEEKS -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['weeks'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Academic - Weeks';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- TERM DATES -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['terms'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Academic - Term dates';
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
					$checks['category'] = 'Academic - Holidays';
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
					$checks['category'] = 'Academic - Departments';
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
				<td>
					<!-- USERS -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['users'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Security - Users';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
				<td width="50">&nbsp;</td>
				<td>
					<!-- GROUPS -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['groups'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Security - Groups';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- PERMISSIONS -->
					<?php
					unset($checks);
					$checks['options'] = $permissions['permissions'];
					$checks['group_id'] = $group_id;
					$checks['category'] = 'Security - Permissions';
					$this->load->view('security/permissions.checks.php', $checks);
					?>
				</td>
				<td width="50">&nbsp;</td>
				<td>
					&nbsp;
				</td>
			</tr>
			
			<tr>
				<td colspan="3">
					<table class="form" cellpadding="0" cellspacing="0" border="0" width="100%">
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

</div>
<!-- // #tabs -->


<script type="text/javascript">
$(function() {
	$("#tabs").tabs();
});
</script>