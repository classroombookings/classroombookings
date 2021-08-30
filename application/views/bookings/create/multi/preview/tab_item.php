<?php

use app\components\Calendar;

$day_name = Calendar::get_day_name($slot->datetime->format('N'));

$period = $slot->period->name;

if ($slot->conflict_count === 0) {
	$icon_name = 'accept.png';
	$title = 'No conflicts';
} else {
	$icon_name = 'error.png';
	$title = sprintf('%d %s', $slot->conflict_count, ($slot->conflict_count === 1 ? 'conflict' : 'conflicts'));
}

$img = img([
	'src' => base_url('assets/images/ui/' . $icon_name),
	'alt' => $title,
	'title' => $title,
	'style' => 'display:inline-block;margin-top:4px;',
]);


echo "<div style='float:right;text-align:right'><div>{$period}</div>{$img}</div>";

echo "<div><strong>{$day_name}</strong></div>";

$room = $slot->room->name;
echo "<div style='margin-top:4px;'>{$room}</div>";
