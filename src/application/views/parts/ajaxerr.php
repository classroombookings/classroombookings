<div id="ajaxerr">
<?php echo $this->msg->err($error); ?>
</div>

<script type="text/javascript">
$('#ajaxerr').hide();
$('#alert').html($('#ajaxerr').html());
</script>