<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
	
		<label for="g_name"><?php echo lang('name') ?></label>
		<?php echo form_input(array(
			'name' => 'g_name',
			'value' => element('g_name', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="pp"><?php echo lang('per_page') ?></label>
		<?php
		echo form_dropdown('pp', config_item('per_page'), element('pp', $filter, ''), 'class="text-input"');
		?>
		
		<input type="submit" class="primary button" name="filter" value="<?php echo lang('filter') ?>">
		<a href="<?php echo site_url('groups') ?>" class="empty button"><?php echo lang('clear') ?></a>
		
	</div>

</form>


