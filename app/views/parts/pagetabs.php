<?php
$tablink = '<li class="tab"><a href="%s">%s</a></li>';
?>

<ul id="pagetabs" class="subsection_tabs">
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