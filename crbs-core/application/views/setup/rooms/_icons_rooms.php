<?php

if ( ! isset($active)) $active = NULL;

$items = [];

if (isset($room_group)) {
	$items[] = ['setup/rooms/groups/view/' . $room_group->room_group_id, $room_group->name, 'arrow_turn_left.png'];
}

if (isset($room)) {
	$items[] = ['setup/rooms/rooms/edit/' . $room->room_id, lang('app.action.update'), 'edit.png'];
	$items[] = ['setup/rooms/acl/room/' . $room->room_id, lang('acl.access_control'), 'lock.png'];
}

echo iconbar($items, $active);
