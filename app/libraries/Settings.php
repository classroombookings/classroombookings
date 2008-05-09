<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Settings{


	var $CI;


	function Settings(){
		// Load original CI object
		$this->CI =& get_instance();
	}
	
	
	
	
	function getldap(){
		$result1 = array();
		$result2 = array();
		
		$sql = 'SELECT * FROM settings-ldap LIMIT 1';
		$query1 = $this->CI->db->query($sql);
		
		if($query1->num_rows() == 1){
			$result1 = $query1->result_array();
		}

		$sql = 'SELECT * FROM settings-ldap-rdns';
		$query2 = $this->CI->db->query($sql);
		
		if($query2->num_rows() > 0){
			$result2 = $query2->result_array();
		}
			
		$result['ldap'] = $result1;
		$result['rnds'] = $result2;
		
	}

}
?>