<?php echo form_open( $action, '', array( 'id' => $id ) ); ?>
<br/>
<p class="msgbox question">Are you sure you want to delete this item?</p>
<?php if( isset($text) ){ ?><p class="msgbox exclamation"><?php echo $text ?></p><?php } ?>
<br /><br />
<table cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" valign="middle">
			<?php echo form_submit( array( 'value' => 'Yes' ) ) ?> &nbsp;&nbsp;&nbsp; <?php echo anchor( $cancel, 'No') ?>
		</td>
	</tr>
</table>
<?php echo form_close() ?>
