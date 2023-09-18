<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

echo iconbar([
	array('weeks/add', 'Add Week', 'add.png'),
]);

$sort_cols = ["Name", "Colour", "None"];

?>

<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="zebra-table sort-table"
	id="jsst-weeks"
	up-data='<?= json_encode($sort_cols) ?>'
>
	<col /><col /><col />

	<thead>
		<tr class="heading">
			<td class="h" width="5%" title="Colour"></td>
			<td class="h" width="85%" title="Name">Name</td>
			<!-- <td class="h" title="Colour">Colour</td> -->
			<td class="n" width="10%" title="X">&nbsp;</td>
		</tr>
	</thead>

	<?php if (empty($weeks)): ?>

	<tbody>
		<tr>
			<td colspan="4" align="center" style="padding:16px 0; color: #666">No weeks.</td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		foreach ($weeks as $week) {

			echo "<tr>";

			$dot = week_dot($week);
			echo "<td style='text-align:center'>{$dot}</td>";

			$name = html_escape($week->name);
			echo "<td>{$name}</td>";

			echo "<td>";
			$actions['edit'] = 'weeks/edit/'.$week->week_id;
			$actions['delete'] = 'weeks/delete/'.$week->week_id;
			$this->load->view('partials/editdelete', $actions);
			echo "</td>";

			echo "</tr>";

		}

		?>
	</tbody>

	<?php endif; ?>

</table>
