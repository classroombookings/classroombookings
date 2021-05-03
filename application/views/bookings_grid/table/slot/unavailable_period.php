<td class='<?= $class ?>'>

	<?php
	echo img([
		'role' => 'button',
		'src' => 'assets/images/ui/clock.png',
		'data-up-popup' => '',
		'alt' => 'Period not available',
	]);
	?>

	<div data-up-popup-content hidden>
		<div class='content'>
			<?= html_escape($slot->label) ?>
		</div>
	</div>

</td>
