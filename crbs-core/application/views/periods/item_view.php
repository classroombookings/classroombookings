<?php
$time_start = strtotime(sprintf('%s %s', date('Y-m-d'), $period->time_start));
$time_end = strtotime(sprintf('%s %s', date('Y-m-d'), $period->time_end));

$sizes = [
	'bookable' => 10,
	'name' => 20,
	'time' => 20,
	// 'duration' => 10,
	'days' => 5 * count($days),
	'actions' => 15,
];

$total = array_sum($sizes);
// echo $total;

?>

<div class="box box-period box-period-view" id="period_<?= $period->period_id ?>" style="margin-bottom:4px;padding:0;">

	<div class="block-group is-middle">

		<div class="block" style="width: <?= $sizes['bookable'] ?>%">
			<?php
			$bookable_img = ($period->bookable == 1) ? 'enabled.png' : 'no.png';
			echo img([
				'src' => base_url("assets/images/ui/{$bookable_img}"),
				'width' => 16,
				'height' => 16,
				'alt' => $bookable_img,
				'style' => 'vertical-align:middle'
			]);
			?>
		</div>

		<div class="block" style="width: <?= $sizes['name'] ?>%">
			<?= html_escape($period->name) ?>
		</div>

		<div class="block" style="width: <?= $sizes['time'] ?>%">
			<?php
			$start_fmt = date('H:i', $time_start);
			$end_fmt = date('H:i', $time_end);
			echo sprintf('%s - %s', $start_fmt, $end_fmt);
			?>
		</div>

		<?php
		/*<div class="block" style="width: <?= $sizes['duration'] ?>%">
			<?php
			echo timespan($time_start, $time_end);
			?>
		</div>
		*/
		?>

		<?php
		$day_size = round($sizes['days'] / count($days));
		foreach ($days as $day_num => $day_name) {
			$prop = sprintf('day_%d', $day_num);
			$value = $period->{$prop};
			if ($value == 1) {
				$day_value = "{$day_name}";
			} else {
				$day_value = "<s style='color:#ccc'>{$day_name}</s>";
			}
			echo "<div class='block' style='width:{$day_size}%'>{$day_value}</div>";
		}
		?>

		<div class="block" style="width: <?= $sizes['actions'] ?>%; text-align:right">
			<?php
			$uri = site_url(sprintf('periods/edit/%d/%d', $schedule->schedule_id, $period->period_id));
			$img = img([
				'src' => base_url('assets/images/ui/edit.png'),
				'hspace' => 6,
				'border' => 0,
				'alt' => 'Edit',
			]);
			echo anchor($uri, $img, [
				'title' => 'Edit',
				'hx-get' => $uri,
				'hx-target' => 'closest .box-period',
				'hx-swap' => 'outerHTML',
			]);

			?>

		</div>

	</div>

</div>
