<?php


class Standard_Acl_Assert_CleanIp implements Zend_Acl_Assert_Interface {


	public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
	{
		return $this->_isCleanIP($_SERVER['REMOTE_ADDR']);
	}
       
          
	/**
	  * Check if IP is OK
      * @param $ip
      * @return boolan
    */      
	protected function _isCleanIP($ip) {

		if($ip == '127.0.0.1') {
			return true;
		}
		
		return false;
          	
	}
	
	
}