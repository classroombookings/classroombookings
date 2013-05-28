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

<link rel="shortcut icon" href="favicon.ico">

<script>
var CRBS = {}, Q = [];
CRBS.base_url = "<?php echo config_item('base_url') ?>";
CRBS.site_url = "<?php echo site_url() ?>/";
CRBS.tt_view = "<?php echo config_item('timetable_view') ?>";
</script>

</head>
<body>

	<header class="wrapper header">
		
		<div class="row">
		
			<div class="grid_7 header-left">
				<ul class="nav primary">
					<li class="logo"><a href="<?php echo site_url() ?>" ?><img src="img/template/crbs-logo1.png"></a></li>
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
			
			<div class="grid_5 header-right">
				<ul class="nav primary">
				<?php
				if ( ! $this->auth->is_logged_in())
				{
					echo '<li>' . anchor('account/login', lang('login'), 'class=" security"') . '</li>';
				}
				else
				{
					echo '<li class="name">' . anchor('account', lang('logged_in_as') . ' <strong>' . $this->session->userdata('u_display') . '</strong>', 'class=" account"') . '</li>';
					echo '<li>' . anchor('account/logout', lang('logout'), 'class=" security"') . '</li>';
				}
				?>
				</ul>
			</div>
			
		</div>
		
	</header>
	
	
	
	
	<?php
	$bread = $this->layout->get_breadcrumb();
	$bread_markup = array();
	?>
	
	<?php if ($bread): ?>
	
	<section class="wrapper sub">
		
		<div class="row">
			
			<div class="grid_7">
				
				<div class="breadcrumb">
					<?php
					foreach ($this->layout->get_breadcrumb() as $bc)
					{
						if (count($bc) === 1)
						{
							$bread_markup[] = '<span>' . $bc[0] . '</span>';
						}
						else
						{
							$bread_markup[] = '<a href="' . $bc[1] . '">' . $bc[0] . '</a>';
						}
					}
					
					echo implode('<span class="separator">/</span>', $bread_markup);
					?>
				</div> <!-- / .breadcrumb -->
				
				<div class="nav-items">
					<?php if (isset($subnav)) $this->load->view('parts/nav') ?>
				</div> <!-- / .nav-items -->
				
			</div> <!-- / .grid_7 -->
			
			<div class="grid_5 flash-container">
				<?php echo $this->layout->get('flash') ?>
			</div> <!-- / .grid_5 -->
			
		</div> <!-- / .row -->
		
	</section> <!-- / .wrapper.sub -->
		
	<?php endif; ?>
	
	
	
	
	
	<!-- page body -->
	<main class="wrapper body">
		<div class="row">
			<?php echo $this->layout->get('content') ?>
		</div>
	</main>
	<!-- end page body -->
	
	
	
	
	<footer class="wrapper footer">
		<div class="row">
			<div class="grid_6"> 
				<p><a href="http://classroombookings.com/" target="_blank">Classroombookings</a> is released under the Open Software License v3.0.</p>
				<p>&copy; 2006 &mdash; <?php echo date('Y') ?> Craig A Rodway.</p>
			</div>
			<div class="grid_6 text-right">
				<?php echo anchor(option('school_url'), option('school_name'), 'target="_blank"') ?>
			</div>
		</div>
	</footer>
	
	
	<!-- Javascript template for delete dialogs -->
	<script id="ich_delete" type="text/html">
	<h3 class="sub-heading"><?php echo lang('delete') ?> {{name}}</h3>
	<p class="text">{{prompt}}</p>
	<form action="{{url}}" method="post" accept-charset="utf-8" id="delete_form">
		<input type="hidden" name="id" value="{{id}}" />
		<input type="hidden" name="redirect" value="{{redirect}}" />
		<input type="hidden" name="crbscsrftoken" value="{{csrf}}" />
		<div style="margin: 30px 0 15px 0; bottom: 0; position: absolute;">
			<button type="submit" class="red button delete"><span><?php echo lang('delete') ?></span></button>
			<a href="<?php echo current_url() ?>" class="grey button close-dialog"><?php echo lang('cancel') ?></a>
		</div>
	</form>
	</script>
	
	
	<div id="delete_dialog" class="hidden"></div>
	
	
<?php echo $this->layout->get_js() ?>


<script>
if (typeof(window['Q']) !== "undefined") {
	for (var i = 0, len = Q.length; i < len; i++) {
		Q[i]();
	}
}
</script>


</body>
</html>