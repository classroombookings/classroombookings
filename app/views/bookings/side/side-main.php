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



<script type="text/javascript">
// Room/Date tabs
$(function() {
	$("#tabs").tabs({cookie:{expires:7,name:'tab.bookings'}});
});


// Room information box
$(document).ready(function($){
	$('a[rel*=facebox]').boxy({title:'Room Information'});
});


/*
// Room info ajax box
jQuery(document).ready(function($){
	$('a[rel*=facebox]').facebox();
}); */

// Clicking on the room LI element
$('li[class*=room]').bind("click", function(e){
	roomajax($(e.currentTarget).find('a[rel*=room]').attr("href"), $(e.currentTarget));
});
// Clicking on the link
$('a[rel*=room]').bind("click", function(e){
	roomajax($(e.currentTarget).attr("href"), $(e.currentTarget).parent());
	return false;
});

// Load room timetable via AJAX and set classes on LIs
function roomajax(url, li){
	//$('#tt').html(ajax_load).load(url);
	//$('#tt').load(url);
	crbsajax(url, 'tt');
	$('ul.bookings-roomlist li').removeClass("current");
	li.addClass("current");
}

// Set up calendar month navigation links
function calnavlinks(){
	$('a[rel*=calmonth]').bind("click", function(e){
		crbsajax($(e.currentTarget).attr("href"), 'cal', calnavlinks);
		return false;
	});
}
calnavlinks();
</script>