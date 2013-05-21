<?php echo $this->session->flashdata('saved') ?>
<?php
$icondata[0] = array('users/add', 'Add User', 'add.gif' );
$icondata[1] = array('users/import', 'Import Users', 'user_import.gif' );
$this->load->view('partials/iconbar', $icondata);
?>
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-users">
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Type">Type</td>
		<td class="h" title="Enabled">Enabled</td>
		<td class="h" title="Username">Username</td>
		<td class="h" title="Name">Display name</td>
		<td class="h" title="Lastlogin">Last login</td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if( $users ){
	foreach( $users as $user ){ ?>
	<tr class="tr<?php echo ($i & 1) ?>">
		<?php
		$img_type = ($this->userauth->CheckAuthLevel(ADMINISTRATOR, $user->authlevel)) ? 'user_administrator.gif' : 'user_teacher.gif';
		$img_enabled = ($user->enabled == 1) ? 'enabled.gif' : 'no.gif';
		?>
		<td width="50" align="center"><img src="webroot/images/ui/<?php echo $img_type ?>" width="16" height="16"  alt="<?php echo $img_type ?>" /></td>
		<td width="70" align="center"><img src="webroot/images/ui/<?php echo $img_enabled ?>" width="16" height="16"  alt="<?php echo $img_enabled ?>" /></td>
		<td><?php echo $user->username ?></td>
		<td><?php
		if( $user->displayname == '' ){ $user->displayname = $user->username; }
		echo $user->displayname;
		?></td>
		<td><?php
		if($user->lastlogin == '0000-00-00 00:00:00'){
			$lastlogin = 'Never';
		} else { 
			$lastlogin = date("d/m/Y, H:i", strtotime($user->lastlogin));
		}
		echo $lastlogin;
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'users/edit/'.$user->user_id;
			$actions['delete'] = 'users/delete/'.$user->user_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; } } ?>
	</tbody>
</table>

<?php echo $pagelinks ?>

<?php $this->load->view( 'partials/iconbar', $icondata ); ?>
<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-users';
$jsst['cols'] = array("Icon", "Name", "Location", "Teacher", "Notes", "Photo", "None");
$this->load->view('partials/js-sorttable', $jsst);
?>
