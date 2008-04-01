<?php
if(!isset($title)){
	$title = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Craig Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui.css" />
	<link rel="stylesheet" type="text/css" media="print" href="css/print.css" />
</head>
<body>
	
	<!-- Top area -->
	<div id="top">
	
		<!-- Main top area container -->
		<div id="top-main">
		
			<!-- Title -->
			<div id="top-left">
				<a href="<?php echo site_url() ?>">
					<img src="img/template/title.gif" alt="Classroombookings" />
				</a><br />
				<span>Bishop Barrington School Sports With Mathematics College</span>
			</div>
			
			<!-- User utils -->
			<div id="top-right">
				<span>Logged in as <?php echo anchor('account/view/46', 'john.smith'); ?></span>
				<span><?php echo anchor('account/bookings', '4 active bookings') ?></span>
				<span><?php echo anchor('account/logout', 'Logout') ?></span>
			</div>
			
			<!-- main nav menu -->
			<div id="top-menu">
				<ul>
					<li><?php echo anchor('bookings', 'Bookings') ?></li>
					<li><?php echo anchor('account', 'Account') ?></li>
					<li><?php echo anchor('settings', 'Settings') ?></li>
					<li><?php echo anchor('rooms', 'Rooms') ?></li>
					<li><?php echo anchor('periods', 'Periods') ?></li>
					<li><?php echo anchor('weeks', 'Weeks') ?></li>
					<li><?php echo anchor('holidays', 'Holidays') ?></li>
					<li><?php echo anchor('departments', 'Departments') ?></li>
					<li><?php echo anchor('reports', 'Reports') ?></li>
					<li><?php echo anchor('users', 'Users') ?></li>
				</ul>
			</div>
			
		</div>
	
	</div>
	

	<!-- main container -->
	<div id="container">
		
		<!-- wrapper -->
		<div id="wrapper">
		
			<!-- content -->
			<div id="content"<?php if(!isset($sidebar)){ echo ' class="solo"'; } ?>>
			
				<?php if(isset($pagetitle)){ ?>
				<h1><?php echo $pagetitle ?></h1>
				<?php } ?>
				
				<?php echo (isset($body)) ? $body : 'Nothing to display.'; ?>
				
				<!-- <p>
				Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla rutrum leo id mauris. In a mauris. Vestibulum congue lacus et justo. Nulla eu felis. Duis eget augue at risus interdum ullamcorper. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Suspendisse potenti. Sed consequat, tortor in lacinia scelerisque, libero ipsum tincidunt leo, a euismod urna sapien vehicula orci. Donec volutpat rutrum turpis. Donec vel odio quis tellus sollicitudin gravida. Mauris leo odio, placerat sed, accumsan malesuada, tincidunt eu, lacus.
				</p>
				<p>
				Praesent quis turpis nec ipsum hendrerit consectetuer. Sed ut mauris. Nulla facilisi. Morbi venenatis arcu nec felis. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris quam tellus, tristique sed, iaculis vel, varius in, purus. Pellentesque viverra, justo a interdum porta, sem dolor consectetuer arcu, sed sagittis pede nisi eu quam. Maecenas viverra, nulla in lobortis vulputate, sem tortor mattis enim, quis imperdiet nunc nisi nec purus. Sed at turpis. Nunc sit amet velit vitae eros laoreet consequat. Quisque at mi eu sapien gravida consectetuer. Nullam imperdiet volutpat risus. Donec non neque id justo bibendum tempor. In mattis malesuada lectus. Suspendisse quis massa quis arcu mattis commodo. Mauris fringilla.
				</p> -->
			</div>
		
		</div>
		
		<?php if(isset($sidebar)){ ?>
		<!-- sidebar -->
		<div id="sidebar">
			<div>
				<?php echo $sidebar; ?>
			</div>
		</div>
		<?php } ?>
		
		<?php if(isset($extra)){ ?>
		<!-- extra -->
		<div id="extra">
			<?php echo $extra; ?>
		</div>
		<?php } ?>
		
		<!-- footer -->
		<div id="footer">
			<p><a href="http://classroombookings.com/" target="_blank">Classroombookings</a> is 
			released under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU 
			General Public License</a>.</p>
		</div>
		
	</div>
</body>
</html>