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
	
	<link rel="shortcut icon" type="image/x-icon" href="favicon3.ico" />
	
	<style type="text/css"><?php
	$weeks = $this->weeks_model->get();
	if(!empty($weeks)){
		$this->load->view('template/_weekcss', array('weeks' => $weeks));
	}
	?></style>
	
	<script type="text/javascript">
	var baseurl = "<?php echo $this->config->item('base_url') . 'assets/' ?>";
	var siteurl = "<?php echo site_url() ?>/";
	var tt_view = "<?php echo $this->settings->get('timetable_view') ?>";
	var _jsQ = [];
	</script>

	
	<script type="text/javascript" src="js/LAB.min.js"></script>

</head>
<body>


<div id="header">
	
	<div class="header-left">
		<?php echo $header_left ?>
	</div>
	
	<div class="header-right">
		<?php echo $header_right ?>
	</div>
	
</div>


<?php
$submenu_html = '';
if (!empty($submenu))
{
	$submenu_html .= '<div class="submenu">';
	$submenu_html .= $this->load->view('configure/sidebar', array(
		'menu' => $submenu,
		'ulclass' => 'horiz subnav'
	), true);
	$submenu_html .= '</div>'."\n\n";
}
$body = $submenu_html . $body;
?>


<?php if(empty($sidebar)): ?>
	
	<br class="clear">
	<div class="container_skel container">
		
		<?php $alert = (isset($alert)) ? $alert : $this->session->flashdata('flash') ?>
		<?php if (!empty($alert)): ?>
		<div class="row remove-bottom" style="padding-top: 20px;">
			<?php echo $alert ?>
		</div>
		<?php else: ?>
			<br class="clear">
		<?php endif; ?>
		
		<!-- page body -->
		<?php echo $body ?>
		<!-- end page body -->
	
	</div> <!-- / .container_skel .container -->
	

<?php else: ?>
	
	
	<div class="colmask leftmenu"> 
	
		<div class="colright">
	
			<div class="col1wrap">
				<div class="col1 container">
					
					<?php $alert = (isset($alert)) ? $alert : $this->session->flashdata('flash') ?>
					<?php if (!empty($alert)): ?>
					<div class="add-bottom"><?php echo $alert ?></div>
					<?php endif; ?>
					
					<!-- start page body -->
					<?php echo $body ?>
					<!-- end page body -->
					
				</div> <!-- / .col1 .container -->
			</div> <!-- / .col1wrap -->
			
			<div class="col2" id="sidebar">
				<!-- start sidebar -->
				<?php echo $sidebar ?>
				<!-- end sidebar -->
			</div> 
			<!-- / .col2 #sidebar -->
			
		</div> <!-- / .colright -->
		
	</div> <!-- / .colmask .leftmenu -->
	
	
<?php endif; ?>


<div id="footer"> 
	<p><a href="http://classroombookings.com/" target="_blank">Classroombookings</a> is released under the Affero GNU General Public License v3.<br>
	&copy; 2006 &mdash; <?php echo date('Y') ?> Craig A Rodway.</p>
</div>


<script type="text/javascript">
/* Initialise tabs */
_jsQ.push(function(){
	var tabs = $('ul.tabs');
	tabs.each(function(i){
		//Get all tabs
		var tab = $(this).find('> li > a');
		tab.click(function(e) {
			//Get Location of tab's content
			var contentLocation = "#" + $(this).data('tab');
			//Let go if not a hashed one
			if(contentLocation.charAt(0)=="#") {
				e.preventDefault();
				//Make Tab Active
				tab.removeClass('active');
				$(this).addClass('active');
				//Show Tab Content & add active class
				$(contentLocation).show().addClass('active').siblings().hide().removeClass('active');
			}
		});
	});
});
</script>


<!-- JS script loading -->
<script type="text/javascript">
var extras = [];
<?php
if (isset($js) && is_array($js))
{
	foreach($js as $j)
	{
		echo 'extras.push("' . $j . '?");';
		echo "\n";
	}
}
?>
var $loader = $LAB
	.setOptions({
		BasePath: baseurl,
		AlwaysPreserveOrder: true,
		UsePreloading: false,
		UseLocalXHR: false,
		UseCachePreload: false})
	.script("js/jquery-1.6.2.min.js")
	.script("js/syronex-colorpicker-mod.js")
	.script("js/jquery.tools.min.js")
	.script(extras).wait(function(){
		if (typeof(window['_jsQ']) != "undefined") {
			for (var i=0, len =_jsQ.length; i<len; i++) {
				$loader = $loader.wait(_jsQ[i]);
			}
		}
	});
</script>


</body>
</html>