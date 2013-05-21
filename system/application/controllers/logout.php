<?php
class Logout extends Controller {


  function Logout(){
		parent::Controller();
	}
	
	
	function index(){
		$this->userauth->logout();
		#$layout['title'] = 'Logout';
		#$layout['body'] = '<h2>Logged out</h2>You have successfully logged out of Classroom Bookings.' . anchor('site/home','Home');
		#$this->load->view('layout', $layout);
		redirect('login', 'location');
	}
	
	
}
?>