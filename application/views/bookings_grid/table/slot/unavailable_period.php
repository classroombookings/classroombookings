<td class="<?= $class ?>">
	<a href="#"
		class="bookings-grid-button"
		up-layer="new popup"
		up-align="top"
		up-size="medium"
		up-content="<p><?= html_escape($slot->label) ?></p><button up-dismiss>OK</button>"
	>
		<?php
		echo img([
			'role' => 'button',
			'src' => 'assets/images/ui/clock.png',
			'alt' => 'Period not available',
		]);
		?>
	</a>
</td>
