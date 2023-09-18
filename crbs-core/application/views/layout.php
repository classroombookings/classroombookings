<?php

$enable_onset_link = $this->userauth->is_level(ADMINISTRATOR);
$enable_doorbell = $this->userauth->is_level(ADMINISTRATOR) && !is_demo_mode();

//

$global_menu = $this->menu_model->global();
$footer_menu = $global_menu;

if ($enable_onset_link) {
	$footer_menu[] = [
		'label' => 'Changelog',
		'url' => config_item('onset_public_url'),
		'icon' => 'bell.png',
		'ext' => true,
	];
}

if ($enable_doorbell) {
	$footer_menu[] = [
		'label' => 'Feedback',
		'url' => '#',
		'icon' => 'comment.png',
		'id' => 'feedback_link',
		'attrs' => 'style="opacity:0.3;pointer-events:none"',
	];
}

$css = [
	'screen' => (ENVIRONMENT === 'development' ? 'assets/css/main.css' : 'assets/css/main.min.css'),
	'print' => (ENVIRONMENT === 'development' ? 'assets/css/print.css' : 'assets/css/print.min.css'),
];

?>

<!DOCTYPE html>
<html>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Craig A Rodway">
	<title><?= html_escape($title) ?> | classroombookings</title>
	<?php
	if (CRBS_MANAGED && setting('logo')) {
		echo "<link rel='preconnect' href='https://crbsappimg.b-cdn.net'>";
	}
	?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url($css['screen']) ?>?v=<?= VERSION ?>">
	<link rel="stylesheet" type="text/css" media="print" href="<?= base_url($css['print']) ?>?v=<?= VERSION ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/brand/apple-touch-icon.png') ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/brand/favicon-32x32.png') ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/brand/favicon-16x16.png') ?>">
	<link rel="manifest" href="<?= base_url('assets/brand/site.webmanifest') ?>">
	<link rel="mask-icon" href="<?= base_url('assets/brand/safari-pinned-tab.svg') ?>" color="#ff6400">
	<link rel="shortcut icon" href="<?= base_url('assets/brand/favicon.ico') ?>">
	<meta name="msapplication-TileColor" content="#ff6400">
	<meta name="msapplication-config" content="<?= base_url('assets/brand/browserconfig.xml') ?>">
	<meta name="theme-color" content="#ff6400">
	<script>
	var h = document.getElementsByTagName("html")[0];
	(h ? h.classList.add('js') : h.className += ' ' + 'js');
	var BASE_URL = "<?= base_url() ?>";
	</script>
</head>
<body>

	<?php
	if (setting('maintenance_mode') == 1) {
		$message = setting('maintenance_mode_message');
		if (empty($message)) {
			$message = 'classroombookings is currently in maintenance mode. Please check again soon.';
		}
		echo "<div class='maintenance-wrapper'>";
		echo "<div class='outer'>";
		echo html_escape($message);
		echo "</div>";
		echo "</div>";
	}
	?>
	<div class="outer" up-main>

		<div class="header">

			<div class="block-group">

				<div class="block b-50 header-title">
					<div class="title">
						<?php
						$name = '';
						$output = '';
						$attrs = '';
						if (config_item('is_installed')) {
							$name = setting('name');
						}
						if (!empty($name)) {
							$name = html_escape($name);
						} else {
							$attrs = "title='classroombookings' style='font-weight:normal;color:#0081C2;letter-spacing:-2px'";
							$name = "classroom";
							$name .= "<span style='color:#ff6400;font-weight:bold'>bookings</span>";
						}
						echo anchor('/', $name, $attrs);
						?>
					</div>
				</div>

				<div class="block b-50 header-meta">
					<?php
					if ( ! empty($global_menu)) {
						echo "<p class='iconbar'>";
						foreach ($global_menu as $idx => $item) {
							$icon = img('assets/images/ui/' . $item['icon'], FALSE, "align='top' alt='{$item['label']}'");
							$label = $icon . $item['label'];
							echo anchor($item['url'], $label);
						}
						echo "</p>";
					}

					if ($this->userauth->logged_in()) {
						$display = $this->userauth->user->displayname;
						$label = (!empty($display))
							? $this->userauth->user->displayname
							: $this->userauth->user->username;
						echo sprintf('<p class="normal">Logged in as %s</p>', html_escape($label));
					}

					?>
				</div>

			</div>

		</div>

		<?php if (isset($midsection)): ?>
			<div class="mid-section" align="center">
				<h1 style="font-weight:normal"><?php echo $midsection ?></h1>
			</div>
		<?php endif; ?>

		<div class="content_area">
			<?php
			if (isset($showtitle) && !empty($showtitle)) {
				echo '<h2>'.html_escape($showtitle).'</h2>';
			}
			echo $body;
			?>
		</div>

		<div class="footer">
			<div id="footer">
				<br>
				<div class="block-group">
					<div class="block b-60">
						<div id="footer_links">
							<?php
							if ( ! empty($footer_menu)) {
								foreach ($footer_menu as $idx => $item) {
									$icon = img('assets/images/ui/' . $item['icon'], FALSE, "align='top' alt='{$item['label']}'");
									$attrs = '';
									if (isset($item['ext']) && $item['ext']) {
										$attrs .= " target='_blank' rel='noopener' ";
									}
									if (isset($item['id'])) {
										$attrs .= " id='{$item['id']}' ";
									}
									if (isset($item['attrs'])) {
										$attrs .= ' ' . $item['attrs'] . ' ';
									}
									$label = $icon . $item['label'];
									$link = anchor($item['url'], $label, $attrs);
									if ($item['url'] === '#') {
										$link = str_replace(site_url('#'), '#', $link);
									}
									echo "{$link}\n";
								}
							} else {
								echo "&nbsp;";
							}
							?>
						</div>
					</div>
					<div class="block b-40">
						<div style="font-size:90%;color:#678; line-height: 2; text-align:right">
							<span><a href="https://www.classroombookings.com/" target="_blank">classroombookings</a> version <?= VERSION ?>.
							<br>
							&copy; <?= date('Y') ?> Craig A Rodway.</span>
							<br />
							Load time: <?php echo $this->benchmark->elapsed_time() ?> seconds.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	foreach ($scripts as $script)
	{
		$ver = VERSION . (ENVIRONMENT === 'development' ? '-' . time() : '');
		$url = sprintf('%s?v=%s', base_url($script), $ver);
		echo "<script type='text/javascript' src='{$url}'></script>\n";
	}
	?>

	<?php
	if ($enable_doorbell) {
		$this->load->view('partials/layout/doorbell');
	}
	?>

</body>
</html>
