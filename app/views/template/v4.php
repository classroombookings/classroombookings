<?php
// Column widths in main area
define('W_SIDEBAR', 5);
define('W_MAIN', 19);

// Decide on page title
$title = (!isset($title)) ? '' : $title;

// URI segments
$seg1 = $this->uri->segment(1, 'dashboard');
$seg2 = $this->uri->segment(2);

// Decide which classes to apply to the main grid divs
if(!isset($sidebar)){
	$width_left = W_MAIN + W_SIDEBAR;
} else {
	$width_left = W_MAIN;
}

// Can change academic year?
$changeyear = ($this->auth->logged_in() && $this->auth->check('changeyear', TRUE));
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta name="author" content="Craig A Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="stylesheet" href="css/v4/reset.css" /> 
	<link rel="stylesheet" href="css/v4/text.css" /> 
	<link rel="stylesheet" href="css/v4/960_24_col.css" /> 
	<link rel="stylesheet" href="css/v4/crbs.css" /> 
	<link rel="stylesheet" href="css/v4/crbs-<?php echo $this->config->item('theme') ?>.css" />
	<style type="text/css"><?php
		$weeks = $this->weeks_model->get();
		if(!empty($weeks)){
			foreach($weeks as $week){
				$cssarr[] = 'table tr.week_%1$d td{background:%2$s;color:%3$s}';
				$cssarr[] = '.week_%1$d{background:%2$s;color:%3$s}';
				$cssarr[] = '.week_%1$d a{color:%3$s !important}';
				$cssarr[] = '.week_%1$d_fg{color:%2$s}';
				$css = implode("\n", $cssarr);
				unset($cssarr);
				echo sprintf($css, $week->week_id, $week->colour, (isdark($week->colour)) ? '#fff' : '#000');
				echo "\n";
			}
		}
	?></style> 
	<script type="text/javascript">
	var baseurl = "<?php echo $this->config->item('base_url').'web/' ?>";
	var siteurl = "<?php echo site_url() ?>/";
	</script>
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="js/qTip.js"></script>
	<script type="text/javascript" src="js/facebox.js"></script>
	<script type="text/javascript" src="js/syronex-colorpicker-mod.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<?php
	// Load additional javascript requested by controller
	if(isset($js)){
		foreach($js as $j){
			echo '<script type="text/javascript" src="js/' . $j .'"></script>';
			echo "\n";
		}
	}
	?>
	<script type="text/javascript">jQuery.fx.off = true;</script>
</head> 

<body>

	
	<div id="ajaxload">Loading...</div>

	
	<div id="header">
		<div class="container_24">
			<div class="grid_12" id="header-left">
				<a href="<?php echo site_url() ?>">Classroombookings</a>
				<span class="schoolname">
				<?php
				$settings = $this->settings->get_all('main');
				if($settings->schoolurl != FALSE){
					echo '<a href="'.$settings->schoolurl.'">'.$settings->schoolname.'</a>';
				} else {
					echo $settings->schoolname;
				}
				?>
				</span>
			</div>
			<div class="grid_12" id="header-right"><div class="mr"><?php
				if($this->auth->logged_in()){
					echo sprintf('<span>Logged in as <strong>%s</strong></span>', anchor('account/main', $this->session->userdata('display')));
					echo sprintf(' &mdash; <span>%s</span>', anchor('account/logout', 'Logout'));
				} else {
					echo sprintf('<span>%s</span>', anchor('account/login', 'Login'));
				}
			?></div></div>
			<div class="clear"></div>
		</div>
	</div>


	<div class="container_24" id="menu">
		<div class="grid_24">
			<ul class="h"><?php
				if($this->auth->check('dashboard', TRUE)){ echo dolink($seg1, 'dashboard', 'Dashboard'); }
				if($this->auth->check('bookings', TRUE)){ echo dolink($seg1, 'bookings', 'Bookings'); }
				#if($this->auth->check('account', TRUE)){ echo dolink($seg1, 'account/activebookings', 'My Profile'); }
				if($this->auth->check('configure', TRUE)){ echo dolink($seg1, 'configure/general', 'Configure'); }
				if($this->auth->check('rooms', TRUE)){ echo dolink($seg1, 'rooms/manage', 'Rooms'); }
				if($this->auth->check('academic', TRUE)){ echo dolink($seg1, 'academic/main', 'Academic setup'); }
				if($this->auth->check('departments', TRUE)){ echo dolink($seg1, 'departments', 'Departments'); }
				if($this->auth->check('reports', TRUE)){ echo dolink($seg1, 'reports', 'Reports'); }
				$security = ($this->auth->check('users', TRUE) OR $this->auth->check('groups', TRUE) OR $this->auth->check('permissions', TRUE));
				if($this->auth->check('users', TRUE)){
					echo dolink($seg1, 'security/users', 'Security');
				} elseif($security){
					echo dolink($seg1, 'security/main', 'Security');
				} 
			?></ul>
		</div>
		<div class="clear"></div>
	</div>


	<div class="container_24" id="title">

		<div class="grid_10" id="title-left"><span class="ml"><?php echo $title ?></span></div>
		<div class="grid_14" id="title-right">
			<ul class="h"><?php
			if(isset($subnav)){
				foreach($subnav as $item){
					if($this->auth->check($item[2], TRUE)){ echo dolink($seg2, $item[0], $item[1], 1); }
				}
			}
			?></ul>
		</div>
		<div class="clear"></div>
	
	</div>


	<div class="container_24" id="main">

		<div class="grid_24" id="links"><?php if(isset($links)){ echo $links; } ?></div>
		
		<div class="grid_24" id="flashmsg">
			<?php echo (isset($alert)) ? $alert : $this->session->flashdata('flash'); ?>
		</div>

		<div class="grid_<?php echo $width_left ?>" id="main-left"><div class="ml<?php if(!isset($sidebar)){ echo ' mr'; } ?>">
			<?php echo (isset($body)) ? $body : '[Nothing to display]'; ?>
		</div></div>

		<?php if(isset($sidebar)): ?>
		<div class="grid_<?php echo W_SIDEBAR ?>" id="main-right"><div class="mr">
			<?php echo $sidebar ?>
		</div></div>
		<?php endif; ?>
		
		<div class="clear"></div>
		
	</div>


	<div class="container_24" id="footer">
		<div class="grid_24">
			<p>&copy; Craig Rodway 2006 &mdash; <?php echo date('Y') ?>.
			<a href="http://classroombookings.com/">Classroombookings</a> is released under the Affero GNU General Public Licence version 3.</p>
			<p>Total execution time: <?php echo $this->benchmark->elapsed_time() ?> seconds; Memory usage: <?php echo $this->benchmark->memory_usage() ?></p>
		</div>
	</div>


</body> 
</html>


<?php
/**
 * seg1 - the segment of the current URI at the position we want
 * href - path/to/url (gets truned into array)
 * text - text of link
 * i - index of href array to check uri segment to
 */
function dolink($seg, $href, $text, $i = 0){
	$hrefarr = (strpos($href, '/') === FALSE) ? array($href) : explode('/', $href);
	#echo $hrefarr[$i] . "/ ";
	#echo "Seg: $seg/ ";
	#$hrefarr = explode('/', $href);
	$link = '<li><a href="%s"%s>%s</a></li>';
	$sel = ($seg == $hrefarr[$i]) ? ' class="current"' : '';
	return sprintf($link, site_url($href), $sel, $text);
}
?>
