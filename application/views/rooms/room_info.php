<div class='room-info' data-style="min-width: 320px">

	<h3><?= html_escape($room->name) ?></h3>

	<?php
	$photo_html = '';
	$fields_html = '';

	$this->table->set_template([
		'table_open' => '<table class="zebra-table" width="100%" cellpadding="4" cellspacing="0" border="0">',
	]);

	foreach ($room_info as $row) {
		$this->table->add_row($row['label'], $row['value']);
	}

	$fields_html = $this->table->generate();

	if ($photo_url) {
		$img = img($photo_url);
		$photo_html = "<div class='room-photo'>{$img}</div>";
	}

	echo $fields_html;
	echo $photo_html;

	?>

</div>
