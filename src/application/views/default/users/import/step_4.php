<div class="grid_12 form">

	<h3 class="sub-heading"><?php echo lang('users_import_complete_summary') ?></h3>
	
	<ul class="nav action-statuses">
		<li><span class="label black" data-action=""><?php echo lang('users_import_action_total') ?>: <?php echo count($import['result']['users']) ?></span></li>
		<li><span class="label grey" data-action="ignored"><?php echo lang('users_import_action_ignored') ?>: <?php echo $import['result']['ignored'] ?></span></li>
		<li><span class="label blue" data-action="skipped"><?php echo lang('users_import_action_skipped') ?>: <?php echo $import['result']['skipped'] ?></span></li>
		<li><span class="label green" data-action="added"><?php echo lang('users_import_action_added') ?>: <?php echo $import['result']['added'] ?></span></li>
		<li><span class="label orange" data-action="updated"><?php echo lang('users_import_action_updated') ?>: <?php echo $import['result']['updated'] ?></span></li>
		<li><span class="label red" data-action="failed"><?php echo lang('users_import_action_failed') ?>: <?php echo $import['result']['failed'] ?></span></li>
	</ul>
	
	<br><hr>
	
	<table class="list users-preview">
		
		<thead>
			<tr>
				<th><?php echo lang('users_username') ?></th>
				<th><?php echo lang('users_display') ?></th>
				<th><?php echo lang('users_email') ?></th>
				<th class="text-right"><?php echo lang('status') ?></th>
			</tr>
		</thead>
		
		<tbody>
			
			<?php foreach ($import['result']['users'] as $user): ?>
			
			<tr data-action="<?php echo $user['action'] ?>">
				<td><?php echo $user['u_username'] ?></td>
				<td><?php echo $user['u_display'] ?></td>
				<td><?php echo $user['u_email'] ?></td>
				<td class="text-right"><?php echo user_import_status($user) ?></td>
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
	</table>
	
	
	<div class="row submit">
			<div class="grid_9 offset_3 text-right">
				<?php echo form_button(array(
					'type' => 'link',
					'url' => 'users/import/finish',
					'class' => 'blue right',
					'text' => lang('next'),
					'tab_index' => tab_index(),
				)) ?>
			</div>
		</div>
		
	
</div>

<script>
Q.push(function() {
	$(".action-statuses").on("click", "span", function(e) {
		var status = $(this).data("action");
		var all_rows = $("tr[data-action]");
		if (status.length === 0) {
			all_rows.show();
			return;
		}
		var selected_rows = $("tr[data-action='" + status + "']");
		all_rows.hide();
		selected_rows.show();
	});
	$(".action-statuses span").css("cursor", "pointer");
});
</script>