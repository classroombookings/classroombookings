<td class='<?= $class ?>'>

	<?php
	echo img([
		'role' => 'button',
		'src' => 'assets/images/ui/school_manage_holidays.png',
		'data-up-popup' => '',
		'alt' => 'Holiday',
	]);
	?>

	<div data-up-popup-content hidden>
		<div class='content'>
			<?= html_escape($slot->label) ?>
		</div>
	</div>

</td>
