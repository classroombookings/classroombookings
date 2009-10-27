<?php
$title = (!isset($title)) ? '' : $title;

// URI segments
$seg1 = $this->uri->segment(1, 'dashboard');
$seg2 = $this->uri->segment(2);

// Decide which classes to apply to the main grid divs
if(isset($sidebar)){
	$class = 'with-side';
} else {
	$class = 'without-side';
}

// Can change academic year?
$changeyear = ($this->auth->logged_in() && $this->auth->check('changeyear', TRUE));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Classroombookings | <?php echo $title ?></title>
		<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
		<meta name="author" content="Craig A Rodway" />
		<link rel="shortcut icon" type="image/x-icon" href="favicon2.ico" />
		<link rel="icon" type="image/x-icon" href="favicon2.ico" />
		<link rel="stylesheet" type="text/css" href="css/mg/reset.css" />
		<link rel="stylesheet" type="text/css" href="css/mg/text.css" />
		<link rel="stylesheet" type="text/css" href="css/mg/mg.css" />
		<link rel="stylesheet" type="text/css" href="css/mg/crbs.css" />
		<link rel="stylesheet" type="text/css" href="css/mg/colours.css" />
		<style type="text/css">
			.bp{border:1px solid #ccc;padding:10px;}
			.pad{padding:5px;}
			#page{background-color:#fff;}
			
			ul.h{
				list-style-type:none;
			}
			ul.h li{
				float:left;
				margin:0 20px 0 0;
				padding:0;
			}
			.ui-tabs .ui-tabs-hide {
				display: none;
			}
			<?php
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
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.cookie.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="js/qTip.js"></script>
		<!-- <script type="text/javascript" src="js/facebox.js"></script> -->
		<script type="text/javascript" src="js/jquery.boxy.js"></script>
		<script type="text/javascript" src="js/syronex-colorpicker-mod.js"></script>
		<script type="text/javascript" src="js/ajax.js"></script>
		<!-- <script type="text/javascript" src="js/tabber-minimized.js"></script>
		
		<script type="text/javascript" src="js/jquery.date_input.min.js"></script>
		<script type="text/javascript" src="js/timepicker.js"></script>
		<script type="text/javascript" src="js/jquery.autocomplete.js"></script> -->
		
		<script type="text/javascript">jQuery.fx.off = true;</script>
	</head>
	
	
	<body>
	
		<div id="ajaxload">Loading...</div>
		
		<div id="page" class="fluid">
			
			
			<!-- #head -->
			<div class="row" id="head">
			
				<!-- #head-account -->
				<div class="right column grid_4" id="head-account">
					<div class="cell pad"><?php
						if($this->auth->logged_in()){
							echo sprintf('<span>Logged in as <strong>%s</strong></span>', anchor('account', $this->session->userdata('display')));
							echo sprintf(' &mdash; <span>%s</span>', anchor('account/logout', 'Logout'));
						} else {
							echo sprintf('<span>%s</span>', anchor('account/login', 'Login'));
						}
					?></div>
				</div>
				<!-- // #head-account -->
			
				<div class="row">
					
					<!-- #head-menu -->
					<div class="column grid_12p" id="head-menu">
						<div class="cell pad">
							<ul id="head-menulist" class="h">
								<?php
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
								?>
							</ul>
						</div>
					</div>
					<!-- // #head-menu -->
					
				</div>
				
			</div>
			<!-- // #head -->
			
			
			<?php if(isset($subnav) OR $changeyear == TRUE): ?>
			<!-- #subhead -->
			<div class="row" id="subhead">
			
				<!-- area to change the academic year, if appropriate -->
				<!-- #subhead-year -->
				<div class="right column grid_6" id="subhead-year">
					<div class="cell pad"><?php
						if($changeyear == TRUE){
						$years = $this->years_model->get_dropdown();
						$this->load->view('template/set-year', array('years' => $years));
						}
					?></div>
				</div>
				<!-- // #subhead-year -->
				
				<div class="row">
				<!-- #subhead-main -->
				<div class="column grid_12p" id="subhead-main">
					<div class="cell pad">
						<ul id="subhead-menulist" class="h">
							<?php if(isset($subnav)){
								foreach($subnav as $item){
									if($this->auth->check($item[2], TRUE)){ echo dolink($seg2, $item[0], $item[1], 1); }
								}
							} ?>
						</ul>
					</div>
				</div>
				<!-- // #subhead-main -->
				</div>
				
			</div>
			<!-- // #subhead -->
			<?php endif; ?>
			
			
			<br />
			
			
			<div class="row">
				
				<div class="column grid_6" id="middle-logo">
					<p class="cell">
					<a href="<?php echo site_url() ?>">
						<img src="img/template/title.gif" alt="Classroombookings" />
					</a><br /><span><?php
						$settings = $this->settings->get_all('main');
						if($settings->schoolurl != FALSE){
							echo '<a href="'.$settings->schoolurl.'">'.$settings->schoolname.'</a>';
						} else {
							echo $settings->schoolname;
						}
					?></span>
					</p>
				</div>
				
				<div class="row">
					<div class="column grid_12p" id="middle-alert">
						<!-- <p class="bp cell">status message/alert</p> -->
						<?php echo $this->session->flashdata('flash'); ?>&nbsp;
					</div>
				</div>
				
			</div>
			
			
			<br />
			
			
			<div class="row">
				
				<?php if(isset($sidebar)): ?>
				<div class="column grid_3" id="main-sidebar">
					<div class="cell"><!-- <h1>Sidebar</h1> -->
					<?php echo $sidebar ?>
					</div>
				</div>
			
				<div class="row <?php echo $class ?>">
				<?php endif; ?>
					
					<div class="column grid_12p" id="main-body">
						<div class="cell"><?php
							echo (isset($pretitle)) ? $pretitle : '';
							echo (isset($pagetitle)) ? '<h1>' . $pagetitle . '</h1>' : '';
							echo (isset($links)) ? $links : '';
						?></div>
						<div class="cell"><?php
							echo (isset($body)) ? $body : '[Nothing to display]';
						?></div>
					</div>
					
				<?php if(isset($sidebar)): ?>
				</div>
				<?php endif; ?>
			</div>
			
			
			
			
			<!-- #footer -->
			<div class="row">
				<div class="column grid_12p" id="footer">
					<div class="cell">
						<p>&copy; Craig Rodway 2006 &mdash; <?php echo date('Y') ?>.</p>
						<p><a href="http://classroombookings.com/">Classroombookings</a> is released under the GNU General Public Licence version 3.</p>
						<p>Total execution time: <?php echo $this->benchmark->elapsed_time() ?> seconds; Memory usage: <?php echo $this->benchmark->memory_usage() ?></p>
					</div>
				</div>
			</div>
			<!-- // #footer -->
			
			
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
	$hrefarr = explode('/', $href);
	$link = '<li><a href="%s"%s>%s</a></li>';
	$sel = ($seg == $hrefarr[$i]) ? ' class="current"' : '';
	return sprintf($link, site_url($href), $sel, $text);
}
?>