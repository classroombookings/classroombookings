<?php
$title = (!isset($title)) ? '' : $title;
$seg1 = $this->uri->segment(1, 'dashboard');
$seg2 = $this->uri->segment(2);

if(isset($sidebar)){
	$class['content'] = 'grid_8';
	$class['sidebar'] = 'grid_4';
} else {
	$class['content'] = 'grid_12';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Craig Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/reset.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/text.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/960.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui960.css" />
	<style type="text/css">
	body{background:#fff;	/*#F4F4F4;*/}
	#t{background:url(img/template/patt4.jpg) top left #069;padding-top:0.5em;}
	#tabs{/*background:#AFCEED;*/}
	
	#t2{background:#AFCEED;}
	#tabs2{background:#AFCEED;padding-top:0.5em;}
	
	#tr{text-align:right;}
	#tr span a{color:#fff;}
	
	#tabs ul, #tabs2 ul{
		list-style:none;
		padding:0;
		margin:0;
	}
	
	#tabs li, #tabs2 li{
		float:left;
		border:0;
		border-bottom-width:0;
		margin:0 0.5em 0 0;
	}
	
	#tabs a, #tabs2 a{
		display:block;
		padding:0.25em 1em 0.5em 1em;
		color:#fff;
		text-decoration:underline;
		font-weight:bold;
	}
	
	
	#tabs .selected, #tabs2 .selected, #tabs2 a:hover{
		position:relative;
		top:1px;
		background:#fff;	/*#F4F4F4;*/
		color:#000;
	}
	
	a.selected{
		text-decoration:none;
	}
	
	
	#ft{
		margin-top:4em;
		padding-top:0.5em;
		border-top:1px solid #ccc;
		font-size:90%;
		color:#666;
	}
	
	#ft p{
		margin-bottom:0.25em;
	}
	
	#fr{
		text-align:right;
	}
	
	<?php
	if(isset($subnav)){
		echo '#tabs .selected, #tabs a:hover{background:#AFCEED;color:#000;}';
	} else {
		echo '#tabs .selected, #tabs a:hover{background:#fff;color:#000;}';
	}
	
	
	$weeks = $this->weeks_model->get();
	foreach($weeks as $week){
		$cssarr[] = 'table tr.week_%1$d td{background:%2$s;color:%3$s}';
		$cssarr[] = '.week_%1$d{background:%2$s;color:%3$s}';
		$css = implode("\n", $cssarr);
		unset($cssarr);
		echo sprintf($css, $week->week_id, $week->colour, (isdark($week->colour)) ? '#fff' : '#000');
		echo "\n";
	}
	?>
	</style>
	<script type="text/javascript">
	var tabberOptions = {
		'cookie':"crbstabber",
		'onLoad':function(argsObj){
			//var t = argsObj.tabber;
			//var i;
			//if(t.id){t.cookie = t.id + t.cookie;}
			/* If a cookie was previously set, restore the active tab */
			//i = parseInt(getCookie(t.cookie));
			//if (isNaN(i)) { return; }
			//t.tabShow(i);
		},
		'onClick':function(argsObj){
			var c = argsObj.tabber.cookie;
			var i = argsObj.index;
			setCookie(c, i);
		}
	};
	</script>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/qTip.js"></script>
	<script type="text/javascript" src="js/tabber-minimized.js"></script>
	<script type="text/javascript" src="js/facebox.js"></script>
	<script type="text/javascript" src="js/syronex-colorpicker-mod.js"></script>
	<script type="text/javascript" src="js/jquery.date_input.min.js"></script>
	<script type="text/javascript" src="js/timepicker.js"></script>
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
</head>
<body>
	
	
	<div id="t">
		
		
		<!-- header 960 container -->
		<div class="container_12">
			
			
			<!-- #tl (top left) -->
			<div class="grid_6" id="tl">
				<p><a href="<?php echo site_url() ?>">
					<img src="img/template/title.png" alt="Classroombookings" />
				</a><br />
				<span><?php
					$settings = $this->settings->get_all('main');
					if($settings->schoolurl != FALSE){
						echo '<a href="'.$settings->schoolurl.'">'.$settings->schoolname.'</a>';
					} else {
						echo $settings->schoolname;
					}
				?></span>
				</p>
			</div>
			<!-- // tl -->
			
			
			<!-- #tr (top right) -->
			<div class="grid_6" id="tr"><br /><?php
				if($this->auth->logged_in()){
					echo sprintf('<span>Logged in as %s</span>', anchor('account', $this->session->userdata('display')));
					echo sprintf('<span>%s</span>', anchor('account/bookings', sprintf('%d active bookings', 5)));
					echo sprintf('<span>%s</span>', anchor('account/logout', 'Logout'));
				} else {
					echo sprintf('<span>You are not currently logged in. %s</span>', anchor('account/login', 'Login now.'));
				}
			?></div>
			<!-- // tr -->
			
			
			<div class="clear">&nbsp;</div>
			
			
			<!-- #tabs (primary nav) -->
			<div class="grid_12" id="tabs">
				<ul>
					<?php
					if($this->auth->check('dashboard', TRUE)){ echo dolink($seg1, 'dashboard', 'Dashboard'); }
					if($this->auth->check('bookings', TRUE)){ echo dolink($seg1, 'bookings', 'Bookings'); }
					if($this->auth->check('myprofile', TRUE)){ echo dolink($seg1, 'account', 'My Profile'); }
					if($this->auth->check('configure', TRUE)){ echo dolink($seg1, 'configure', 'Configure'); }
					if($this->auth->check('rooms', TRUE)){ echo dolink($seg1, 'rooms', 'Rooms'); }
					if($this->auth->check('academic', TRUE)){ echo dolink($seg1, 'academic/main', 'Academic setup'); }
					if($this->auth->check('departments', TRUE)){ echo dolink($seg1, 'departments', 'Departments'); }
					if($this->auth->check('reports', TRUE)){ echo dolink($seg1, 'reports', 'Reports'); }
					if($this->auth->check('users', TRUE)){ echo dolink($seg1, 'security/users', 'Security'); }
					?>
				</ul>
			</div>
			<!-- // #tabs -->
			
			
			<!-- needed to keep bg colour at each side of tabs -->
			<div class="clear">&nbsp;</div>
			
			
		</div>
		<!-- // header 960 container -->
		
	</div>
	
	
	<?php if(isset($subnav)){ ?>
	<!-- #t2 (secondary horizontal nav) -->
	<div id="t2">
		
		<div class="container_12">
			
			<!-- #tabs2 -->
			<div class="grid_12" id="tabs2">
				<ul><?php
					foreach($subnav as $item){
						if($this->auth->check($item[2], TRUE)){ echo dolink($seg2, $item[0], $item[1], 1); }
					}
				?></ul>
			</div>
			<!-- // #tabs2 -->
			
			<div class="clear">&nbsp;</div>
			
		</div>
		
	</div>
	<!-- // #t2 -->
	<?php } ?>
	
	
	<div class="clear">&nbsp;</div>
	
	
	<div class="container_12">
		
		
		<div class="grid_12">
			&nbsp;
		</div>
		
		
		<div class="clear">&nbsp;</div>
		
		
		<!-- stuff that goes above main content here (title, sublinks, flash msgs) -->
		<div class="grid_12">
			<?php
			$h1class = (isset($links)) ? 'class="nomargin"' : '';
			echo $this->session->flashdata('flash');
			echo (isset($pretitle)) ? $pretitle : '';
			echo (isset($pagetitle)) ? '<h1 ' . $h1class . '>' . $pagetitle . '</h1>' : '';
			echo (isset($links)) ? $links : '';
			?>
		</div>
		
		
		<div class="clear">&nbsp;</div>
		
		
		<!-- #content (main area) -->
		<div class="<?php echo $class['content'] ?>" style="margin-bottom:4em;" id="content">
			<?php echo (isset($body)) ? $body : '[Nothing to display]'; ?>
		</div>
		<!-- // #content (main area) -->
		
		
		<?php if(isset($sidebar)){ ?>
		<!-- #sidebar -->
		<div class="<?php echo $class['sidebar'] ?>" id="sidebar">
			<?php echo $sidebar ?>
		</div>
		<!-- // #sidebar -->
		<?php } ?>
		
		
		<!-- area to change the academic year, if appropriate -->
		<?php if($this->auth->logged_in() && $this->auth->check('changeyear', TRUE)){ ?>
			<div class="clear">&nbsp;</div>
			<div class="grid_12 extra"><?php
			$years = $this->years_model->get_dropdown();
			$this->load->view('template/set-year', array('years' => $years));
			?></div>
		<?php } ?>
		
		
		<?php if(isset($extra)){ ?>
		<!-- extra -->
		<div class="clear">&nbsp;</div>
		<div class="grid_12" class="extra">
			<?php echo $extra; ?>
		</div>
		<!-- // extra -->
		<?php } ?>
		
		
		<div class="clear">&nbsp;</div>
		
		
		<!-- #ft -->
		<div class="grid_12" id="ft">
		
			<div class="grid_7 alpha" id="fl">
				<p>&copy; Craig Rodway 2008.</p>
				<p><a href="http://classroombookings.com/">Classroombookings</a> is released under the GNU General Public Licence version 3.</p>
				<p>Total execution time: <?php echo $this->benchmark->elapsed_time() ?> seconds; Memory usage: <?php echo $this->benchmark->memory_usage() ?></p>
			</div>
			
			<div class="grid_5 omega" id="fr">
				<p><?php
				if($this->auth->logged_in()){
					echo sprintf('<span>Logged in as %s</span> &#183; ', anchor('account', $this->session->userdata('display')));
					echo sprintf('<span>%s</span> &#183; ', anchor('account/bookings', sprintf('%d active bookings', 5)));
					echo sprintf('<span>%s</span>', anchor('account/logout', 'Logout'));
				} else {
					echo sprintf('<span>You are not currently logged in. %s</span>', anchor('account/login', 'Login now.'));
				}
				?></p>
			</div>
		
		</div>
		<!-- // #ft -->
		
	</div>
	
	
</body>
</html>



<?php
/**
 * seg1 - the segment of the current URI at the position we want
 * href - path/to/url (gets truned into array, too)
 * text - text of link
 * i - index of href array to check uri segment to
 */
function dolink($seg, $href, $text, $i = 0){
	$hrefarr = explode('/', $href);
	$link = '<li><a href="%s"%s>%s</a></li>';
	$sel = ($seg == $hrefarr[$i]) ? ' class="selected"' : '';
	return sprintf($link, site_url($href), $sel, $text);
}
?>