<?php

/**
 * Helper :: Khaos :: ACL :: Check
 *
 * @param mixed $aro
 * @param mixed $aco
 * @param mixed $axo
 * 
 * @return bool
 * @access public
 */
function kh_acl_check($aro, $aco, $axo = null)
{
    /*
     * Some general init stuff we need before
     * carrying out the check.
     */
    
    static $tables, $ci;
    
    if (!is_object($ci))
        $ci = &get_instance();
        
    $cache = ((defined('KH_CACHE') && is_object($ci->khcache))?true:false);

    /*
     * --- Start of main check process ---
     */
     
    // Try the cache first
        
    if ($cache)
    {
        $aco_axo = md5($aco.$axo);
            
        if (($aro_cache = $ci->khcache->fetch($key = $ci->khcache->generatekey('acl', $aro))) !== false)
        {
            if (isset($aro_cache[$aco_axo]))
                return $aro_cache[$aco_axo];
        }
        else 
            $aro_cache = array();
    }

    // No cache so we will need to determine the tables to be used
    
    if (!is_array($tables))
    {
        $options = $ci->config->item('acl', 'khaos');        
        
        $tables = array(
            'aros'           => 'khacl_aros',
            'acos'           => 'khacl_acos',
            'axos'           => 'khacl_axos',
            'access'         => 'khacl_access',
            'access_actions' => 'khacl_access_actions'
        );
            
        if (isset($options['tables']) && is_array($options['tables']))
            $tables = array_merge($tables, $options['tables']);
    }
    

    // Cache not available so lets query the database
            
    $rs = $ci->db->query('SELECT access.allow
                            FROM '.$ci->db->dbprefix.$tables['aros'].' AS aro_node, '.$ci->db->dbprefix.$tables['acos'].' AS aco_node
                              LEFT JOIN '.$ci->db->dbprefix.$tables['aros'].' AS aro_branch ON (aro_node.lft >= aro_branch.lft AND aro_node.lft <= aro_branch.rgt)
                              LEFT JOIN '.$ci->db->dbprefix.$tables['acos'].' AS aco_branch ON (aco_node.lft >= aco_branch.lft AND aco_node.lft <= aco_branch.rgt)
                              INNER JOIN '.$ci->db->dbprefix.$tables['access'].' AS access ON (aro_branch.id = access.aro_id AND aco_branch.id = access.aco_id)
                            WHERE aro_node.name = ? AND aco_node.name = ?
                            ORDER BY aro_branch.rgt ASC, aco_branch.rgt ASC
                            LIMIT 1', array($aro, $aco));
        
    if ($rs->num_rows() == 1)
    {
        $row = $rs->row();
        
        if ($row->allow == 'Y')
        {
            if ($axo !== null)
            {
                // AXO specified so lets determine if the aro has access to this axo
                
                $rs = $ci->db->query('SELECT access_actions.allow
                                        FROM '.$ci->db->dbprefix.$tables['aros'].' AS aro_node, '.$ci->db->dbprefix.$tables['acos'].' AS aco_node, '.$ci->db->dbprefix.$tables['axos'].' AS axo_node
                                          LEFT JOIN '.$ci->db->dbprefix.$tables['aros'].' AS aro_branch ON (aro_node.lft >= aro_branch.lft AND aro_node.lft <= aro_branch.rgt)
                                          LEFT JOIN '.$ci->db->dbprefix.$tables['acos'].' AS aco_branch ON (aco_node.lft >= aco_branch.lft AND aco_node.lft <= aco_branch.rgt)
                                          LEFT JOIN '.$ci->db->dbprefix.$tables['access'].' AS access ON (aro_branch.id = access.aro_id AND aco_branch.id = access.aco_id)
                                          INNER JOIN '.$ci->db->dbprefix.$tables['access_actions'].' AS access_actions ON (access.id = access_actions.access_id AND axo_node.id = access_actions.axo_id)
                                        WHERE aro_node.name = ? AND aco_node.name = ? AND axo_node.name = ?
                                        ORDER BY aro_branch.rgt ASC, aco_branch.rgt ASC
                                        LIMIT 1', array($aro, $aco, $axo));
                    
                if ($rs->num_rows() == 1)
                {
                    $row   = $rs->row();
                    $allow = (($row->allow == 'Y')?true:false);
                }
                else // No ((ARO->ACO)->AXO) link exists
                    $allow = false;
            }
            else // ARO -> ACO link is set to allow with no AXO specified
                $allow = true;
        } 
        else // ARO -> ACO link is set to deny
            $allow = false;
    }
    else // No results matching the specified ARO, ACO combination
        $allow = false;
        
    // If applicable cache the result before returning   

    if ($cache)
    {
        $aro_cache[$aco_axo] = $allow;
        $ci->khcache->store($key, $aro_cache);
    }
               
    return $allow; 
}

?>