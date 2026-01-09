<?php

class Dashboard extends MY_Controller
{
	public function index()
	{
		redirect('setup/rooms/groups');
	}
}
