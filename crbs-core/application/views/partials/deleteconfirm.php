<?php echo form_open( $action, '', array( 'id' => $id ) ); ?>

<p class="msgbox question"><?= lang('app.delete.confirm') ?></p>
<?php if( isset($text) ){ ?><p class="msgbox exclamation"><?php echo $text ?></p><?php } ?>
<br /><br />
<table cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" valign="middle">
			<?php echo form_submit( array( 'value' => lang('app.action.delete') ) ) ?> &nbsp;&nbsp;&nbsp; <?php echo anchor( $cancel, lang('app.action.cancel')) ?>
		</td>
	</tr>
</table>

<?php echo form_close() ?>
