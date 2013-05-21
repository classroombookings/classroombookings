<?php
if( !isset($stage) ){
	$stage = @field($this->uri->segment(3, NULL), $this->validation->stage, '1');
}
$errorstr = $this->validation->error_string;

echo form_open_multipart('users/import', array('class' => 'cssform', 'id' => 'user_import'), $post );

$t = 1;
?>


<?php $this->load->view('users/import/buttons', array('stage' => $stage, 'stage_config' => $stage_config, 't' => $t)) ?>

</form>
