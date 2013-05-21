<?php echo $this->session->flashdata('saved') ?>
<?php
$icondata[0] = array('holidays/add', 'Add Holiday', 'add.gif' );
$this->load->view('partials/iconbar', $icondata);
?>
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-holidays">
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Name</td>
		<td class="h" title="StartDate">Start Date</td>
		<td class="h" title="EndDate">End Date</td>
		<td class="h" title="Duration">Duration</td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if($holidays){
	foreach( $holidays as $holiday ){
	?>
	<tr class="tr<?php echo ($i & 1) ?>">
		<td><?php echo $holiday->name ?></td>
		<td><?php echo date("d/m/Y", strtotime($holiday->date_start)); ?></td>
		<td><?php echo date("d/m/Y", strtotime($holiday->date_end)) ?></td>
		<td><?php
		if(strtotime($holiday->date_start) != strtotime($holiday->date_end)){
			echo timespan(strtotime($holiday->date_start), strtotime($holiday->date_end));
		} else {
			echo "1 Day";
		}
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'holidays/edit/'.$holiday->holiday_id;
			$actions['delete'] = 'holidays/delete/'.$holiday->holiday_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No holidays defined!</td>';
	}
	?>
	</tbody>
</table>
<?php $this->load->view( 'partials/iconbar', $icondata ); ?>
<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-holidays';
$jsst['cols'] = array("Name", "StartDate", "EndDate", "None");
$this->load->view('partials/js-sorttable', $jsst);
?>
