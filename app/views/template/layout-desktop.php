<?php
// Get all settings
$settings = $this->settings->get();
$school_name = $this->settings->get('school_name');

// Decide on page title
$title_arr[] = (isset($title)) ? $title : NULL;
$title_arr[] = 'Classroombookings';
$title_arr[] = (!empty($school_name)) ? $school_name : NULL;
$title_string = implode(' - ', $title_arr);

// URI segments
$seg1 = $this->uri->segment(1, 'dashboard');
$seg2 = $this->uri->segment(2);

// Can user change working academic year?
$changeyear = $this->auth->check('changeyear', true);

?>
<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>

	<meta charset="utf-8" />
	
	<base href="<?php echo $this->config->item('base_url').'assets/'; ?>">
	
	<title><?php echo $title_string ?></title>

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<link rel="stylesheet" href="css/baseline.reset.css">
	<link rel="stylesheet" href="css/baseline.base.css">
	<link rel="stylesheet" href="css/baseline.type.css">
	<link rel="stylesheet" href="css/baseline.table.css">
	<link rel="stylesheet" href="css/layout-fluid.css">
	<link rel="stylesheet" href="css/layout.css">
	
	<style type="text/css"><?php
	$weeks = $this->weeks_model->get();
	if(!empty($weeks)){
		foreach($weeks as $week){
			$cssarr[] = 'table tr.week_%1$d td{ background:%2$s; color:%3$s }';
			$cssarr[] = '.week_%1$d{ background:%2$s; color:%3$s }';
			$cssarr[] = '.week_%1$d a{ color:%3$s !important }';
			$cssarr[] = '.week_%1$d_fg{ color:%2$s }';
			$css = implode("\n", $cssarr);
			unset($cssarr);
			echo sprintf($css, $week->week_id, $week->colour, (isdark($week->colour)) ? '#fff' : '#000');
			echo "\n";
		}
	}
	?></style>

</head>
<body>


<div id="header">
	
	<div class="header-left">
		<ul class="horiz">
			<li><a href="#!/dashboard" data-name="dashboard" class="i dashboard">Home</a></li> 
			<li><a href="#!/bookings" data-name="bookings" class="i bookings">Bookings</a></li> 
			<li><a href="#!/configure" data-name="configure" class="i configure">Configure</a></li> 
			<li><a href="#!/reports" data-name="reports" class="i reports">Reports</a></li>
		</ul>
	</div>
	
	<div class="header-right">
		<ul class="horiz">
			<li><a href="#!/account" data-name="account" class="i account">Welcome, User</a></li>
			<li><a href="#!/account/logout" data-name="logout" class="i security">Log Out</a></li>
		</ul>
	</div>
	
</div> 

<div class="colmask leftmenu"> 
	<div class="colright"> 
		<div class="col1wrap"> 
			<div class="col1"> 
				
			</div> 
		</div> 
		
		<!-- Sidebar -->
		<div class="col2"> 
			<ul class="nav">
				<li><a class="i configure-settings" data-slug="settings">Display settings</a></li>
				<li><a class="i configure-authentication" data-slug="authentication">Authentication</a></li>
				<li><a class="i configure-users" data-slug="users">Users</a></li>
				<li><a class="i configure-groups" data-slug="groups">User groups</a></li>
				<li><a class="i configure-permissions" data-slug="permissions">Group permissions</a></li>
				<li><a class="i configure-departments" data-slug="departments">Departments</a></li>
				<li><a class="i configure-rooms" data-slug="rooms">Rooms</a></li>
				<li><a class="i configure-years" data-slug="years">Academic years</a></li>
				<li><a class="i configure-terms" data-slug="terms">Term dates</a></li>
				<li><a class="i configure-weeks" data-slug="weeks">Timetable weeks</a></li>
				<li><a class="i configure-holidays" data-slug="holidays">Holidays</a></li>
				<li><a class="i configure-periods" data-slug="periods">Periods</a></li>
			</ul>
		</div> 
		<!-- / sidebar -->
		
		
	</div> 
</div> 


<div id="footer"> 
	<p>Classroombookings is released under the Affero GNU General Public License v3.<br>
	&copy; 2006 &mdash; 2011 Craig A Rodway.</p>
</div>

	
</body>
</html>