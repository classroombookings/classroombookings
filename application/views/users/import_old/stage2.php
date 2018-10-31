<?php
if( !isset($stage) ){
	$stage = @field($this->uri->segment(3, NULL), $this->validation->stage, '1');
}
$errorstr = $this->validation->error_string;

echo form_open_multipart('users/import', array('class' => 'cssform', 'id' => 'user_import'), $post );

$t = 1;


#print_r($post);

$fields['ignore']				= '(Ignore)';
$fields['--']						= '--';
$fields['username']			= 'Username';
$fields['firstname'] 		= 'First name';
$fields['lastname'] 		= 'Last name';
$fields['displayname']	= 'Display name';
$fields['email'] 				= 'Email';
$fields['ext'] 					= 'Extension';
$fields['department']		= 'Department';
$fields['enabled']			= 'Enabled';
$fields['authlevel']		= 'Type';

$total_cols = count($csvdata[1]);
$total_rows = count($csvdata);
$cols = @field($post['csvcols']);
if($cols == 1){
	$total_rows--;
	$csv_headings = $csvdata[1];
}
?>



<fieldset style="margin:16px 0px;"><legend accesskey="C" tabindex="<?php echo $t ?>">Columns</legend>

<div style="width:auto;overflow:auto;height:auto;">
<table cellspacing="0" cellpadding="4" width="<?php echo 200*$total_cols ?>">
<tr>
<td>&nbsp;</td>
<?php
for($i=0;$i<$total_cols;$i++){
	#if($post['csvcols'] == 1){ $csvheadings
	echo '<td width="200" style="border-bottom:1px solid #ff6400;">';
	echo form_dropdown(
		'col_'.$i,
		$fields,
		'',
		' id="col'.$i.'"'
	);
	echo '</td>';
}
?>
</tr>
<?php
$row = 0;
for($r=1;$r<$total_rows;$r++){
	echo '<tr class="tr'.($row & 1).'">';
	echo '<td><input type="checkbox" name="'.$r.'" id="'.$r.'" value="'.$r.'" checked="checked" /></td>';
	for($c=0;$c<$total_cols;$c++){
		echo '<td style="border-right:1px solid #ccc">';
		echo '<label for="'.$r.'" class="ni">'.character_limiter($csvdata[$r][$c], 20).'</label>';
		echo '</td>';
	}
	echo '</tr>';
	$row++;
}
?>
</tr>
</table>
</div>


</fieldset>


<fieldset><legend>Auto-username</legend>

To automatically generate usernames for the imported users, type the format you'd like in the box below.

<p>
	<label for="autouser">Auto-generate username</label>
	<?php echo form_input(array(
		'name' => 'autouser',
		'id' => 'autouser',
		'size' => '40',
		'maxlength' => '40',
		'value' => '%f.%l',
	));
	?>
</p>

<p class="hint">
	<strong>%f</strong> First name<br />
	<strong>%l</strong> Last name<br />
	<strong>%e</strong> Email address<br />
	<strong>%x</strong> Extension<br />
	<strong>%n</strong> Display name<br />
	<strong>%d</strong> Department<br />
	<strong>%t</strong> Type (Teacher or Administrator)
</p>


</fieldset>


<?php $this->load->view('users/import/buttons', array('stage' => $stage, 'stage_config' => $stage_config, 't' => $t)) ?>


</form>
