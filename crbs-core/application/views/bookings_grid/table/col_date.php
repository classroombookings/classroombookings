<?php
$classes = [
	'bookings-grid-header-cell',
	'bookings-grid-header-cell-day',
];

if ($date->date == $today->format('Y-m-d')) {
	$classes[] = 'bookings-grid-header-cell-is-today';
}

?>

<th class="<?= implode(' ', $classes) ?>" width="<?= $width ?>">
	<?php
	$val = date_output_weekday($date->date);
	$out = highlight_weekday($val, '<strong>', '</strong><br>');
	echo $out;
	?>
</th>
