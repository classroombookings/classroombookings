<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
	
		<label for="l_username"><?php echo lang('username') ?></label>
		<?php echo form_input(array(
			'name' => 'l_username',
			'value' => element('l_username', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="l_ip"><?php echo lang('event_log_ip') ?></label>
		<?php echo form_input(array(
			'name' => 'l_ip',
			'value' => element('l_ip', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="l_area"><?php echo lang('event_log_area') ?></label>
		<?php
		$area_dropdown = array('' => '(' . lang('any') . ')');
		$area_dropdown += $areas;
		echo form_dropdown('l_area', $area_dropdown, element('l_area', $filter, ''), 'class="text-input"');
		?>
		
		<label for="l_type"><?php echo lang('event_log_type') ?></label>
		<?php
		$type_dropdown = array('' => '(' . lang('any') . ')');
		$type_dropdown += $types;
		echo form_dropdown('l_type', $type_dropdown, element('l_type', $filter, ''), 'class="text-input"');
		?>
		
		<label for="pp"><?php echo lang('per_page') ?></label>
		<?php
		echo form_dropdown('pp', config_item('per_page'), element('pp', $filter, ''), 'class="text-input"');
		?>
		
		<input type="submit" class="primary button" name="filter" value="<?php echo lang('filter') ?>">
		<a href="<?php echo site_url('event_log') ?>" class="empty button"><?php echo lang('clear') ?></a>
		
	</div>

</form>


