<td class="<?= $class ?>" data-up-popup>

	<button class="bookings-grid-button" disabled>

		<?php
		echo img([
			'role' => 'button',
			'src' => 'assets/images/ui/school_manage_holidays.png',
			'alt' => 'Holiday',
		]);
		?>
	</button>

	<div data-up-popup-content hidden>
		<div class='content'>
			<?= html_escape($slot->label) ?>
		</div>
	</div>

</td>
