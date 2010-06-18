<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Khaos :: Khacl
 * 
 * @author      David Cole <neophyte@sourcetutor.com>
 * @version     0.1-alpha5
 * @copyright   2008
 */

define('KH_ACL', true);
define('KH_ACL_VERSION', 0.1);

/**
 * KhACL
 * 
 */
class Khacl
{
    /**
     * Access Request Object
     *
     * @var object
     * @access public
     */
    var $aro;
    
    /**
     * Access Control Pbject
     *
     * @var object
     * @access public
     */
    var $aco;
    
    /**
     * Access Extension Object
     *
     * @var object
     * @access public
     */
    var $axo;
    
    /**
     * Codeigniter Super Object
     *
     * @var object
     * @access private
     */
    var $_CI;
    
    /**
     * Enable Cache?
     *
     * @var bool
     * @access private
     */
    var $_Cache = false;
    
    /**
     * KhACL Tables
     *
     * @var array
     * @access private
     */
    var $_Tables = array('aros'           => 'khacl_aros',
                         'acos'           => 'khacl_acos',
                         'axos'           => 'khacl_axos',
                         'access'         => 'khacl_access',
                         'access_actions' => 'khacl_access_actions');
    
    /**
     * Constructor
     *
     * @return Khacl
     */
    function Khacl()
    {
        $this->_CI =& get_instance();
        $this->_CI->config->load('khaos', true, true);        
        
        // Is 'Khaos :: Cache' available ?
        if (defined('KH_CACHE') && (KH_CACHE_VERSION >= 0.3) && is_object($this->_CI->khcache))
            $this->_Cache = true;

        // Grab ACL options
        $options = $this->_CI->config->item('acl', 'khaos');        

        if (isset($options['tables']) && is_array($options['tables']))
            $this->_Tables = array_merge($this->_Tables, $options['tables']);
        
        // Instantiate the ARO, ACO and AXO objects
        $this->aro = new KH_ACL_ARO($this->_CI, $this->_Tables, $this->_Cache);
        $this->aco = new KH_ACL_ACO($this->_CI, $this->_Tables, $this->_Cache);
        $this->axo = new KH_ACL_AXO($this->_CI, $this->_Tables, $this->_Cache); 
    }
    
    /**
     * Check Access
     *
     * @param mixed $aro
     * @param mixed $aco
     * @param mixed $axo
     * 
     * @return bool
     */
    function check($aro, $aco, $axo = null)
    {
        if (!function_exists('kh_acl_check'))
            $this->_CI->load->helper('khacl');
        
        return kh_acl_check($aro, $aco, $axo);
    }
    
    /**
     * Allow Access
     *
     * Grants the ARO access to the AXO on the ACO, if no AXO is specified
     * then the user is simply granted access to the ACO.
     * 
     * @param mixed $aro
     * @param mixed $aco
     * @param mixed $axo
     * 
     * @return bool
     * @access public
     */
    function allow($aro, $aco, $axo = null)
    {
        return $this->_set($aro, $aco, $axo, true);
    }
    
    /**
     * Deny Access
     *
     * Denies the ARO access to AXO on the ACO, if no AXO is specified
     * then the ARO is outright denied access to the ACO.
     * 
     * @param mixed $aro
     * @param mixed $aco
     * @param mixed $axo
     * 
     * @return bool
     * @access public
     */
    function deny($aro, $aco, $axo = null)
    {
        return $this->_set($aro, $aco, $axo, false);
    }
    
