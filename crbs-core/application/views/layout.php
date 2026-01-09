<?php

//

$global_menu = $this->menu_model->global();
$footer_menu = $global_menu;
if (has_setup_permission()) {
	$footer_menu[] = [
		'label' => lang('app.whats_new') . ' ' . $this->changelog->get_indicator_markup(),
		'url' => site_url('changelog'),
		'icon' => 'cake.png',
		'ext' => true,
	];
}

?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Craig A Rodway">
<meta name="robots" content="noindex,nofollow">
<title><?= html_escape($title) ?> | classroombookings</title>
<?php
if (CRBS_MANAGED && setting('logo')) {
	echo "<link rel='preconnect' href='https://crbsimg.b-cdn.net'>\n";
}
$conf_suffix = '';
if (CRBS_MANAGED && config_item('asset_cdn_host')) {
	echo "<link rel='preconnect' href='https://cdn.classroombookings.net'>\n";
	$conf_suffix = '-cloud';
}
foreach ($css as $css_conf) {
	$url = asset_url($css_conf['path'], true);
	$fmt = "<link rel='stylesheet' type='text/css' media='%s' href='%s'>\n";
	echo sprintf($fmt, $css_conf['media'], $url);
}
foreach ($hs as $hs_config) {
	$src = asset_url($hs_config['path'], $hs_config['version']);
	$attrs = [];
	$attrs['type'] = 'text/hyperscript';
	if (isset($hs_config['defer']) && $hs_config['defer'] === true) {
		$attrs['defer'] = '';
	}
	echo script_src($src, $attrs);
}
foreach ($js as $js_conf) {
	$src = asset_url($js_conf['path'], $js_conf['version']);
	$attrs = [];
	if (isset($js_conf['defer']) && $js_conf['defer'] === true) {
		$attrs['defer'] = '';
	}
	echo script_src($src, $attrs);
}
?>
<link rel="apple-touch-icon" sizes="180x180" href="<?= asset_url('assets/brand/apple-touch-icon.png', true) ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= asset_url('assets/brand/favicon-32x32.png', true) ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= asset_url('assets/brand/favicon-16x16.png', true) ?>">
<link rel="manifest" href="<?= asset_url(sprintf('assets/brand/site%s.webmanifest', $conf_suffix), true) ?>">
<link rel="mask-icon" href="<?= asset_url('assets/brand/safari-pinned-tab.svg', true) ?>" color="#ff6400">
<link rel="shortcut icon" href="<?= asset_url('assets/brand/favicon.ico', true) ?>">
<meta name="msapplication-TileColor" content="#ff6400">
<meta name="msapplication-config" content="<?= asset_url(sprintf('assets/brand/browserconfig%s.xml', $conf_suffix), true) ?>">
<meta name="theme-color" content="#ff6400">
<script type="text/javascript">
function ready(fn) {
	if (document.readyState !== "loading") {
		setTimeout(fn);
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}

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
			$message = lang('app.maintenance_message');
		}
		echo "<div class='maintenance-wrapper'>";
		echo "<div class='outer'>";
		echo html_escape($message);
		echo "</div>";
		echo "</div>";
	}
	?>
	<header class="header">
		<div class="outer">

			<div class="block-group">

				<div class="block b-40 header-title">
					<div class="title">
						<?php
						$logo = img([
							'src' => asset_url('assets/images/crbs-logo-square.svg'),
							'width' => 24,
							'height' => 24,
							'alt' => 'classroombookings logo',
						]);
						$name = '';
						$output = '';
						$attrs = '';
						if (config_item('is_installed')) {
							$name = setting('name');
						}
						if (!empty($name)) {
							$name = html_escape($name);
						} else {
							$attrs = "title='classroombookings' style='font-weight:normal;color:#0081C2;letter-spacing:-1px'";
							$name = "classroom";
							$name .= "<span style='color:#ff6400;font-weight:bold'>bookings</span>";
						}
						echo anchor('/', $logo . $name, $attrs);
						?>
					</div>
				</div>

				<div class="block b-60 header-meta">
					<?php
					if ( ! empty($global_menu)) {
						echo "<p class='iconbar'>";
						foreach ($global_menu as $idx => $item) {
							$icon = img(asset_url('assets/images/ui/' . $item['icon']), FALSE, "align='top' alt='{$item['label']}'");
							$label = $icon . html_escape($item['label']);
							echo anchor($item['url'], $label);
						}
						echo "</p>";
					}
					?>
				</div>

			</div>
		</div>
	</header>

	<div class="outer" up-main>

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
									$icon_url = asset_url('assets/images/ui/' . $item['icon']);
									$alt = html_escape(strip_tags((string) $item['label']));
									$icon = img($icon_url, FALSE, "align='top' alt='{$alt}'");
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
							<span>
								<a href="https://www.classroombookings.com/" target="_blank">classroombookings</a>
								<?= strtolower(lang('app.version')) ?>
								<?= VERSION ?>.
							<br>
							&copy; <?= date('Y') ?> Craig A Rodway.</span>
							<br />
							<?php
							$fmt = "%s: %s %s";
							echo sprintf($fmt, lang('app.load_time'), $this->benchmark->elapsed_time(), lang('app.seconds'));
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>
