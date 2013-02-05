<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
	
		<table class="filter">
			<tr>
				<td><label for="g_name"><?php echo lang('name') ?></label></td>
				<td><label for="pp"><?php echo lang('per_page') ?></label></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<?php echo form_input(array(
						'name' => 'g_name',
						'value' => element('g_name', $filter),
						'class' => 'text-input',
					)) ?>
				</td>
				
				<td>
					<?php
					echo form_dropdown('pp', config_item('per_page'), element('pp', $filter, ''), 'class="text-input"');
					?>
				</td>
				
				<td class="text-right">
					<input type="submit" class="black button" value="<?php echo lang('filter') ?>">
					<a href="<?php echo site_url('users') ?>" class="grey button"><?php echo lang('clear') ?></a>
				</td>
				
			</tr>
			
		</table>
	
	</div>

</form>


