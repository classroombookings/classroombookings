<?php

$title = (isset($title)) ? $title : 'Delete';
$confirm = (isset($confirm)) ? $confirm : 'Are you sure you want to delete this item?'; 

#echo '<a href="'.site_url($url).'" title="'.$title.'" onclick="if(!confirm(\''.$confirm.'\')){return false;}" />'.$title.'</a>';
echo '<a href="'.site_url($url).'" title="'.$title.'" />'.$title.'</a>';

?>