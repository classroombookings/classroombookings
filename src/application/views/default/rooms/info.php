<?php
$col1 = '200';
$col2 = '200';

$tablestyle = 'width: 100%;';
?>

<?php if(!IS_XHR): ?><h1><?php echo $room->name ?></h1><?php endif; ?>

<?php
if(!empty($room->photo)){
	$filename = str_replace('#', 'sm', $room->photo);
	$path = "upload/$filename";
	$tablestyle = 'float: left; clear:none; margin-left:20px; width: 300px;';
	?>
	<div>
	<img src="<?php echo $path ?>" alt="" style="float: left; clear: none; display: inline; " />
	<?php
} else {
	?>
<div style="width:400px;">
<?php } ?>

<table class="list" cellpadding="5" style="<?php echo $tablestyle ?>">
	<?php if(!empty($room->description)): ?>
	<tr>
		<td class="t" width="<?php echo $col1 ?>">Description</td>
		<td width="<?php echo $col2 ?>"><?php echo $room->description ?></td>
	</tr>
	<?php endif; ?>
	<?php if($room->category_id > 0): ?>
	<tr>
		<td class="t" width="<?php echo $col1 ?>">Category</td>
		<td width="<?php echo $col2 ?>"><?php echo $room->cat_name ?></td>
	</tr>
	<?php endif; ?>
	<?php if($room->user_id > 0): ?>
	<tr>
		<td class="t" width="<?php echo $col1 ?>">Owner</td>
		<td width="<?php echo $col2 ?>"><?php echo $room->owner_name ?></td>
	</tr>
	<?php endif; ?>
	<?php foreach($room->attrs as $attr): ?>
	<?php if($attr->type != 'check' && empty($attr->value)){ continue; } ?>
	<tr>
		<td class="t" width="<?php echo $col1 ?>"><?php echo $attr->name ?></td>
		<td valign="middle" width="<?php echo $col2 ?>"><?php
			if($attr->type != 'check'){
				echo $attr->value;
			} else {
				$str = '<img src="img/ico/%s" width="16" height="16" alt="%s" title="%s"></span>';
				switch($attr->value){
					case 1:
						$img = sprintf($str, 'f_yes.gif', 'Yes', 'Yes');
						break;
					default:
						$img = sprintf($str, 'f_err.gif', 'No', 'No');
						break;
				}
				echo $img;
			}
		?></td>
	</tr>
	<?php endforeach; ?>
</table>


<div class="clear"></div>


</div>
