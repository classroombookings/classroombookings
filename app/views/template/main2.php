<?php $title = (!isset($title)) ? '' : $title; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Craig Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout2.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui2.css" />
	
	<script type="text/javascript">
	var tabberOptions = {
		'cookie':"crbstabber",
		'onLoad':function(argsObj){
			var t = argsObj.tabber;
			var i;
			/* Optional: Add the id of the tabber to the cookie name to allow
			   for multiple tabber interfaces on the site.  If you have
			   multiple tabber interfaces (even on different pages) I suggest
			   setting a unique id on each one, to avoid having the cookie set
			   the wrong tab.
			*/
			if(t.id){
				t.cookie = t.id + t.cookie;
			}
			/* If a cookie was previously set, restore the active tab */
			i = parseInt(getCookie(t.cookie));
			if (isNaN(i)) { return; }
			t.tabShow(i);
		},
		'onClick':function(argsObj){
			var c = argsObj.tabber.cookie;
			var i = argsObj.index;
			setCookie(c, i);
		}
	};
	</script>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/jquery.boxy.js"></script>
	<!-- <script type="text/javascript" src="js/jquery-ui-personalized-1.6rc2.js"></script> -->
	<script src="js/qTip.js" type="text/javascript"></script>
	<script src="js/tabber-minimized.js" type="text/javascript"></script>
</head>

<body>

	<!-- // #header -->
	<div id="header">
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
	<!-- #header // -->

	<!-- // #sidebar -->
	<div id="sidebar">
		<ul class="links"><?php
			if($this->auth->logged_in()){
				echo sprintf('<li>Logged in as %s</li>', anchor('account/edit', $this->session->userdata('display')));
				echo sprintf('<li>%s</li>', anchor('account/bookings', sprintf('%d active bookings', 5)));
				echo sprintf('<li>%s</li>', anchor('account/logout', 'Logout'));
			} else {
				echo sprintf('<li>%s</li>', anchor('account/login', 'Login'));
			}
			?></ul>
		<br />
		<ul class="menu">
			<li><?php echo anchor('dashboard', 'Dashboard', 'style="background-image:url(img/ico/home.gif)"') ?></li>
			<li><?php echo anchor('bookings', 'Bookings', 'style="background-image:url(img/ico/books.gif)"') ?></li>
			<li><?php echo anchor('account', 'My Profile', 'style="background-image:url(img/ico/user_grey.gif)"') ?></li>
			<li><?php echo anchor('configure', 'Configure', 'style="background-image:url(img/ico/tools.gif)"') ?></li>
			<li><?php echo anchor('rooms', 'Rooms', 'style="background-image:url(img/ico/door.gif)"') ?></li>
			<li><?php echo anchor('periods', 'Periods', 'style="background-image:url(img/ico/clock1.gif)"') ?></li>
			<li><?php echo anchor('weeks', 'Weeks', 'style="background-image:url(img/ico/calendar.gif)"') ?></li>
			<li><?php echo anchor('holidays', 'Holidays', 'style="background-image:url(img/ico/weather.gif)"') ?></li>
			<li><?php echo anchor('departments', 'Departments', 'style="background-image:url(img/ico/addressbook.gif)"') ?></li>
			<li><?php echo anchor('reports', 'Reports', 'style="background-image:url(img/ico/piechart.gif)"') ?></li>
			<li><?php echo anchor('security/users', 'Users and Security', 'style="background-image:url(img/ico/lock.gif)"') ?></li>
		</ul>
	</div>
	<!-- #sidebar //-->

	<!-- // #main -->
	<div id="main">
		<?php
		echo $this->session->flashdata('flash');
		echo (isset($pretitle)) ? $pretitle : '';
		echo (isset($pagetitle)) ? '<h1>' . $pagetitle . '</h1>' : '';
		echo (isset($body)) ? $body : 'Nothing to display.';
		?>
	</div>
	<!-- #main // -->

</body>

</html>
