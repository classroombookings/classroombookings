<?php

$classes = ['bookings-grid-header'];

if ($week) {
	$classes[] = 'has-week';
	$classes[] = sprintf('week%d-bg', $week->week_id);
	$classes[] = sprintf('week%d-fg', $week->week_id);
}

?>

<div class="<?= implode(' ', $classes) ?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>

			<td width="20%" align="left">
				<?php
				if ($prev) {
					echo anchor($prev['url'], $prev['label'], [
						'up-follow' => '',
						// 'up-instant' => '',
						'up-preload' => '',
					]);
				}
				?>
			</td>

			<td align="center">
				<?= $title ?>
			</td>

			<td width="20%" align="right">
				<?php
				if ($next) {
					echo anchor($next['url'], $next['label'], [
						'up-follow' => '',
						// 'up-instant' => '',
						'up-preload' => '',
					]);
				}
				?>
			</td>
		</tr>
	</table>
</div>