    /**
     * Set Permissions
     *
     * @param mixed $aro
     * @param mixed $aco
     * @param mixed $axo
     * @param mixed $allow
     * 
     * @return bool
     * @access private
     */
    function _set($aro, $aco, $axo = null, $allow = true)
    {
        $allow = ($allow)?'Y':'N';
        
        // delete ARO cache
        if ($this->_Cache)
            $this->_CI->khcache->delete($this->_CI->khcache->generatekey('acl', $aro));
        
        // Grab the id of the ARO
        if (!($rs = $this->_CI->db->get_where($this->_Tables['aros'], array('name' => $aro), 1)))
            return false;
        
        if ($rs->num_rows() == 1)
        {
            $row    = $rs->row();
            $aro_id = $row->id;
        }
        else 
            return false;

        
        // Grab the id of the ACO
        if (!($rs = $this->_CI->db->get_where($this->_Tables['acos'], array('name' => $aco), 1)))
            return false;        
        
        if ($rs->num_rows() == 1)
        {
            $row    = $rs->row();
            $aco_id = $row->id;
        }
        else 
            return false;    
        
        // Grab the id of the AXO
        if ($axo !== null)
        {
            if (!($rs = $this->_CI->db->get_where($this->_Tables['axos'], array('name' => $axo), 1)))
                return false;    
            
            if ($rs->num_rows() == 1)
            {
                $row    = $rs->row();
                $axo_id = $row->id;
            }
            else 
                return false;    
        }

        /*
         * If needed create/modify the ARO -> ACO map in the access table
         */
        
        if (($rs = $this->_CI->db->get_where($this->_Tables['access'], array('aro_id' => $aro_id, 'aco_id' => $aco_id))) !== false)
        {
            if ($rs->num_rows() === 0) // Create new link
            {
                if ($axo === null) // No AXO so set the ARO -> ACO access to whatever is set by $allow
                {
                    if (!$this->_CI->db->insert($this->_Tables['access'], array('aro_id' => $aro_id, 'aco_id' => $aco_id, 'allow' => $allow)))
                        return false;
                }
                else // AXO set so make the ARO -> ACO access to allowed as the ALLOW/DENY will be determined by the AXO later on
                {
                    if (!$this->_CI->db->insert($this->_Tables['access'], array('aro_id' => $aro_id, 'aco_id' => $aco_id, 'allow' => 'Y')))
                        return false;                    
                }
                    
                $access_id = $this->_CI->db->insert_id();
            }
            else // Modify existing link if needed
            {
                $row       = $rs->row();
                $access_id = $row->id;
                
                if ($axo === null) // No AXO so update the ARO -> ACO access to whatever is specified by $allow
                {
                    if ($row->allow != $allow)
                        if (!$this->_CI->db->update($this->_Tables['access'], array('allow' => $allow), array('id' => $access_id)))
                            return false;
                }
                else // AXO specified so we set the ARO -> ACO access to allowed as the ALLOW/DENY willbe determined by the AXO later on
                {
                    if (!$this->_CI->db->update($this->_Tables['access'], array('allow' => 'Y'), array('id' => $access_id)))
                        return false;                    
                }
            }
        }
        else 
            return false;
        
        /*
         * If needed create/modify the access -> action link in the access_actions table
         */
        
        if ($axo !== null)
        {
            
            if (($rs = $this->_CI->db->get_where($this->_Tables['access_actions'], array('access_id' => $access_id, 'axo_id' => $axo_id))) !== false)
            {
                if ($rs->num_rows() === 0) // create link
                {
                    if (!$this->_CI->db->insert($this->_Tables['access_actions'], array('access_id' => $access_id, 'axo_id' => $axo_id, 'allow' => $allow)))
                        return false;
                }
                else // Modify existing link 
                {
                    $row = $rs->row();
                    
                    if ($row->allow != $allow)
                        if (!$this->_CI->db->update($this->_Tables['access_actions'], array('allow' => $allow), array('id' => $row->id)))
                            return false;
                }
                
                return true;
            }
            else 
                return false;
        }
        else 
            return true;
    }    
}

/**
 * ARO List
 *
 */
class KH_ACL_ARO
{
    /**
     * KhACL Tables
     *
     * @var array
     * @access private
     */
    var $_Tables = array();
    
    /**
     * Codeigniter super object
     *
     * @var object
     * @access private
     */
    var $_CI;
    
    /**
     * Cache Available ?
     *
     * @var bool
     * @access private
     */
    var $_Cache = false;
    
    /**
     * Constructor
     *
     * @param object $ci
     * @param array  $config
     * @param bool   $cache
     * 
     * @return KH_ACL_ARO
     */
    function KH_ACL_ARO(&$ci, $tables, $cache)
    {        
        $this->_CI     = &$ci;
        $this->_Tables = $tables;
        $this->_Cache  = $cache;
    }
    
