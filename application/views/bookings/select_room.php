<?php
$roomlist = array();
$roomphoto = array();
foreach($rooms as $room){
	$roomlist[$room->room_id] = html_escape($room->name);
	if($room->photo != NULL){
		$roomphoto[$room->room_id] = TRUE;
	} else {
		#$roomphoto[$room->room_id] = FALSE;
	}
}
?>
<form action="<?php echo site_url('bookings/load') ?>" method="POST">
<?php echo form_hidden('chosen_date', $chosen_date) ?>
<table>
	<tr>
		<td valign="middle">
			<label for="room_id">
				<?php
				$url = site_url('rooms/info/'.$room_id);
				if(isset($roomphoto[$room_id])){
					$width = 760;
				} else {
					$width = 400;
				}
				?>
				<strong>
					<a onclick="window.open('<?php echo $url ?>','','width=<?php echo $width ?>,height=360,scrollbars');return false;" href="<?php echo $url ?>" title="View Room Information">Room</a>:
				</strong>
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
</form>

<br />
