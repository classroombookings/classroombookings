<?php
$icondata[0] = array('security/users', 'Manage users', 'user_orange.gif' );
$icondata[1] = array('security/groups', 'Manage groups', 'group.gif' );
#$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
$this->load->view('parts/iconbar', $icondata);
?>


<p>Listed below are the groups that exist within Classroombookings. In each tab, it is possible to configure what users belonging to that group are allowed and not allowed to do. The permissions for the Guests and Administrators groups can not be changed.</p>


<div id="tabs-permissions">

	<ul style="height:30px;"> 
		<li><a href="#conf-grp-01"><span>Group 1</span></a></li>
		<li><a href="#conf-grp-02"><span>Group 2</span></a></li>
		<li><a href="#conf-grp-03"><span>Group 3</span></a></li>
	</ul>
    
	<div id="conf-grp-01"> 
        <?php #$this->load->view('configure/conf.main.php'); ?>
		group 1 permission options
    </div> 
	
    <div id="conf-grp-02"> 
		<?php #$this->load->view('configure/conf.logo.php'); ?>
		group 1 permission options
	</div>
	
    <div id="conf-grp-03"> 
		<?php #$this->load->view('configure/conf.ldap.php'); ?>
		group 1 permission options
	</div>
	
</div>


<script type="text/javascript">
$("#tabs-permissions > ul").tabs();
</script>