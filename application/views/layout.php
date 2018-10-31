<?php

if($this->loggedin){
	$menu[1]['text'] = img('assets/images/ui/link_controlpanel.gif', FALSE, 'hspace="4" align="top" alt=" "') . 'Control Panel';
	$menu[1]['href'] = site_url('controlpanel');
	$menu[1]['title'] = 'Tasks';

	#$menu[2]['text'] = '<img src="webroot/images/ui/link_help.gif" hspace="4" align="top" alt=" " />Help';
	#$menu[2]['href'] = site_url('help'.ereg_replace('help(/)', '', $this->uri->uri_string()));
	#$menu[2]['title'] = 'Get help on this page';

	if($this->userauth->CheckAuthLevel(ADMINISTRATOR)){ $icon = 'user_administrator.gif'; } else { $icon = 'user_teacher.gif'; }
	$menu[3]['text'] = img('assets/images/ui/logout.gif', FALSE, 'hspace="4" align="top" alt=" "') . 'Logout';
	$menu[3]['href'] = site_url('logout');
	$menu[3]['title'] = 'Close your current classroombookings session';
}
?>

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
	<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('assets/cpicker/js_color_picker_v2.css') ?>">
	<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('assets/datepicker.css') ?>">
</head>
<body style="background-image:url('<?= base_url('assets/images/bg/global.png') ?>')">
	<div class="outer">

		<div class="header">

			<div class="nav-box">
				<?php if(!$this->loggedin){ echo '<br /><br />'; } ?>
				<?php
				$i=0;
				if(isset($menu)){
					foreach( $menu as $link ){
						echo "\n".'<a href="'.$link['href'].'" title="'.$link['title'].'">'.$link['text'].'</a>'."\n";
						if( $i < count($menu)-1 ){ echo '<img src="webroot/images/blank.png" width="16" alt=" " />'."\n\t\t"; }
						$i++;
					}
				}
				?><br />
				<?php if($this->loggedin){ ?>
					<p class="normal">Logged in as <?php echo (strlen($this->session->userdata('displayname')) > 1) ? $this->session->userdata('displayname') : $this->session->userdata('username'); ?></p>
				<?php } ?>
			</div>

			<br />

			<span class="title">
				<?php
				if($this->session->userdata('schoolname')){
					echo '<a href="'.$this->session->userdata('schoolurl').'">'.$this->session->userdata('schoolname').'</a>';
				} else {
					echo '<a href="'.$this->config->item('base_url').'" title="ClassroomBookings" style="font-weight:normal;color:#0081C2;letter-spacing:-2px">classroom<span style="color:#ff6400;font-weight:bold">bookings</span></a>';
					echo '<sup style="font-weight:bold;color:#555;letter-spacing:-1px;font-size:12pt">BETA</sup>';
				}
				?>
			</span>

		</div>

		<?php if(isset($midsection)){ ?>
			<div class="mid-section" align="center">
				<h1 style="font-weight:normal"><?php echo $midsection ?></h1>
			</div>
		<?php } ?>

		<div class="content_area">
			<?php if(isset($showtitle)){ echo '<h2>'.$showtitle.'</h2>'; } ?>
			<?php echo $body ?>
		</div>

		<div class="footer">
			<br />

			<div id="footer">
				<?php
				if(isset($menu)){ foreach( $menu as $link ){
					echo "\n".'<a href="'.$link['href'].'" title="'.$link['title'].'">'.$link['text'].'</a>'."\n";
					echo '<img src="webroot/images/blank.png" width="16" height="10" alt=" " />'."\n";
				} }
				?>
				<br /><br /><span style="font-size:90%;color:#678;"><a href="https://www.classroombookings.com/" target="_blank">Classroombookings</a> version <?= VERSION ?>. &copy; <?= date('Y') ?> Craig A Rodway.<br />This page was loaded in <?php echo $this->benchmark->elapsed_time() ?> seconds.</span><br />
				<br />
			</div>
		</div>
	</div>

	<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>

	<?php
	$scripts = array();
	$scripts[] = base_url('assets/js/prototype.lite.js');
	$scripts[] = base_url('assets/js/util.js');
	$scripts[] = base_url('assets/js/sorttable.js');
	$scripts[] = base_url('assets/cpicker/color_functions.js');
	$scripts[] = base_url('assets/cpicker/js_color_picker_v2.js');
	$scripts[] = base_url('assets/js/datepicker.js');
	$scripts[] = base_url('assets/js/imagepreview.js');

	foreach ($scripts as $script)
	{
		echo "<script type='text/javascript' src='{$script}'></script>\n";
	}

	?>


</body>
</html>
