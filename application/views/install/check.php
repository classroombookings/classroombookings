<?php
echo isset($notice) ? $notice : '';

echo "<div class='req-error'>";
echo msgbox('exclamation', "Please address the errors below and refresh the page before continuing.");
echo "</div>";

echo form_open(current_url(), array('class' => 'cssform', 'id' => 'install_step3'));

echo form_hidden('install', '1');

$items = array(
	'php_version' => 'PHP Version 5.5.0 or greater',
	'php_module_gd' => "PHP module 'GD' is available",
	'php_module_ldap' => "PHP module 'LDAP' is available",
	'database' => 'Database connection',
	'database_empty' => 'Database is empty',
	'folder_local' => "'local' directory exists and wirtable",
	'folder_uploads' => "'uploads' directory exists and wirtable",
);


$errors = 0;
?>


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

				if ($requirements[$name]['status'] == 'warn') {
					$status = "<span class='line-status status-warn'>Warning</span>";
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
		'submit' => array('Install', tab_index()),
		'cancel' => array('Back', tab_index(), 'install/info'),
	));
	echo "<style>.req-error{ display: none }</style>";
} else {
	$this->load->view('partials/submit', array(
		// 'submit' => array('Install', tab_index()),
		'cancel' => array('Go back', tab_index(), 'install/info'),
	));
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
.line-status.status-warn {
	background: #DDA458;
	color: #fff;
}
.line-status.status-err {
	background: #85144b;
	color: #fff;
}
</style>
