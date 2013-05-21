<?php echo $this->session->flashdata('saved') ?>
<?php
$icondata[0] = array('weeks/add', 'Add Week', 'add.gif');
//$icondata[1] = array('weeks/dates', 'Week dates', 'school_manage_weeks.gif');
$icondata[1] = array('weeks/academicyear', 'Academic Year', 'school_manage_weeks_academicyear.gif');
$this->load->view('partials/iconbar', $icondata);
?>
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-weeks">
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="I">Icon</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="Colour">Colour</td>
		<td class="n" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if($weeks){
	foreach($weeks as $week){
	?>
	<tr class="tr<?php echo ($i & 1) ?>">
		<?php
		if( isset($week->icon) && $week->icon != '0'){
			list(,,,$img_wh) = @getimagesize('webroot/images/standardicons/'.$week->icon);
			$img_file = 'webroot/images/standardicons/'.$week->icon;
		} else {
			$img_wh = 'width="16" height="16"';
			$img_file = 'webroot/images/blank.png';
		}
		?>
		<td width="50" align="center"><img src="<?php echo $img_file ?>" <?php echo $img_wh; ?>  alt=" " /></td>
		<td><?php echo $week->name ?></td>
		<td>
		<?php
		echo sprintf('<span style="padding:2px;background:#%s;color:#%s">%s</span>', $week->bgcol, $week->fgcol, $week->name);
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'weeks/edit/'.$week->week_id;
			$actions['delete'] = 'weeks/delete/'.$week->week_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No weeks defined!</td>';
	}
	?>
	</tbody>
</table>
<?php $this->load->view('partials/iconbar', $icondata) ?>
<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-weeks';
$jsst['cols'] = array("Icon", "Name", "Colour", "None");
$this->load->view('partials/js-sorttable', $jsst);
?>
