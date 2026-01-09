<?php

use app\components\Calendar;

$day_name = Calendar::get_day_name($slot->datetime->format('N'));
$lang_key = sprintf('cal_%s', strtolower((string) $day_name));
$day_name_lang = lang($lang_key);

$period = $slot->period->name;

if ($slot->conflict_count === 0) {
	$icon_name = 'accept.png';
	$title = lang('booking.no_conflicts');
} else {
	$icon_name = 'error.png';
	$title = sprintf('%d %s',
		$slot->conflict_count,
		($slot->conflict_count === 1
			? lang('booking.conflict')
			: lang('booking.conflicts')
		)
	);
}

$img = img([
	'src' => asset_url('assets/images/ui/' . $icon_name),
	'alt' => $title,
	'title' => $title,
	'style' => 'display:inline-block;margin-top:4px;',
]);


echo "<div style='float:right;text-align:right'><div>{$period}</div>{$img}</div>";

echo "<div><strong>{$day_name_lang}</strong></div>";

$room = $slot->room->name;
echo "<div style='margin-top:4px;'>{$room}</div>";
