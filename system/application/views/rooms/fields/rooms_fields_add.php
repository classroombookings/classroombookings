<?php
if( !isset($field_id) ){
	$field_id = @field($this->uri->segment(4, NULL), $this->validation->field_id, 'X');
}
$errorstr = $this->validation->error_string;

echo "<!-- $field_id -->";

if($field_id != 'X'){
	$this->load->view('msgbox/warning', 'Changing the type of field now may have adverse effects on existing rooms.');
}

echo form_open('rooms/fields/save', array('class' => 'cssform', 'id' => 'fields_add'), array('field_id' => $field_id) );
?>

<br />

<fieldset><legend accesskey="F" tabindex="1">Field Information</legend>

<p>
  <label for="name" class="required">Name</label>
  <?php
	$name = @field($this->validation->name, $field->name);
	echo form_input(array(
		'name' => 'name',
		'id' => 'name',
		'size' => '30',
		'maxlength' => '64',
		'tabindex' => '2',
		'value' => $name,
	));
	?>
</p>
<?php echo @field($this->validation->name_error) ?>


<p>
  <label for="type">Type</label>
  <?php
	$type = @field($this->validation->type, $field->type);
	/*$options['CHECKBOX']	= 'Checkbox (Yes/No)';
	$options['SELECT']		= 'Drop-down list';
	$options['TEXT']			= 'Text';*/
	echo form_dropdown('type', $options_list, $type, 'id="type" tabindex="3" onchange="$(\'fitems\').style.display = (this.value == \'SELECT\') ? \'block\':\'none\';"');
	?>
</p>
<?php echo @field($this->validation->type_error) ?>


<div id="fitems">
<p>
  <label for="items">Items</label>
  <?php
	$options = @field($this->validation->options, $field->options);
	$options_str = "";
	if($options){
		foreach($options as $option){
			$options_str .= $option->value . "\n";
		}
		$options_str = substr($options_str, 0, strlen($options_str)-1);
	}
	echo form_textarea(array(
		'name' => 'options',
		'id' => 'options',
		'rows' => '4',
		'cols' => '20',
		'tabindex' => '5',
		'value' => $options_str,
	));
	?><p class="hint">If you selected the <span>Drop-down list</span> option above, please enter the possible options above; one on each line.</p>
	<br />
	<?php if($field_id != 'X'){ ?><p class="hint"><span class="error">If you change drop-down list items now, you may have to <strong>re-assign</strong> appropriate values to rooms.</span></p><?php } ?>
</p>
<?php echo @field($this->validation->items_error) ?>
</div>
</fieldset>


<script type="text/javascript">
//$('fitems').style.display='none';
$('fitems').style.display = ($('type').value == 'SELECT') ? 'block':'none';
</script>


<?php
$submit['submit'] = array('Save', '6');
$submit['cancel'] = array('Cancel', '7', 'rooms/fields');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
