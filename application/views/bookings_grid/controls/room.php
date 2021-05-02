
<?php
echo form_open($form_action, ['method' => 'get', 'id' => 'bookings_controls_room'], $query_params);
?>

<table>
	<tr>
		<td valign="middle">
			<label>
				<?php
				$url = site_url("rooms/info/{$room->room_id}");
				$name = 'Room:';
				$link = "<a href='{$url}' up-position='left' up-drawer='.room-info' up-history='false' up-preload>{$name}</a>";
				echo "<strong>{$link}</strong>";
				?>
			</label>
		</td>
		<td valign="middle">
			<?php
			echo form_dropdown([
				'name' => 'room',
				'id' => 'room_id',
				'options' => $rooms,
				'selected' => $room->room_id,
			]);
			?>
		</td>
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>

<?= form_close() ?>

<br />
