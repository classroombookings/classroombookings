<?php

$open_group_id = $open_group_id ?? null;
$is_open = (!empty($group) && $group->room_group_id == $open_group_id);

?>

<details class="collapse" <?= $is_open ? 'open' : '' ?>>

	<summary class="collapse-header">
		<?php
		if (empty($group)) {
			$title = "Ungrouped";
			$count = count($rooms);
		} else {
			$title = html_escape($group->name);
			$count = $group->room_count;
		}
		echo sprintf('<strong>%s</strong> (%d)', $title, $count);
		?>
	</summary>

	<div class="collapse-body">
		<?php
		if ( ! empty($group)) {
			echo iconbar([
				array('rooms/add?group=' . $group->room_group_id, 'Add room here', 'add.png'),
			]);
		}

		$this->load->view('rooms/index_groups/rooms', [
			'rooms' => $rooms ?? [],
			'group' => $group,
		]);
		?>
	</div>

</details>
