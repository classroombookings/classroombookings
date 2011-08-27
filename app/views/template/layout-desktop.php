<?php
// Get school name
$school_name = $this->settings->get('school_name');

// Decide on page title
$title_arr[] = (isset($title)) ? $title : NULL;
$title_arr[] = 'Classroombookings';
$title_arr[] = (!empty($school_name)) ? $school_name : NULL;
$title_string = implode(' - ', array_filter($title_arr));

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
	
	<base href="<?php echo $this->config->item('base_url') . 'assets/' ?>">
	
	<title><?php echo $title_string ?></title>

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout-fluid.css">
	<link rel="stylesheet" href="css/layout.css">
	
	<style type="text/css"><?php
	$weeks = $this->weeks_model->get();
	if(!empty($weeks)){
		$this->load->view('template/_weekcss', array('weeks' => $weeks));
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
		<?php $this->load->view('template/layout-desktop.header-right.php') ?>
	</div>
	
</div> 


<div class="colmask leftmenu"> 
	
	<div class="colright">
		
		<div class="col1wrap">
			<div class="col1 container">
				
				<?php echo (isset($alert)) ? $alert : $this->session->flashdata('flash') ?>
				
				<?php echo $body ?>
				
				<?php /* <div class="one-third column">One</div>
				<div class="one-third column">Two</div>
				<div class="one-third column">Three</div>
				<br class="clear">
				<div class="four columns">LEFT</div>
				<div class="eight columns">RIGHT</div> */
				?>
				
			</div> 
		</div> 
		
		<!-- Sidebar -->
		<div class="col2" id="sidebar"> 
			<?php echo $sidebar ?>
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