<?php
echo isset($notice) ? $notice : '';

echo "<div class='req-error'>";
echo msgbox('exclamation', "Please address the errors below and refresh the page before continuing.");
echo "</div>";

echo form_open(current_url(), array('class' => 'cssform', 'id' => 'upgrade_check'));

echo form_hidden('upgrade', '1');

$items = array(
	'php_version' => 'PHP Version 5.5.0 or greater',
	'php_module_gd' => "PHP module 'GD' is available",
	'database' => 'Database connection',
	'database_has_tables' => 'Database has classroombookings tables',
	'folder_local' => "'local' directory exists and wirtable",
	'folder_uploads' => "'uploads' directory exists and wirtable",
);

$errors = 0;
?>

<div>Please take a backup of your classroombookings database before continuing.</div>
<br><br>


<fieldset>

	<legend accesskey="C" tabindex="<?php echo tab_index() ?>">Configuration</legend>

	<p>
		<label for="url" class="required">URL</label>
		<?php
		$default = config_item('base_url');
		$field = 'url';
		$value = set_value($field, isset($_SESSION[$field]) ? $_SESSION[$field] : $default, FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<br>
		<br>
		<p class="hint">This is the web address that classroombookings will be accessed at. It must end with a forward slash /.</p>
	</p>
	<?php echo form_error($field); ?>

</fieldset>

<table cellpadding="2" cellspacing="2" width="100%" class="req-table">

	<thead>
		<tr class="heading">
			<td class="h">Requirement</td>
			<td class="h">Status</td>
		</tr>
	</thead>

	<tbody>

		<?php
		foreach ($items as $name => $label) {

			$status = '-';
			$message = '';


			if (array_key_exists($name, $requirements) && is_array($requirements[$name])) {

				if ($requirements[$name]['status'] == 'ok') {
					$status = "<span class='line-status status-ok'>OK</span>";
				}

				if ($requirements[$name]['status'] == 'err') {
					$errors++;
					$status = "<span class='line-status status-err'>Error</span>";
				}

				if (array_key_exists('message', $requirements[$name])) {
					$message = $requirements[$name]['message'];
				}

			}

			echo "<tr>";
			echo "<td class='req-table-label'>";
			echo "<div class='req-table-label-title'>{$label}</div>";
			echo "<div class='req-table-label-message'>{$message}</div>";
			echo "</td>";
			echo "<td class='req-table-status'>{$status}</td>";
			echo "</tr>";
		}
		?>

	</tbody>

</table>


<?php

if ($errors === 0) {
	$this->load->view('partials/submit', array(
		'submit' => array('Upgrade', tab_index()),
	));
	echo "<style>.req-error{ display: none }</style>";
} else {
	echo "<style>.req-error{ display: block }</style>";
}

echo form_close();

?>

<style>
.req-table tr td {
	border-bottom: 1px solid #ddd;
	padding: 10px;
}
.req-table .req-table-label-title {
	font-weight: normal;
	margin: 0 0 5px 0;
	font-size: 115%;
}
.req-table .req-table-label-message {
	font-weight: normal;
	font-size: 90%;
	color: #666;
}

.line-status {
	font-weight: bold;
	background: #ccc;
	padding: 4px;
}

.line-status.status-ok {
	background: #3D9970;
	color: #fff;
}
.line-status.status-err {
	background: #85144b;
	color: #fff;
}
</style>
