<?php echo $this->session->flashdata('saved') ?>
<?php
$icondata[0] = array('departments/add', 'Add Department', 'add.gif' );
$this->load->view('partials/iconbar', $icondata);
?>
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-departments">
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="I">Icon</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="Description">Description</td>
		<td class="n" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if($departments){
	foreach( $departments as $department ){
	?>
	<tr class="tr<?php echo ($i & 1) ?>">
		<?php
		if( isset($department->icon) && $department->icon != '' && ! empty($department->icon)){
			list(,,,$img_wh) = @getimagesize('webroot/images/standardicons/'.$department->icon);
			$img_file = 'webroot/images/standardicons/'.$department->icon;
		} else {
			$img_wh = 'width="16" height="16"';
			$img_file = 'webroot/images/blank.gif';
		}
		?>
		<td width="50" align="center"><img src="<?php echo $img_file ?>" <?php echo $img_wh; ?>  alt=" " /></td>
		<td><?php echo $department->name ?></td>
		<td><?php echo $department->description ?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'departments/edit/'.$department->department_id;
			$actions['delete'] = 'departments/delete/'.$department->department_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No departments exist!</td>';
	}
	?>
	</tbody>
</table>

<?php echo $pagelinks ?>

<?php $this->load->view('partials/iconbar', $icondata) ?>
<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-departments';
$jsst['cols'] = array("Icon", "Name", "Description", "None");
$this->load->view('partials/js-sorttable', $jsst);
?>
