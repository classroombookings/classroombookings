<td class="<?= $class ?>" data-up-popup>

	<button class="bookings-grid-button" disabled>

		<?php
		echo img([
			'role' => 'button',
			'src' => 'assets/images/ui/clock.png',
			// 'data-up-popup' => '',
			'alt' => 'Period not available',
		]);
		?>

		<div data-up-popup-content hidden>
			<div class='content'>
				<?= html_escape($slot->label) ?>
			</div>
		</div>

	</button>

</td>
