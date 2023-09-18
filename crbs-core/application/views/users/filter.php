<div class="filter-form add-bottom">

	<?php

	$attrs = [
		'class' => 'cssform-stacked',
		'id' => 'users_filter',
		'method' => 'GET',
		'up-autosubmit' => '',
		'up-target' => '#users_list',
	];

	echo form_open('users', $attrs);

	?>

	<div class="block-group">

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label('Search', 'q');
				$value = set_value('q');
				echo form_input([
					'name' => 'q',
					'id' => 'q',
				]);
			?>
			</p>
		</div>

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label('&nbsp;');

				echo anchor('users', 'Clear', ['class' => 'button']);
			?>
			</p>
		</div>
 	</div>

	<?php echo form_close(); ?>

</div>
