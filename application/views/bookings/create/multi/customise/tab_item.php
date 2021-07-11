<?php

use app\components\Calendar;

$day_name = Calendar::get_day_name($slot->datetime->format('N'));

$period = $slot->period->name;
echo "<div style='float:right'>{$period}</div>";

echo "<div><strong>{$day_name}</strong></div>";

$room = $slot->room->name;
echo "<div style='margin-top:4px;'>{$room}</div>";
