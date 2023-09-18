<?php
$messages = $this->session->flashdata('saved');
echo "<div class='messages' up-hungry>{$messages}</div>";

$sort_cols = ["Room", "Who", "Permission"];

$this->load->view('access_control/add_link');

$this->load->view('access_control/filter');

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="border-table table-align-vat" up-data='<?= json_encode($sort_cols) ?>' id="access_control_list" up-hungry>
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" width="20%" title="Room">Room</td>
		<td class="h" width="40%" title="Who">Who</td>
		<td class="h" width="10%" title="Permission">Permission</td>
		<td class="h" width="5%" title="X"></td>
	</tr>
	</thead>
	<?php if ( ! empty($grouped_items)): ?>

		<?php foreach ($grouped_items as $_t => $target): ?>
		<?php $i = 0; ?>
		<tbody class="has-border">
			<?php foreach ($target['actors'] as $_a => $actor): ?>
			<?php foreach ($actor['items'] as $item): ?>
			<tr>
				<td>
					<?php if ($i === 0): ?>
					<p><?= html_escape($target['name']) ?></p>
					<?php endif; ?>
				</td>
				<td>
					<?php
					echo "<p>";
					if ($actor['name']) {
						echo "<strong>{$actor['name']}</strong>";
						echo "<br>";
					}
					if ($actor['type'] !== $actor['name']) {
						echo $actor['type'];
					}
					echo "</p>";
					?>
				</td>
				<td>
					<p><?= $item->permission_name ?></p>
				</td>
				<td width="45" class="n">
					<?php
					$img = img('assets/images/ui/delete.png', FALSE, "hspace='2' border='0' alt='Delete'");
					echo "<p>" . anchor('access_control/delete/' . $item->id, $img, [
						'title' => 'Delete',
						'up-follow' => '',
						'up-method' => 'POST',
						'up-confirm' => 'Are you sure you want to remove this entry?',
					]) . "</p>";
					?>
				</td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
			<?php endforeach; ?>
		</tbody>
		<?php endforeach; ?>

	<?php else: ?>

		<tbody>
			<tr>
				<td colspan="5" align="center" style="padding:16px 0">No access control entries found!</td>
			</tr>
		</tbody>

	<?php endif; ?>

</table>

