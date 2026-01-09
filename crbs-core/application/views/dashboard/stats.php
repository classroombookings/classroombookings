<?php

$blocks = [];

$blocks[] = [
	'title' => 'All bookings',
	'figure' => $totals['all'],
];

$blocks[] = [
	'title' => 'Bookings this session',
	'figure' => $totals['session'],
];

$blocks[] = [
	'title' => 'Active bookings',
	'figure' => $totals['active'],
];

if ( ! is_null($constraints['max_active_bookings'])) {
	$blocks[] = [
		'title' => 'Maximum active bookings',
		'figure' => $constraints['max_active_bookings'],
	];
	$blocks[] = [
		'title' => 'Bookings you can create',
		'figure' => ($constraints['max_active_bookings'] - $totals['active']),
	];
}

echo "<div style='margin-bottom:48px'>";

foreach ($blocks as $block) {

	$figure = number_format($block['figure']);
	$figure_html = "<dt>{$figure}</dt>";

	$title = html_escape($block['title']);
	$title_html = "<dd>{$title}</dd>";

	$block_content = "<div class='stat-item'><dl>{$title_html}{$figure_html}</dl></div>";

	echo $block_content;

}

echo "</div>";
