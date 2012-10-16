<?php
// URI segments
$seg1 = $this->uri->segment(1, 'home');
$seg2 = $this->uri->segment(2);

// Can user change working academic year?
$changeyear = $this->auth->check('changeyear', true);

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<base href="<?php echo $this->config->item('base_url') . 'assets/' ?>">
<title><?php echo $this->layout->get_title('full') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php echo $this->layout->get_css() ?>
<?php
/*
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/skeleton.css">
<link rel="stylesheet" href="css/layout-fluid.css">
<link rel="stylesheet" href="css/layout.css">
<link rel="stylesheet" href="3rdparty/syronex-colorpicker/syronex-colorpicker.css">
<link rel="stylesheet" href="css/calendar.css">
*/
?>

<script>
var CRBS = {}, Q = [];
CRBS.base_url = "<?php echo config_item('base_url') ?>";
CRBS.site_url = "<?php echo site_url() ?>/";
CRBS.tt_view = "<?php echo config_item('timetable_view') ?>";
</script>

</head>
<body>


<div id="header">
	
	<div class="header-left">
		<ul class="horiz">
			<li class="name"><?php echo anchor(option('school_url'), option('school_name'), 'target="_blank"') ?></li>
			<?php
			foreach ($nav['primary'] as $item => $data)
			{
				$href = $item;
				$text = $data['label'];
				$class = $data['class'];
				echo '<li>' . anchor(site_url($href), $text, 'class=" ' . $class . '"') . '</li>';
			}
			?>
		</ul>
	</div>
	
	<div class="header-right">
		<ul class="horiz">
		<?php
		if ( ! $this->auth->logged_in())
		{
			echo '<li>' . anchor('account/login', lang('LOGIN'), 'class=" security"') . '</li>';
		}
		else
		{
			echo '<li>' . anchor('account/logout', lang('LOGOUT'), 'class=" security"') . '</li>';
			echo '<li><strong>' . anchor('account', $this->session->userdata('display'), 'class=" account"') . '</strong></li>';
		}
		?>
		</ul>
	</div>
	
</div>


<?php
if ($this->layout->has('sidebar'))
{
	$this->load->view('template/default/content_width_sidebar');
}
else
{
	$this->load->view('template/default/content_no_sidebar');
}
?>


<?php
/* $submenu_html = '';
if (!empty($submenu))
{
	$submenu_html .= '<div class="submenu">';
	$submenu_html .= $this->load->view('configure/sidebar', array(
		'menu' => $submenu,
		'ulclass' => 'horiz subnav'
	), true);
	$submenu_html .= '</div>'."\n\n";
}
$body = $submenu_html . $body; */
?>




<div id="footer"> 
	<p><a href="http://classroombookings.com/" target="_blank">Classroombookings</a> is released under the Open Software License v3.0.<br>
	&copy; 2006 &mdash; <?php echo date('Y') ?> Craig A Rodway.</p>
</div>


<?php echo $this->layout->get_js() ?>
<script>
$(document).ready(function() {
	if (typeof(window['Q']) != "undefined") {
		for (var i = 0, len = Q.length; i < len; i++) {
			Q[i];
		}
	}
});
</script>


</body>
</html>