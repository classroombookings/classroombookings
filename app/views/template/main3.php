<?php $title = (!isset($title)) ? '' : $title; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Craig Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout3.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui3.css" />
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
	<script type="text/javascript" src="js/syronex-colorpicker.js"></script>
	<script type="text/javascript" src="js/jquery.date_input.min.js"></script>
	<script type="text/javascript" src="js/timepicker.js"></script>
</head>

<body>
	
	<!-- top -->
	<div id="top">
		
		<!-- top-main -->
		<div id="top-main">
			
			<!-- title -->
			<div id="top-left">
				<a href="<?php echo site_url() ?>">
					<img src="img/template/title.gif" alt="Classroombookings" />
				</a><br />
				<span><?php
					$schoolname = $this->settings->get('schoolname');
					$schoolurl = $this->settings->get('schoolurl');
					if($schoolurl != FALSE){
						echo '<a href="'.$schoolurl.'">'.$schoolname.'</a>';
					} else {
						echo $schoolname;
					}
				?></span>
			</div>
			<!-- // title -->
			
			<!-- user-utils -->
			<div id="top-right"><?php
				if($this->auth->logged_in()){
					echo sprintf('<span>Logged in as %s</span>', anchor('account', $this->session->userdata('display')));
					echo sprintf('<span>%s</span>', anchor('account/bookings', sprintf('%d active bookings', 5)));
					echo sprintf('<span>%s</span>', anchor('account/logout', 'Logout'));
				} else {
					echo sprintf('<span>You are not currently logged in. %s</span>', anchor('account/login', 'Login now.'));
				}
			?></div>
			<!-- // user-utils -->
			
			
			<!-- primary nav -->
			<div id="top-menu">
				<ul>
					<?php
					if($this->auth->check('dashboard', TRUE)){ echo '<li>'.anchor('dashboard', 'Dashboard').'</li>'; }
					if($this->auth->check('bookings', TRUE)){ echo '<li>'.anchor('bookings', 'Bookings').'</li>'; }
					if($this->auth->check('myprofile', TRUE)){ echo '<li>'.anchor('account', 'My Profile').'</li>'; }
					if($this->auth->check('configure', TRUE)){ echo '<li>'.anchor('configure', 'Configure').'</li>'; }
					if($this->auth->check('rooms', TRUE)){ echo '<li>'.anchor('rooms', 'Rooms').'</li>'; }
					if($this->auth->check('academic', TRUE)){ echo '<li>'.anchor('academic/main', 'Academic Setup').'</li>'; }
					if($this->auth->check('departments', TRUE)){ echo '<li>'.anchor('departments', 'Departments').'</li>'; }
					if($this->auth->check('reports', TRUE)){ echo '<li>'.anchor('reports', 'Reports').'</li>'; }
					if($this->auth->check('users', TRUE)){ echo '<li>'.anchor('security/users', 'Security').'</li>'; }
					?>
				</ul>
			</div>
			<!-- // primary nav -->
			
		</div>
		<!-- // top-main -->
		
	</div>
	<!-- // top -->
	

	<!-- container -->
	<div id="container">
		
		
		<!-- wrapper -->
		<div id="wrapper">
			
			
			<!-- content -->
			<div id="content"<?php if(!isset($sidebar)){ echo ' class="solo"'; } ?>>
				<?php
				$h1class = (isset($links)) ? 'class="nomargin"' : '';
				echo $this->session->flashdata('flash');
				echo (isset($pretitle)) ? $pretitle : '';
				echo (isset($pagetitle)) ? '<h1 ' . $h1class . '>' . $pagetitle . '</h1>' : '';
				echo (isset($links)) ? $links : '';
				echo (isset($body)) ? $body : 'Nothing to display.';
				?>
			</div>
			<!-- // content -->
			
			
			<?php if(isset($sidebar)){ ?>
			<!-- sidebar -->
			<div id="sidebar">
				<div><?php echo $sidebar ?></div>
			</div>
			<!-- // sidebar -->
			<?php } ?>
			
			
		</div>
		<!-- // wrapper -->
		
		
		<?php if($this->auth->logged_in() && $this->auth->check('changeyear', TRUE)){
			echo '<div class="extra">';
			$years = $this->years_model->get_dropdown();
			$this->load->view('template/set-year', array('years' => $years));
			echo '</div>';
		}
		?>
		
		
		<?php if(isset($extra)){ ?>
		<!-- extra -->
		<div id="extra">
			<?php echo $extra; ?>
		</div>
		<!-- // extra -->
		<?php } ?>
		
		<!-- footer -->
		<div id="footer">
			<p>&copy; Craig Rodway 2008. <a href="http://classroombookings.com/">Classroombookings</a> is released under the GNU General Public Licence version 3.</p>
			<p>Total execution time: <?php echo $this->benchmark->elapsed_time() ?> seconds; Memory usage: <?php echo $this->benchmark->memory_usage() ?></p>
		</div>
		<!-- // footer -->
		
	</div>
</body>
</html>