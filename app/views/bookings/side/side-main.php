<div id="tabs" class="bookings-sidetabs">
	
	
	<ul>
		<li><a href="#rooms">Rooms</a></li>
		<li><a href="#date">Date</a></li>
	</ul>
	
	
	<div id="rooms" class="bookings-roomlist">
		<?php
		foreach ($rooms as $cat_id => $cat){
			if($cat_id > -1 ){
				echo '<h4>' . $cats[$cat_id] . '</h4>';
			}
			echo '<ul class="bookings-roomlist">';
			foreach($cat as $room){
				$roominfo = site_url('rooms/info/' . $room->room_id);
				$roomurl = site_url('bookings/room/' . $room->room_id);
				
				// $room_id is the previously chosen room, comes via the controller
				$current = ($room->room_id == $room_id) ? 'current' : '';
				
				echo '<li class="room ' .$current . '">';
				echo '<a href="' . $roomurl . '" rel="room">' . $room->name . '</a>';
				echo '<a href="' . $roominfo . '" class="ui-icon ui-icon-info" rel="facebox"></a>';
				echo '<span>' . $room->owner_name . '</span>';
				echo '</li>';
			}
			echo '</ul>';
		}
		?>
	</div>

	
	<div id="date" class="bookings-cal">
		<div id="cal"><?php echo $cal ?></div>
		<div class="weeks-legend"><strong>Legend: </strong><?php
			foreach($weeks as $week){
				echo sprintf('<span class="week_%d">%s</span>', $week->week_id, $week->name);
			}
		?></div>
	</div>
	
	
</div>