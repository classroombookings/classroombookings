<div id="tabs" class="bookings-sidetabs hidden">
	
	
	<ul>
		<li><a href="#rooms">Rooms</a></li>
		<li><a href="#date">Date</a></li>
	</ul>
	
	
	<div id="rooms" class="bookings-roomlist">
		<table class="list" id="sb-roomlist">
		<?php
		foreach ($rooms as $cat_id => $cat){
			if($cat_id > -1 ){
				#echo '<h4>' . $cats[$cat_id] . '</h4>';
				echo '<tr class="heading">';
				echo '<td colspan="2" class="h">' . $cats[$cat_id] . '</td>';
				echo '</tr>';
			}
			#echo '<ul class="bookings-roomlist">';
			foreach($cat as $room){
				$roominfo = site_url('rooms/info/' . $room->room_id);
				$roomurl = site_url('bookings/room/' . $room->room_id);
				
				// $room_id is the previously chosen room, comes via the controller
				$current = ($room->room_id == $room_id) ? 'current' : '';
				
				#echo '<li class="room ' .$current . '">';
				#echo '<a href="' . $roomurl . '" rel="room">' . $room->name . '</a>';
				#echo '<a href="' . $roominfo . '" class="ui-icon ui-icon-info" rel="facebox"></a>';
				#echo '<span>' . $room->owner_name . '</span>';
				#echo '</li>';
				
				echo '<tr rel="room" class="room-' . $room->room_id . '">';
				
				echo '<td class="t" rel="room-' . $room->room_id . '">';
				echo anchor('bookings/room/' . $room->room_id, $room->name, 
					'rel="room" id="room-' . $room->room_id . '"');
				#echo '<a href="' . $roomurl . '" rel="room">' . $room->name . '</a>';
				echo '<span>' . $room->owner_name . '</span>';
				echo '</td>';
				echo '<td class="il">';
				unset($actiondata);
				$actiondata[] = array('rooms/info/' . $room->room_id, ' ', 'f_info.gif', 'Room information', FALSE, 'rel="boxy"');
				$this->load->view('parts/linkbar', $actiondata);
				echo '</td>';
				
				echo '</tr>';
			}
			#echo '</ul>';
			
		}
		?>
		</table>
	</div>

	
	<div id="date" class="bookings-cal">
		<div id="cal"><?php echo $cal ?></div>
		<div class="weeks-legend"><strong>Legend: </strong><?php
			if(is_array($weeks)){
				foreach($weeks as $week){
					echo sprintf('<span class="week_%d">%s</span>', $week->week_id, $week->name);
				}
			}
		?></div>
	</div>
	
	
</div>