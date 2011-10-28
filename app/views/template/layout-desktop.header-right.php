<ul class="horiz">
		
<?php

if(!$this->auth->logged_in())
{

	echo '<li>' . anchor('account/login', lang('LOGIN'), 'class="i security"') . '</li>';
	
}
else
{

	echo '<li>' . anchor('account', $this->session->userdata('display'), 'class="i account"') . '</li>';
	echo '<li>' . anchor('account/logout', lang('LOGOUT'), 'class="i security"') . '</li>';

}

?>

</ul>