    /**
     * Create ARO
     *
     * @param string $aro
     * @param string $parent
     * @param int    $link
     * 
     * @return bool
     * @access public
     */
    function create($aro, $parent = null, $link = null)
    {
        /*
         * Ensure there is no other ARO by this name in the
         * database.
         */
        
        $rs = $this->_CI->db->get_where($this->_Tables['aros'], array('name' => $aro), 1);
   
        if ($rs->num_rows() === 0)
        {
            $link = is_numeric($link)?$link:'NULL';
            
            if ($parent === null)
            {
                /*
                 * If no parent is set then we can add the ARO
                 * to the end of the tree so as few records as possible
                 * are updated.
                 */
                
                // Get the right most value of the tree
                $this->_CI->db->order_by('rgt', 'desc');
                $rs = $this->_CI->db->get($this->_Tables['aros'], 1);
                
                if ($rs->num_rows() === 0) // Tree is empty
                   $right = 0;
                else 
                {
                   $row   = $rs->row();
                   $right = $row->rgt;
                }
                
                // Insert the record
                return $this->_CI->db->insert($this->_Tables['aros'], array('lft' => ($right + 1), 'rgt' => ($right + 2), 'name' => $aro, 'link' => $link));
            }
            else 
            {
                /*
                 * Parent is specified so we have to update all records
                 * which are futher down the tree than the parent.
                 */
                
                // Grab the left value of the specified parent
                $rs = $this->_CI->db->get_where($this->_Tables['aros'], array('name' => $parent), 1);
                
                if ($rs->num_rows() === 0) // We cant do much if we cant find the parent
                    return false;
                else 
                {
                    $row  = $rs->row();
                    $left = $row->lft;
                }
                
                // Update all records past the left point by 2 to make room for the new ARO
                $this->_CI->db->trans_start();
                
                $this->_CI->db->set('rgt', 'rgt + 2', false);
                $this->_CI->db->where('rgt >', $left);
                $this->_CI->db->update($this->_Tables['aros']);
                        
                $this->_CI->db->set('lft', 'lft + 2', false);
                $this->_CI->db->where('lft >', $left);
                $this->_CI->db->update($this->_Tables['aros']);
                
                // Insert the record
                $this->_CI->db->insert($this->_Tables['aros'], array('lft' => ($left + 1), 'rgt' => ($left + 2), 'name' => $aro, 'link' => $link));              
                $this->_CI->db->trans_complete();
                
                return $this->_CI->db->trans_status();
            }
            
            return true;
        }
        else 
           return false;
    }
    
    /**
     * Delete ARO
     *
     * @param string $aro
     * 
     * @return bool
     * @access public
     */
    function delete($aro)
    {        
        // Grab the ARO branch details
        if (!($rs = $this->_CI->db->get_where($this->_Tables['aros'], array('name' => $aro), 1)))
            return false;
            
        if ($rs->num_rows() === 0)
            return false;

        // delete ARO cache
        if ($this->_Cache)
            $this->_CI->khcache->delete($this->_CI->khcache->generatekey('acl', $aro));
            
        /*
         * Delete the ARO
         */
        
        $row   = $rs->row();
        $left  = $row->lft;
        $right = $row->rgt;
        $width = ($right - $left) + 1;
        
        $this->_CI->db->trans_start();
        $this->_CI->db->query('DELETE '.$this->_CI->db->dbprefix.$this->_Tables['aros'].',
                                      '.$this->_CI->db->dbprefix.$this->_Tables['access'].',
                                      '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].'
                                 FROM '.$this->_CI->db->dbprefix.$this->_Tables['aros'].'
                                   LEFT JOIN '.$this->_CI->db->dbprefix.$this->_Tables['access'].' ON '.$this->_CI->db->dbprefix.$this->_Tables['aros'].'.id = '.$this->_CI->db->dbprefix.$this->_Tables['access'].'.aro_id
                                   LEFT JOIN '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].' ON '.$this->_CI->db->dbprefix.$this->_Tables['access'].'.id = '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].'.access_id
                                 WHERE '.$this->_CI->db->dbprefix.$this->_Tables['aros'].'.lft BETWEEN '.$left.' AND '.$right);
        
        $this->_CI->db->set('rgt', 'rgt - '.$width, false);
        $this->_CI->db->where('rgt >', $right);
        $this->_CI->db->update($this->_Tables['aros']);
        
        $this->_CI->db->set('lft', 'lft - '.$width, false);
        $this->_CI->db->where('lft >', $right);
        $this->_CI->db->update($this->_Tables['aros']);
        $this->_CI->db->trans_complete();
        
        return $this->_CI->db->trans_status();
    }
}

/**
 * ACO List
 *
 */
class KH_ACL_ACO
{
    /**
     * KhACL Tables
     *
     * @var array
     * @access private
     */
    var $_Tables = array();
    
