
<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="border-table table-align-vat"
	id="<?= $id ?>"
>
	<col /><col /><col /><col />
	<thead>
		<tr class="heading">
			<td class="h" width="20%"><?= lang('session.field.name') ?></td>
			<td class="h" width="10%"><?= lang('session.field.is_current') ?></td>
			<td class="h" width="15%"><?= lang('session.field.is_selectable') ?></td>
			<td class="h" width="25%"><?= lang('session.field.date_start') ?></td>
			<td class="h" width="25%"><?= lang('session.field.date_end') ?></td>
			<td class="h" width="5%"></td>
		</tr>
	</thead>

	<?php if (empty($items)): ?>

	<tbody>
		<tr>
			<td colspan="6" align="center" style="padding:16px 0; color: #666">
				<?= lang('session.no_items') ?>
			</td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		foreach ($items as $session) {

			echo "<tr>";

			$name = html_escape($session->name);
			$link = anchor("sessions/view/{$session->session_id}", $name);
			echo "<td>{$link}</td>";

			// Current
			$img = '';
			if ($session->is_current == 1) {
				$img = img(['src' => asset_url('assets/images/ui/enabled.png'), 'width' => '16', 'height' => '16', 'alt' => lang('session.field.is_current')]);
			}
			echo "<td>{$img}</td>";

			// Selectable
			$img = '';
			if ($session->is_selectable == 1) {
				$img = img(['src' => asset_url('assets/images/ui/enabled.png'), 'width' => '16', 'height' => '16', 'alt' => lang('session.field.is_selectable')]);
			}
			echo "<td>{$img}</td>";

			$start = $session->date_start ? date_output_long($session->date_start) : '';
			echo "<td>{$start}</td>";

			$end = $session->date_end ? date_output_long($session->date_end) : '';
			echo "<td>{$end}</td>";

			echo "<td>";
			$actions['delete'] = 'sessions/delete/'.$session->session_id;
			$this->load->view('partials/editdelete', $actions);
			echo "</td>";

			echo "</tr>";

		}

		?>
	</tbody>

	<?php endif; ?>

</table>

