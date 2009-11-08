<?php
$tablink = '<li><a href="%s">%s</a></li>';
$cookiestr = '';
if(isset($cookie)){
	$cookiestr = "{cookie:{expires:7,name:'$cookie'}}";
}
?>


<!-- #tabs -->
<div id="tabs">


	<ul>
		<?php
		foreach($tabs as $tab){
			echo sprintf($tablink, current_url() . "#" . $tab[0], $tab[1]);
		}
		?>
	</ul>  

	<?php
	foreach($tabs as $tab){
		echo '<div id="'.$tab[0].'">';
		echo $tab[2];
		echo '</div>';
	}
	?>


</div>
<!-- // #tabs -->

<script type="text/javascript">
$(function() {
	$("#tabs").tabs(<?php echo $cookiestr; ?>);
});
</script>