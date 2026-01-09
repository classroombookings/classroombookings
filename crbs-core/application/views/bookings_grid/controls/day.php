<?php

$date_url = site_url('bookings/filter/date') . '?' . http_build_query($query_params);
$long_date = date_output_long($datetime);
$date_img = img(asset_url('assets/images/ui/cal_day.png'));
$date_text = $long_date;
$date_label = "<span>{$date_img} {$date_text}</span> <span>&#x25BC;</span>";
$date_button = "<button
	type='button'
	class='filter-button'
	up-layer='new popup'
	up-size='medium'
	up-href='$date_url'
	up-history='false'
	up-target='.bookings-filter'
	up-preload=''
>{$date_label}</button>";

echo $date_button;
