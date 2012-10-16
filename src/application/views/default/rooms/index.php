<?php
if($rooms != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" colspan="2" title="O_DN">Order</td>
		<td class="h" title="Bookable">Bookable</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="Description">Description</td>
		<td class="h" title="Owner">Room owner</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	
	<tr><td colspan="7">&nbsp;</td></tr>
	<?php
	$i = 0;
	
	foreach ($rooms as $cat_id => $cat){
		
		echo '<tr class="rc"><td colspan="7"><strong>' . $cats[$cat_id] . '</strong></td></tr>';
		
		foreach($cat as $room){
			
			$bookable_img = ($room->bookable == 1) ? 'f_yes.gif' : 'f_yes-grey.gif';
			$bookable_title = ($room->bookable == 1) ? 'This room can be booked' : 'This room cannot be booked.';
			?>
			<tr>
				<td class="x" width="16"><img src="img/ico/arr-down-sm.gif" /></td>
				<td class="x" width="16"><img src="img/ico/arr-up-sm.gif" /></td>
				<td align="center" width="20"><img src="img/ico/<?php echo $bookable_img ?>" width="16" height="16" alt="Bookable" title="<?php echo $bookable_title ?>" /></td>
				<td class="x"><?php echo anchor('rooms/manage/edit/'.$room->room_id, $room->name) ?></td>
				<td class="x"><span title="<?php echo $room->description ?>"><?php echo word_limiter($room->description, 8) ?>&nbsp;</span></td>
				<td class="x"><?php echo $room->owner_name ?>&nbsp;</td>
				<td class="il">
				<?php
				unset($actiondata);
				#$actiondata[] = array('rooms/permissions/'.$room->room_id, 'Edit permissions', 'key-sm.gif' );
				$actiondata[] = array('rooms/manage/info/'.$room->room_id, ' ', 'roominfo-sm.gif');
				$actiondata[] = array('rooms/manage/delete/'.$room->room_id, ' ', 'cross_sm.gif' );
				$this->load->view('parts/linkbar', $actiondata);
				?></td>
			</tr>
			<?php
			$i++;
		}
	}
	?>
	</tbody>
</table>

<?php } else { ?>

<p>No rooms exist!</p>

<?php } ?>