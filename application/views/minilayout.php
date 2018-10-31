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
	<!--[if IE]><style type="text/css">img{behavior:url(webroot/pngbehavior.htc);}</style><![endif]-->
	<script type="text/javascript" src="webroot/js/prototype.lite.js"></script>
	<script type="text/javascript" src="webroot/js/util.js"></script>
	<script type="text/javascript" src="webroot/js/sorttable.js"></script>
	<style tpe="text/css">
	#content{
		margin:16px 8px 8px 8px;
		border:1px solid #c0c0c0;
		padding:8px;
		background:#fff;
		color:#000;
	}
	</style>
	<?php
	$body_attr = 'style="background-image:url(\''.site_url('school/gradient/'.$this->session->userdata('school_id')).'/?\')"';
	?>
</head>
<body <?php echo $body_attr ?>>
<div id="content">
<h3 style="margin:0;"><?php echo $title ?></h3><br />
<?php echo $body ?>
</div>
</body>
</html>
