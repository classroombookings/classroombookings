<?php

$mobile = $this->agent->is_mobile();

if($this->loggedin){
	$menu[1]['text'] = '<img src="webroot/images/ui/link_controlpanel.gif" hspace="4" align="top" alt=" " />Control Panel';
	$menu[1]['href'] = site_url('controlpanel');
	$menu[1]['title'] = 'Tasks';
	
	#$menu[2]['text'] = '<img src="webroot/images/ui/link_help.gif" hspace="4" align="top" alt=" " />Help';
	#$menu[2]['href'] = site_url('help'.ereg_replace('help(/)', '', $this->uri->uri_string()));
	#$menu[2]['title'] = 'Get help on this page';

	if($this->userauth->CheckAuthLevel(ADMINISTRATOR)){ $icon = 'user_administrator.gif'; } else { $icon = 'user_teacher.gif'; }
	$menu[3]['text'] = '<img src="webroot/images/ui/logout.gif" hspace="4" align="top" alt=" " />Logout';
	$menu[3]['href'] = site_url('logout');
	$menu[3]['title'] = 'Close your current classroombookings session';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>classroombookings | <?php echo strtolower($title) ?></title>
	<base href="<?php echo $this->config->config['base_url'] ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="classroom, booking, room, school, education, schedule, timetable, room booking software" />
	<meta name="description" content="ClassroomBookings; the new classroom booking website for schools." />
	<meta name="author" content="Craig Rodway" />
	<link rel="stylesheet" type="text/css" media="screen" href="webroot/style.css" />
	<link rel="stylesheet" type="text/css" media="print" href="webroot/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="webroot/sorttable.css" />
	<script type="text/javascript" src="webroot/js/prototype.lite.js"></script>
	<script type="text/javascript" src="webroot/js/util.js"></script>
	<script type="text/javascript" src="webroot/js/sorttable.js"></script><?php
	$js_cpicker = array('weeks', 'school');
	if(in_array($this->uri->segment(1), $js_cpicker)){
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen" href="webroot/cpicker/js_color_picker_v2.css" />';
		echo "\n".'<script type="text/javascript" src="webroot/cpicker/color_functions.js"></script>';		
		echo "\n".'<script type="text/javascript" src="webroot/cpicker/js_color_picker_v2.js"></script>';
	}
	$js_datepicker = array('holidays', 'weeks', 'bookings');
	if(in_array($this->uri->segment(1), $js_datepicker)){
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen" href="webroot/datepicker.css" />';
		echo "\n".'<script type="text/javascript" src="webroot/js/datepicker.js"></script>';
	}
	$js_imagepreview = array('rooms','bookings');
	if(in_array($this->uri->segment(1), $js_imagepreview)){
		echo "\n".'<script type="text/javascript" src="webroot/js/imagepreview.js"></script>';
	}
	if(!$mobile){
		$bg = (isset($this->school_id)) ? $this->school_id : 'global';
		$body_attr = 'style="background-image:url(\'webroot/images/bg/'.$bg.'.png\')"';
	} else {
		$body_attr = '';
	}
	?>
</head>
<body <?php echo $body_attr ?>>
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
					echo '<a href="'.$this->config->item('base_url').'" title="Classroom Bookings" style="font-weight:normal;color:#0081C2;letter-spacing:-2px">classroom<span style="color:#ff6400;font-weight:bold">bookings</span></a>';
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
				<br /><br /><span style="font-size:90%;color:#678;">&copy; Copyright 2006 Craig Rodway.<br />This page was loaded in <?php echo $this->benchmark->elapsed_time() ?> seconds.</span><br />
			<br />
			</div>
		</div>
	</div>
<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>
</body>
</html>
