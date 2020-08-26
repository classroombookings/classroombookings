<?php

$roomlist = array();

foreach ($rooms as $room){
	$roomlist[$room->room_id] = html_escape($room->name);
}

?>

<?= form_open('bookings/load', [], ['chosen_date' => $chosen_date]) ?>

<table>
	<tr>
		<td valign="middle">
			<label for="room_id">
				<?php
				$url = site_url("rooms/info/{$room_id}");
				$name = 'Room:';
				$link = "<a href='{$url}' up-position='left' up-drawer='.room-info' up-history='false' up-preload>{$name}</a>";
				echo "<strong>{$link}</strong>";
				?>
			</label>
		</td>
		<td valign="middle">
			<?php
			echo form_dropdown(
				'room_id',
				$roomlist,
				$room_id,
				'onchange="this.form.submit()" onmouseup="this.form.submit"'
			);
		?>
		</td>
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>

<?= form_close() ?>

<br />
