<?php
class Help extends Controller {
  
  
  
  
  function Help(){
		parent::Controller();
		// Load language
  	$this->lang->load('crbs', 'english');
    
		// Get school id
    $this->school_id = $this->session->userdata('school_id');

		// Check user is logged in & is admin
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'location');
		} else {
			$this->loggedin = True;
		}
	}
	
	
	
	
	function index(){
		$section = $this->uri->segment(2);
		if( $section == 'help' ){
			// If user is already somewhere in help and clicks help (URL would be /help/help/<page>)
			// !! Change this to the help contents page when it exists
			redirect('help/contents', 'location');
		}
		$str = $this->uri->uri_string();
		$pos = strpos( $str, '/', 1 );
		$page = substr( $str, $pos+1, strlen($str)-$pos );
		$layout['title'] = 'Help';	// on: '.$section;
		$layout['showtitle'] = $layout['title'];	// . ' ('.$section.')';

		// Breadcrumbs
		$total_segs = $this->uri->total_segments();
		$segs = $this->uri->segment_array();
		$start_segs = 2;
		$breadcrumbs = '<p class="breadcrumbs" style="font-size:90%;">Go there now: <a href="'.site_url().'">'.$this->session->userdata('schoolname').'</a>';
		$uri = '';
		for($i=$start_segs;$i<$total_segs+1;$i++){
			$uri .= $this->uri->segment($i).'/';
			$breadcrumbs .= ' &gt; <a href="'.site_url($uri).'">' . $this->uri->segment($i) . '</a> ';
		}
		$breadcrumbs .= '</p>';

		// Check if help file exists
		$file = 'system/application/views'.$str.'.php';
		if(file_exists($file)){
			$layout['body'] = Markdown($this->load->view($str, NULL, True)) . $breadcrumbs;	//'<strong>&#187; <a href="'.site_url($page).'">Go there now</a></strong>';
		} else {
			$layout['showtitle'] = 'Oops!';
			$layout['body'] = 'Sorry, the help file for this page ('.$file.') cannot be found or does not exist yet.'.$breadcrumbs;
		}
		$this->load->view('layout', $layout );
	}





	function contents(){
		$layout['title'] = 'Help Contents';	// on: '.$section;
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = Markdown('Help contents');
		$this->load->view('layout', $layout );
	}





}
?>
