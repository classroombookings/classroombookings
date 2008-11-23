<?php
function mysqlhuman($d, $f = "d/m/Y, H:i", $n = 'Never'){
	if($d == '0000-00-00 00:00:00'){
		$r = $n;
	} else { 
		$r = date($f, strtotime($d));
	}
	return $r;
}
