<?php

$form_uri = (is_null($period))
	? site_url(sprintf('periods/save/%d', $schedule->schedule_id))
	: site_url(sprintf('periods/save/%d/%d', $schedule->schedule_id, $period->period_id))
	;

$attrs = [
	'hx-post' => $form_uri,
	'hx-target' => 'closest .box',
	'hx-swap' => 'outerHTML',
];

$hidden = [
	'schedule_id' => $schedule->schedule_id,
];

$time_start = false;
$time_end = false;

if ( ! is_null($period)) {
	$time_start = strtotime(sprintf('%s %s', date('Y-m-d'), $period->time_start));
	$time_end = strtotime(sprintf('%s %s', date('Y-m-d'), $period->time_end));
}

$sizes = [
	'bookable' => 10,
	'name' => 16,
	'time_start' => 12,
	'time_end' => 12,
	'days' => 5 * count($days),
	'actions' => 15,
];

$total = array_sum($sizes);
// echo $total;

?>

<div class="box box-period box-period-add-edit" style="margin-bottom:4px;padding:0;border:1px solid #F5F4EA; background: transparent;">

	<?= form_open($form_uri, $attrs, $hidden) ?>

	<div class="inline-form">

		<div class="block-group is-middle">

			<div class="block" style="width: <?= $sizes['bookable'] ?>%">
				<p class="input-group">
					<?php
					$field = 'bookable';
					$id = sprintf("period_%s_%s", is_null($period) ? 'new' : $period->period_id, $field);
					$value = set_value($field, is_null($period) ? '1' : $period->bookable, FALSE);
					$hidden = form_hidden($field, '0');
					$input = form_checkbox([
						'id' => $id,
						'name' => $field,
						'value' => '1',
						'checked' => ($value == '1'),
					]);
					echo form_label(lang('period.field.bookable'), $id);
					echo $hidden;
					echo $input;
					?>
				</p>
			</div>

			<div class="block" style="width: <?= $sizes['name'] ?>%">
				<p class="input-group">
					<?php
					$field = 'name';
					$id = sprintf("period_%s_%s", is_null($period) ? 'new' : $period->period_id, $field);
					$value = set_value($field, is_null($period) ? '' : $period->name, FALSE);
					$input = [
						'name' => $field,
						'id' => $id,
						'value' => $value,
						'style' => 'width:100%',
					];
					if ( ! isset($focus)) {
						$input['autofocus'] = '';
					}
					echo form_label(lang('period.field.name'), $id);
					echo form_input($input);
					echo form_error($field);
				?>
				</p>
			</div>

			<div class="block" style="width: <?= $sizes['time_start'] ?>%">
				<p class="input-group">
					<?php
					$field = 'time_start';
					$id = sprintf("period_%s_%s", is_null($period) ? 'new' : $period->period_id, $field);
					$value = set_value($field, is_null($period) ? '' : date('H:i', $time_start), FALSE);
					$input = [
						'type' => 'time',
						'name' => $field,
						'id' => $id,
						'value' => $value,
						'style' => 'width:100%',
					];
					echo form_label(lang('period.field.start'), $id);
					echo form_input($input);
					echo form_error($field);
					?>
				</p>
			</div>

			<div class="block" style="width: <?= $sizes['time_end'] ?>%">
				<p class="input-group">
					<?php
					$field = 'time_end';
					$id = sprintf("period_%s_%s", is_null($period) ? 'new' : $period->period_id, $field);
					$value = set_value($field, is_null($period) ? '' : date('H:i', $time_end), FALSE);
					$input = [
						'type' => 'time',
						'name' => $field,
						'id' => $id,
						'value' => $value,
						'style' => 'width:100%',
					];
					echo form_label(lang('period.field.end'), $id);
					echo form_input($input);
					echo form_error($field);
					?>
				</p>
			</div>

			<?php
			$day_size = round($sizes['days'] / count($days));
			foreach ($days as $day_num => $day_name) {
				$prop = $field = sprintf('day_%d', $day_num);
				$value = set_value($field, is_null($period) ? '' : $period->{$prop}, FALSE);
				$id = sprintf("period_%s_%s", is_null($period) ? 'new' : $period->period_id, $field);
				$hidden = form_hidden($field, '0');
				$input = [
					'id' => $id,
					'name' => $field,
					'value' => '1',
					'checked' => ($value == '1'),
				];
				$lang_key = sprintf('cal_%s', strtolower((string) $day_name));
				$label = form_label(lang($lang_key), $id);
				$input = form_checkbox($input);
				echo "<div class='block' style='width:{$day_size}%'><p class='input-group'>{$label}{$hidden}{$input}</p></div>";
			}
			?>

			<div class="block" style="width: <?= $sizes['actions'] ?>%; text-align:right">

				<p class='input-group'>

					<label>&nbsp;</label>

					<?php
					$img = img([
						'src' => asset_url('assets/images/ui/disk.png'),
						'hspace' => 6,
						'border' => 0,
						'alt' => lang('app.action.save'),
					]);
					echo form_button([
						'type' => 'submit',
						'class' => 'btn-icon',
						'name' => 'save',
						'content' => $img,
						'title' => lang('app.action.save'),
					]);


					if (!is_null($period)) {

						$uri = site_url(sprintf('periods/delete/%d/%d', $schedule->schedule_id, $period->period_id));
						$img = img([
							'src' => asset_url('assets/images/ui/delete.png'),
							'hspace' => 6,
							'border' => 0,
							'alt' => lang('app.action.edit'),
						]);
						echo anchor($uri, $img, [
							'title' => lang('app.action.delete'),
							'hx-post' => $uri,
							'hx-confirm' => lang('period.delete.warning'),
						]);

						$uri = site_url(sprintf('periods/view/%d/%d', $schedule->schedule_id, $period->period_id));
						$img = img([
							'src' => asset_url('assets/images/ui/arrow_undo.png'),
							'hspace' => 6,
							'border' => 0,
							'alt' => lang('app.action.cancel'),
						]);
						echo anchor($uri, $img, [
							'title' => lang('app.action.revert'),
							'hx-get' => $uri,
						]);

					}

					?>

				</p>

			</div>

		</div>

	</div>

	<?= form_close() ?>

</div>
