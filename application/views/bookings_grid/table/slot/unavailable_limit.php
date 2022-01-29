<td class="<?= $class ?>">
	<a href="#"
		class="bookings-grid-button"
		up-layer="new popup"
		up-align="top"
		up-size="medium"
		up-content="<p><?= html_escape($slot->label) ?></p><button up-dismiss>OK</button>"
	>
		<?php
		$icon = 'error.png';
		switch ($extended) {
			case 'quota': $icon = 'stop.png'; break;
			case 'past': $icon = 'date_previous.png'; break;
			case 'future': $icon = 'date_error.png'; break;
		}
		echo img([
			'role' => 'button',
			'src' => 'assets/images/ui/' . $icon,
			'alt' => 'Limit',
		]);
		?>
	</a>
</td>
