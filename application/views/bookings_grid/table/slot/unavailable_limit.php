<td class='<?= $class ?>'>

	<?php
	$icon = 'error.png';
	switch ($slot->extended) {
		case 'quota': $icon = 'stop.png'; break;
		case 'past': $icon = 'no.png'; break;
		case 'future': $icon = 'date_error.png'; break;
	}
	echo img([
		'role' => 'button',
		'src' => 'assets/images/ui/' . $icon,
		'data-up-popup' => '',
		'alt' => 'Limit',
	]);
	?>

	<div data-up-popup-content hidden>
		<div class='content'>
			<?= html_escape($slot->label) ?>
		</div>
	</div>

</td>