    /**
     * Codeigniter super object
     *
     * @var object
     * @access private
     */
    var $_CI;
    
    /**
     * Cache Available ?
     *
     * @var bool
     * @access private
     */
    var $_Cache = false;
    
    /**
     * Constructor
     *
     * @param object $ci
     * @param array  $config
     * @param bool   $cache
     * 
     * @return KH_ACL_ACO
     */
    function KH_ACL_ACO(&$ci, $tables, $cache)
    {
        $this->_CI     = &$ci;
        $this->_Tables = $tables;
        $this->_Cache  = $cache;
    }
    
    /**
     * Create ACO
     *
     * @param string $aco
     * @param string $parent
     * @param int    $link
     * 
     * @return bool
     * @access public
     */
    function create($aco, $parent = null, $link = null)
    {
        /*
         * Ensure there is no other ARO by this name in the
         * database.
         */
        
        $rs = $this->_CI->db->get_where($this->_Tables['acos'], array('name' => $aco), 1);
        
        if ($rs->num_rows() === 0)
        {
            $link = is_numeric($link)?$link:'NULL';
            
            if ($parent === null)
            {
                /*
                 * If no parent is set then we can add the ARO
                 * to the end of the tree so as few records as possible
                 * are updated.
                 */
                
                // Get the right most value of the tree
                $this->_CI->db->order_by('rgt', 'desc');
                $rs = $this->_CI->db->get($this->_Tables['acos'], 1);
                
                if ($rs->num_rows() === 0) // Tree is empty
                   $right = 0;
                else 
                {
                   $row   = $rs->row();
                   $right = $row->rgt;
                }
                
                // Insert the record
                return $this->_CI->db->insert($this->_Tables['acos'], array('lft' => ($right + 1), 'rgt' => ($right + 2), 'name' => $aco, 'link' => $link));
            }
            else 
            {
                /*
                 * Parent is specified so we have to update all records
                 * which are futher down the tree than the parent.
                 */
                
                // Grab the left value of the specified parent
                $rs = $this->_CI->db->get_where($this->_Tables['acos'], array('name' => $parent), 1);
                
                if ($rs->num_rows() === 0) // We cant do much if we cant find the parent
                    return false;
                else 
                {
                    $row  = $rs->row();
                    $left = $row->lft;
                }
                
                // Update all records past the left point by 2 to make room for the new ARO
                $this->_CI->db->trans_start();
                
                $this->_CI->db->set('rgt', 'rgt + 2', false);
                $this->_CI->db->where('rgt >', $left);
                $this->_CI->db->update($this->_Tables['acos']);
                        
                $this->_CI->db->set('lft', 'lft + 2', false);
                $this->_CI->db->where('lft >', $left);
                $this->_CI->db->update($this->_Tables['acos']);
                
                // Insert the record
                $this->_CI->db->insert($this->_Tables['acos'], array('lft' => ($left + 1), 'rgt' => ($left + 2), 'name' => $aco, 'link' => $link));
                $this->_CI->db->trans_complete();
                
                return $this->_CI->db->trans_status();
            }
            
            return true;
        }
        else 
           return false;
    }     
    
