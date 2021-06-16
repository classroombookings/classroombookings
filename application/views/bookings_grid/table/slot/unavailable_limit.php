<td class="<?= $class ?>" data-up-popup>

	<button class="bookings-grid-button" disabled>

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
			'alt' => 'Limit',
		]);
		?>

	</button>

	<div data-up-popup-content hidden>
		<div class='content'>
			<?= html_escape($slot->label) ?>
		</div>
	</div>

</td>
