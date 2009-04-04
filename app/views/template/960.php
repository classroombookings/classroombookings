<?php $title = (!isset($title)) ? '' : $title; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Classroombookings | <?php echo $title ?></title>
	<base href="<?php echo $this->config->item('base_url').'web/'; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Craig Rodway" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="icon" type="image/x-icon" href="favicon2.ico" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/reset.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/text.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/960/960.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui960.css" />
	<style type="text/css">
	body{background:#F4F4F4;}
	#t{background:#468ED8;}
    #tabsB {
		background:#468ED8;
      float:left;
      width:100%;
      font-size:87%;
      line-height:normal;
      }
    #tabsB ul {
	margin:0;
	width:960px;
	margin:0px auto;
	padding:10px 10px 0 40px;
	list-style:none;
      }
    #tabsB li {
      display:inline;
      margin:0;
      padding:0;
      }
    #tabsB a {
      float:left;
      background:url("img/template/tableftB.gif") no-repeat left top;
	  background-position:0% -42px;
      margin:0;
      padding:0 0 0 4px;
      text-decoration:none;
      }
    #tabsB a span {
      float:left;
      display:block;
      background:url("img/template/tabrightB.gif") no-repeat right top;
	  background-position:100% -42px;
      padding:5px 15px 4px 6px;
      color:#666;
      }
    /* Commented Backslash Hack hides rule from IE5-Mac \*/
    #tabsB a span {float:none;}
    /* End IE5-Mac hack */
    #tabsB a:hover span {
      color:#000;
      }
    #tabsB a:hover, #tabsB a.active {
      background-position:0% 0px;
      }
    #tabsB a:hover span, #tabsB a.active span {
      background-position:100% 0px;
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
	<script type="text/javascript" src="js/syronex-colorpicker-mod.js"></script>
	<script type="text/javascript" src="js/jquery.date_input.min.js"></script>
	<script type="text/javascript" src="js/timepicker.js"></script>
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
</head>
<body>

	<div id="t">
		
		
		<div id="tl">
			Classroombookings
		</div>
		
		<div id="tr">
			Username | bookings | Logout
		</div>
		
		<div id="tabsB">
			<ul>
				<?php
				$link = '<li><a href="%s"><span>%s</span></a></li>';
				if($this->auth->check('dashboard', TRUE)){ echo sprintf($link, site_url('dashboard'), 'Dashboard'); }
				if($this->auth->check('bookings', TRUE)){ echo sprintf($link, site_url('bookings'), 'Bookings'); }
				if($this->auth->check('myprofile', TRUE)){ echo sprintf($link, site_url('account'), 'My Profile'); }
				if($this->auth->check('configure', TRUE)){ echo sprintf($link, site_url('configure'), 'Configure'); }
				if($this->auth->check('rooms', TRUE)){ echo sprintf($link, site_url('rooms'), 'Rooms'); }
				if($this->auth->check('academic', TRUE)){ echo sprintf($link, site_url('academic/main'), 'Academic setup'); }
				if($this->auth->check('departments', TRUE)){ echo sprintf($link, site_url('departments'), 'Departments'); }
				if($this->auth->check('reports', TRUE)){ echo sprintf($link, site_url('reports'), 'Reports'); }
				if($this->auth->check('users', TRUE)){ echo sprintf($link, site_url('security/users'), 'Security'); }
				if($this->auth->check('users', TRUE)){ echo '<li><a href="foo" class="active"><span>Foobar</span></a></li>'; }
				?>
			</ul>
			
		</div>
		
	</div>
	
	<div class="container_12">
		
		<div class="grid_12">
			&nbsp;
		</div>
		
		<div class="clear">&nbsp;</div>
		
		<div class="grid_12" style="background:#fff">
				<?php
				echo (isset($body)) ? $body : 'Nothing to display.';
				?>
		</div>
		<!-- end .grid_12 -->


</body>
</html>