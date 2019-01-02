
<!DOCTYPE html>
<html>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Craig A Rodway">
	<title><?= html_escape($title) ?> | Classroombookings</title>
	<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('assets/style.css') ?>">
	<link rel="stylesheet" type="text/css" media="print" href="<?= base_url('assets/print.css') ?>">
	<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('assets/sorttable.css') ?>">
	<style tpe="text/css">
	#content{
		margin:16px 8px 8px 8px;
		border:1px solid #c0c0c0;
		padding:8px;
		background:#fff;
		color:#000;
	}
	</style>
	<script>
	var h = document.getElementsByTagName("html")[0];
	(h ? h.classList.add('js') : h.className += ' ' + 'js');
	var Q = [];
	var BASE_URL = "<?= base_url() ?>";
	</script>
</head>
<body style="background-image:url('<?= base_url('assets/images/bg/global.png') ?>')">

	<div id="content">
		<h3 style="margin:0;"><?php echo html_escape($title) ?></h3>
		<br />
		<?php echo $body ?>
	</div>

	<?php
	$scripts = array();
	$scripts[] = base_url('assets/js/prototype.lite.js');
	$scripts[] = base_url('assets/js/util.js');
	$scripts[] = base_url('assets/js/sorttable.js');

	foreach ($scripts as $script)
	{
		echo "<script type='text/javascript' src='{$script}'></script>\n";
	}

	?>

	<script>
	(function() {
		if (typeof(window['Q']) !== "undefined") {
			for (var i = 0, len = Q.length; i < len; i++) {
				Q[i]();
			}
		}
	})();
	</script>

</body>
</html>
