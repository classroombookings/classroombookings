
<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="border-table table-align-vat"
	up-data='<?= json_encode($sort_cols) ?>'
	id="<?= $id ?>"
>
	<col /><col /><col /><col />
	<thead>
		<tr class="heading">
			<td class="h" width="20%" title="Name">Name</td>
			<td class="h" width="10%" title="Current?">Current?</td>
			<td class="h" width="10%" title="Available?">Available?</td>
			<td class="h" width="25%" title="Start date">Start date</td>
			<td class="h" width="25%" title="End date">End date</td>
			<td class="h" width="10%" title="Actions"></td>
		</tr>
	</thead>

	<?php if (empty($items)): ?>

	<tbody>
		<tr>
			<td colspan="6" align="center" style="padding:16px 0; color: #666">No sessions.</td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		$dateFormat = setting('date_format_long', 'crbs');

		foreach ($items as $session) {

			echo "<tr>";

			$name = html_escape($session->name);
			$link = anchor("sessions/view/{$session->session_id}", $name);
			echo "<td>{$link}</td>";

			// Current
			$img = '';
			if ($session->is_current == 1) {
				$img = img(['src' => 'assets/images/ui/enabled.png', 'width' => '16', 'height' => '16', 'alt' => 'Current session']);
			}
			echo "<td>{$img}</td>";

			// Selectable
			$img = '';
			if ($session->is_selectable == 1) {
				$img = img(['src' => 'assets/images/ui/enabled.png', 'width' => '16', 'height' => '16', 'alt' => 'Selectable']);
			}
			echo "<td>{$img}</td>";

			$start = $session->date_start ? $session->date_start->format($dateFormat) : '';
			echo "<td>{$start}</td>";

			$end = $session->date_end ? $session->date_end->format($dateFormat) : '';
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

