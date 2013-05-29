<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
	
		<label for="d_name"><?php echo lang('departments_name') ?></label>
		<?php echo form_input(array(
			'name' => 'd_name',
			'value' => element('d_name', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="d_description"><?php echo lang('departments_description') ?></label>
		<?php echo form_input(array(
			'name' => 'd_description',
			'value' => element('d_description', $filter),
			'class' => 'text-input',
		)) ?>
		
		<label for="pp"><?php echo lang('per_page') ?></label>
		<?php
		echo form_dropdown('pp', config_item('per_page'), element('pp', $filter, ''), 'class="text-input"');
		?>
		
		<input type="submit" class="primary button" name="filter" value="<?php echo lang('filter') ?>">
		<a href="<?php echo site_url('departments') ?>" class="empty button"><?php echo lang('clear') ?></a>
		
	</div>

</form>


