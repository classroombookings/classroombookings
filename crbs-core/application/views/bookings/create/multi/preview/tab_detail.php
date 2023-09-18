<?php

use app\components\Calendar;

// Day name for the slot
$day_name = Calendar::get_day_name($slot->datetime->format('N'));

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table class="zebra-table" style="line-height:1.3;margin-top:16px;margin-bottom:16px" width="100%" cellpadding="10" cellspacing="0" border="0">',
]);

$this->table->set_heading([
	['data' => 'Date', 'width' => '33%'],
	['data' => 'Action', 'width' => '33%'],
	['data' => 'Existing booking', 'width' => '33%'],
]);


// Table rows for every slot.
//
foreach ($slot->instances as $key => $instance) {

	$date = $instance['datetime']->format($date_format);
	$date = trim(str_replace($day_name, '', $date));

	$existing_html = '';

	if (isset($instance['booking'])) {

		$user_label = !empty($instance['booking']->user->displayname)
				? $instance['booking']->user->displayname
				: $instance['booking']->user->username;

		$notes_html = '';
		if (!empty($instance['booking']->notes)) {
			$notes = html_escape($instance['booking']->notes);
			if (strlen($notes) > 15) {
				$content = html_escape("<p>{$notes}</p><button type='button' up-dismiss>OK</button>");
				$notes_html = "<a href='#'
					up-layer='new popup'
					up-content='{$content}'
					up-align='top'
					up-position='left'
					up-size='medium'
				>" . character_limiter($notes, 15) . "</a>";
			} else {
				$notes_html = $notes;
			}
			$notes_html = "<div>{$notes_html}</div>";
		}

		$existing_html = $user_label . $notes_html;
	}

	$date_ymd = $instance['datetime']->format('Y-m-d');
	$field_name = sprintf('dates[%d][%s][action]', $slot->mbs_id, $date_ymd);

	// Default value
	$hidden_html = form_hidden($field_name, 'do_not_book');

	if (isset($instance['booking'])) {

		$hidden = form_hidden(sprintf('dates[%d][%s][replace_booking_id]', $slot->mbs_id, $date_ymd), $instance['booking']->booking_id);

		$options_value = set_value($field_name);

		$actions_list = form_dropdown([
			'name' => $field_name,
			'options' => $instance['actions'],
			'selected' => $options_value,
		]);

		$actions_html = $hidden . $actions_list;

	} else {

		$field_id = sprintf('slot_%d_date_%s', $slot->mbs_id, $date_ymd);
		$field_value = set_value($field_name, 'book', FALSE);

		$input = form_checkbox([
			'name' => $field_name,
			'id' => $field_id,
			'value' => 'book',
			'style' => 'vertical-align:middle',
			'checked' => ($field_value == 'book'),
		]);

		$input_label = form_label('Create booking', $field_id, ['class' => 'ni', 'style' => 'display:inline-block']);

		$actions_html = $input . $input_label;
	}

	$this->table->add_row($date, $hidden_html . $actions_html, $existing_html);
}

echo $this->table->generate();
