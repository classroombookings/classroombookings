<?php echo $this->session->flashdata('saved') ?>
<?php
$icondata[0] = array('rooms/fields/add', 'Add Field', 'add.gif' );
$icondata[1] = array('rooms', 'Rooms', 'school_manage_rooms.gif' );
$this->load->view('partials/iconbar', $icondata);
?>
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-roomfields">
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Name</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="Options">Options</td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if( $fields ){
	foreach( $fields as $field ){ ?>
	<tr class="tr<?php echo ($i & 1) ?>">
		<td><?php echo $field->name ?></td>
		<td><?php echo $options_list[$field->type] ?></td>
		<td><?php
		if(isset($field->options)){
			$options_str = "";
			foreach($field->options as $option){
				$options_str .= $option->value . ", ";
			}
			$options_str = substr($options_str, 0, strlen($options_str)-2);
			echo $options_str;
		}
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'rooms/fields/edit/'.$field->field_id;
			$actions['delete'] = 'rooms/fields/delete/'.$field->field_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No room fields exist!</td>';
	}
	?>
	</tbody>
</table>
<?php $this->load->view( 'partials/iconbar', $icondata ); ?>
<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-roomfields';
$jsst['cols'] = array("Name", "Type", "Options", "None");
$this->load->view('partials/js-sorttable', $jsst);
?>
