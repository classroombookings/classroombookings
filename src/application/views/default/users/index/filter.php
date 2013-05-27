<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
		
		<label for="u_username"><?php echo lang('username') ?></label>
		<?php echo form_input(array(
			'name' => 'u_username',
			'value' => element('u_username', $filter),
			'class' => 'text-input',
		)) ?>
		
		
		<label for="u_email"><?php echo lang('email') ?></label>
		<?php echo form_input(array(
			'name' => 'u_email',
			'value' => element('u_email', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="u_g_id"><?php echo lang('group') ?></label>
		<?php
		$groups_dropdown = array('' => '(' . lang('any') . ')');
		$groups_dropdown += $groups;
		echo form_dropdown('u_g_id', $groups_dropdown, element('u_g_id', $filter, ''), 'class="text-input"');
		?>
		
		<label for="u_status"><?php echo lang('status') ?></label>
		<?php
		$statuses = array(
			'' => '(' . lang('any') . ')',
			'1' => lang('enabled'),
			'0' => lang('disabled'),
		);
		echo form_dropdown('u_enabled', $statuses, element('u_enabled', $filter, ''), 'class="text-input"');
		?>
		
		<label for="pp"><?php echo lang('per_page') ?></label>
		<?php
		echo form_dropdown('pp', config_item('per_page'), element('pp', $filter, ''), 'class="text-input"');
		?>
		
		
		<input type="submit" class="black button" name="filter" value="<?php echo lang('filter') ?>">
		<a href="<?php echo site_url('users') ?>" class="grey button"><?php echo lang('clear') ?></a>
	
	</div>

</form>


