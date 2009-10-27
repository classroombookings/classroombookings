<?php
$tablewidth = "100%";

$col1 = '200';
$col2 = '200';
?>

<h1><?php echo $room->name ?></h1>

<?php
if(!empty($room->photo)){
	$filename = str_replace('#', 'sm', $room->photo);
	$path = "upload/$filename";
	$tablewidth = '300';
?>
<img src="<?php echo $path ?>" alt="" style="float:left;clear:none;" />
<?php } ?>

<table class="list" cellpadding="5" style="float:right;clear:none;display:block;">
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
				$str = '<span class="ui-icon %s"></span>';
				switch($attr->value){
					case 1:
						$img = sprintf($str, 'ui-icon-check');
						break;
					default:
						$img = sprintf($str, 'ui-icon-close');
						break;
				}
				echo $img;
			}
		?></td>
	</tr>
	<?php endforeach; ?>
</table>
