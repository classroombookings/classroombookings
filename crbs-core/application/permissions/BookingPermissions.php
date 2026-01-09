<?php

namespace app\permissions;

use Permission;

defined('BASEPATH') OR exit('No direct script access allowed');

class BookingPermissions
{

	private $CI;

	private $_cache = [];

	public function __construct()
	{
		$this->CI =& get_instance();
        $this->CI->load->model([
        	'auth_model',
        	'roles_model',
        ]);
	}

    public function book_single(?object $user, mixed $room_id = null)
    {
    	$use_cache = false;
    	if (isset($user->user_id) && !is_null($room_id)) {
    		$use_cache = true;
    		$cache_key = sprintf('book_single_u%d_r%d', $user->user_id, $room_id);
    		if (isset($this->_cache[$cache_key])) {
    			return $this->_cache[$cache_key];
    		}
    	}

    	$names = [
			Permission::BK_SGL_VIEW_OTHER_NOTES,
			Permission::BK_SGL_VIEW_OTHER_USERS,
			Permission::BK_SGL_CREATE,
			Permission::BK_SGL_EDIT_OTHER,
			Permission::BK_SGL_CANCEL_OTHER,
			Permission::BK_SGL_SET_USER,
			Permission::BK_SGL_SET_DEPT,
    	];

        $allowed = $this->get_allowed_permissions($names, $user, $room_id);
        if ($use_cache) {
        	$this->_cache[$cache_key] = $allowed;
        }

        return $allowed;
    }


    public function book_recur(?object $user, mixed $room_id = null)
    {
        $use_cache = false;
    	if (isset($user->user_id) && !is_null($room_id)) {
    		$use_cache = true;
    		$cache_key = sprintf('book_recur_u%d_r%d', $user->user_id, $room_id);
    		if (isset($this->_cache[$cache_key])) {
    			return $this->_cache[$cache_key];
    		}
    	}

    	$names = [
			Permission::BK_RECUR_VIEW_OTHER_NOTES,
			Permission::BK_RECUR_VIEW_OTHER_USERS,
			Permission::BK_RECUR_CREATE,
			Permission::BK_RECUR_EDIT_OTHER,
			Permission::BK_RECUR_CANCEL_OTHER,
			Permission::BK_RECUR_SET_USER,
			Permission::BK_RECUR_SET_DEPT,
    	];

        $allowed = $this->get_allowed_permissions($names, $user, $room_id);
        if ($use_cache) {
        	$this->_cache[$cache_key] = $allowed;
        }

        return $allowed;
    }


    private function get_allowed_permissions($names, $user, $room_id)
    {
    	$allowed = [];
    	// if (empty($user->role_id)) return $allowed;

    	// Check if the user's role has these permissions
    	//
    	if ( ! empty($user->role_id)) {
        	$permission_list = $this->CI->roles_model->get_permission_names($user->role_id);
	    	$mapped = array_map(function($permission) use ($names) {
	    		if (in_array($permission, $names)) {
					[,$action] = explode('.', $permission);
					return $action;
	    		}
	    		return false;
			}, $permission_list);

			$allowed = array_merge($allowed, array_filter($mapped));
    	}

        // Get matching ACL permissions defined at room or group level for the user.
        // Room ID /should/ always be supplied, but check anyway.
        //
        if ( ! empty($user) && ! empty($room_id)) {
	        $acl_result = $this->CI->auth_model->user_room_permissions($user->user_id, $room_id);
	        $mapped = array_map(function($acl_row) use ($names) {
	        	$perm = $acl_row['permission'];
	        	if (in_array($perm, $names)) {
	        		[,$action] = explode('.', $perm);
	        		return $action;
	        	}
	        	return false;
	        }, $acl_result);
	        $allowed = array_merge($allowed, array_filter($mapped));
        }

        return $allowed;
    }



}
