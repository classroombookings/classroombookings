<ul class="horiz">
		
<?php

if(!$this->auth->logged_in())
{

	echo '<li>' . anchor('account/login', lang('LOGIN'), 'class=" security"') . '</li>';
	
}
else
{

	echo '<li>' . anchor('account/logout', lang('LOGOUT'), 'class=" security"') . '</li>';
	echo '<li><strong>' . anchor('account', $this->session->userdata('display'), 'class=" account"') . '</strong></li>';
	

}

?>

</ul>