<td class="<?= $class ?>">
	<a href="#"
		class="bookings-grid-button"
		up-layer="new popup"
		up-align="top"
		up-size="medium"
		up-content="<p><?= html_escape($slot->label) ?></p><button up-dismiss><?= lang('app.ok') ?></button>"
	>
		<?php
		echo img([
			'role' => 'button',
			'src' => asset_url('assets/images/ui/school_manage_holidays.png'),
			'alt' => lang('holiday.holiday'),
		]);
		?>
	</a>
</td>
