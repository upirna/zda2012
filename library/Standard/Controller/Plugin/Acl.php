<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';



/**
 * Application initialization plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 */
class Standard_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Acl
	 * 
	 * @var object
	 */
	public $_acl;
	
	
    /**
     * Post dispatch
     * 
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	return;
    	
    	if (Zend_Auth::getInstance()->hasIdentity()) {
	    	    	
	    	/**
	    	 * User ID
	    	 */
			$userId = Zend_Auth::getInstance()->getStorage()->read()->id;
			
	    	
	    	/**
	    	 * Get user's role
	    	 */
	    	$primaryRole = Role::getRoleIdByUserId($userId);
	    	Zend_Registry::set('roleId', (string) $primaryRole);
	    	
	    	try {
				$cache = new Zend_Cache_Core(array('automatic_serialization' => true));
				$backend = new Zend_Cache_Backend_Apc();
				$cache->setBackend($backend); 		
	    	} catch(Exception $e) {
	    		$cache = null;
	    	}
	
		
		
			if (!$cache or ($acl = $cache->load('zendAcl')) === false) {

				/**
		    	 * ACL
		    	 */
		    	$acl = new Zend_Acl();
				
				
				/**
				 * Add roles
				 */
				require_once APPLICATION_PATH . '/default/models/Role.php';
				$roles = Role::getAll();
				
				
				/*
				 * Sort by level and ord
				 */ 
				if(is_array($roles)){
					
					$level = array();
					foreach ($roles as $key => $row) {
					    $level[$key] = $row['level'];
					}
			
					array_multisort($level, SORT_ASC, $roles);	
				}
				
				
				if(is_array($roles)) {
					foreach($roles as $role) {
						$roleId = $role['role_id'];
						$parentId = $role['parent_id'];
						$roleObect = new Zend_Acl_Role($roleId);
						$acl->addRole($roleObect, $parentId);
					}
				}
				
				
				/**
				 * Add resources
				 */
				require_once APPLICATION_PATH . '/default/models/Resource.php';
				$resources = Resource::getAll();
				
				foreach($resources as $resource) {
					$resourceCode = $resource['resource_code'];
					$resourceObject = new Zend_Acl_Resource($resourceCode);
					$acl->add($resourceObject);
				}
				
				
				
				/**
				 * Add rules
				 */
				require_once APPLICATION_PATH . '/default/models/Rule.php';
				$rules = Rule::getAll();
				
				foreach($rules as $rule) {
					$permission = $rule['permission'];
					$roleId = $rule['role_id'];
					$resourceCode = $rule['resource_code'];
					$privilege = $rule['privilege'];
					$class = $rule['assert_class'];
					
					$assertObject = null;
					if($class) {
						$assertObject = new $class();
					}
					
					
					switch($permission) {
						case 'allow':
							$acl->allow($roleId, $resourceCode, $privilege, $assertObject);
							break;
						
						case 'deny':
							$acl->deny($roleId, $resourceCode, $privilege, $assertObject);
							break;
					}
					
				}
					
				
				
				if($cache) {
					$cache->save($acl, 'zendAcl');
				}
				
	    	}
	    	
			
			$this->_acl = $acl;
			Zend_Registry::set('Zend_Acl', $acl);
			
    	}
    }
}