    /**
     * Delete ACO
     *
     * @param string $aro
     * 
     * @return bool
     * @access public
     */
    function delete($aco)
    {
        // Grab the ACO branch details
        if (!($rs = $this->_CI->db->get_where($this->_Tables['acos'], array('name' => $aco), 1)))
            return false;
            
        if ($rs->num_rows() === 0)
            return false;

        // delete cache
        if ($this->_Cache)
            $this->_CI->khcache->delete_all();    
            
        /*
         * Delete the ACO
         */
        
        $row   = $rs->row();
        $left  = $row->lft;
        $right = $row->rgt;
        $width = ($right - $left) + 1;

        $this->_CI->db->trans_start();
        $this->_CI->db->query('DELETE '.$this->_CI->db->dbprefix.$this->_Tables['acos'].',
                                      '.$this->_CI->db->dbprefix.$this->_Tables['access'].',
                                      '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].'
                                 FROM '.$this->_CI->db->dbprefix.$this->_Tables['acos'].'
                                   LEFT JOIN '.$this->_CI->db->dbprefix.$this->_Tables['access'].' ON '.$this->_CI->db->dbprefix.$this->_Tables['acos'].'.id = '.$this->_CI->db->dbprefix.$this->_Tables['access'].'.aco_id
                                   LEFT JOIN '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].' ON '.$this->_CI->db->dbprefix.$this->_Tables['access'].'.id = '.$this->_CI->db->dbprefix.$this->_Tables['access_actions'].'.access_id                                       
                                 WHERE '.$this->_CI->db->dbprefix.$this->_Tables['acos'].'.lft BETWEEN '.$left.' AND '.$right);

        $this->_CI->db->set('rgt', 'rgt - '.$width, false);
        $this->_CI->db->where('rgt >', $right);
        $this->_CI->db->update($this->_Tables['acos']);
        
        $this->_CI->db->set('lft', 'lft - '.$width, false);
        $this->_CI->db->where('lft >', $right);
        $this->_CI->db->update($this->_Tables['acos']);
        $this->_CI->db->trans_complete();
        
        return $this->_CI->db->trans_status();
    }    
}

/**
 * AXO List
 *
 */
class KH_ACL_AXO
{
    /**
     * ARO Table
     *
     * @var array
     * @access private
     */
    var $_Tables = array();
    
    /**
     * Codeigniter super object
     *
     * @var object
     * @access private
     */
    var $_CI;
    
    /**
     * Cache Available ?
     *
     * @var bool
     * @access private
     */
    var $_Cache = false;
    
    /**
     * Constructor
     *
     * @param object $ci
     * @param array  $config
     * @param bool   $cache
     * 
     * @return KH_ACL_AXO
     */
    function KH_ACL_AXO(&$ci, $tables, $cache)
    {
        $this->_CI     = &$ci;
        $this->_Tables = $tables;
        $this->_Cache  = $cache;
    }
    
    /**
     * Create AXO
     *
     * @param string $axo
     * 
     * @return bool
     * @access public
     */
    function create($axo)
    {
        /*
         * Ensure there is no other AXO
         * in the database by this name
         */
        
        $rs = $this->_CI->db->get_where($this->_Tables['axos'], array('name' => $axo));
        
        if ($rs->num_rows() === 0)
        {
            // Create new AXO
            return $this->_CI->db->insert($this->_Tables['axos'], array('name' => $axo));
        }
        else 
           return false;        
    }
    
    /**
     * Delete AXO
     *
     * @param string $axo
     * 
     * @return bool
     * @access public
     */
    function delete($axo)
    {
        
        // grab the axo_id so we can delete the access -> action links later on
        if (!($rs = $this->_CI->db->get_where($this->_Tables['axos'], array('name' => $axo), 1)))
            return false;
        
        if ($rs->num_rows() === 0)
            return false;
        else 
        {
            $row    = $rs->row();
            $axo_id = $row->id;
        }

        // clear cache
        if ($this->_Cache)
            $this->_CI->khcache->delete_all();
            
        // delete all relvent records
        $this->_CI->db->trans_start();
        $this->_CI->db->delete($this->_Tables['access_actions'], array('axo_id' => $axo_id));
        $this->_CI->db->delete($this->_Tables['axos'], array('id' => $axo_id));
        $this->_CI->db->trans_complete();
        
        return $this->_CI->db->trans_status();
    }
}

?>