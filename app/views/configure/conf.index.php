


<?php
$foo = validation_errors();
if($foo){
	echo $this->msg->err('<ul>' . $foo . '</ul>', 'Configuration values could not be validated.');
}
?>


<div id="tabs-configure">

	<ul style="height:30px;"> 
		<li><a href="#conf-main"><span>Main settings</span></a></li>
		<li><a href="#conf-logo"><span>School logo</span></a></li>
		<li><a href="#conf-ldap"><span>LDAP authentication</span></a></li>
	</ul>
    
	<div id="conf-main"> 
        <?php $this->load->view('configure/conf.main.php'); ?>
    </div> 
	
    <div id="conf-logo"> 
		<?php $this->load->view('configure/conf.logo.php'); ?>
	</div>
	
    <div id="conf-ldap"> 
		<?php $this->load->view('configure/conf.ldap.php'); ?>
	</div>
	
</div>


</form>


<script type="text/javascript">
$("#tabs-configure > ul").tabs();
</script>