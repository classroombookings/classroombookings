<td class="<?= $class ?>">
	<a href="#"
		class="bookings-grid-button"
		up-layer="new popup"
		up-align="top"
		up-size="medium"
		up-content="<p><?= html_escape($slot->label) ?></p><button up-dismiss><?= lang('app.ok') ?></button>"
	>
		<?php
		$icon = 'date_previous.png';
		echo img([
			'role' => 'button',
			'src' => asset_url('assets/images/ui/' . $icon),
			'alt' => '',
		]);
		?>
	</a>
</td>
