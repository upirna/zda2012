<?php 


class Standard_Application_Resource_Db extends Zend_Application_Resource_Db
{

    /**
     * Retrieve initialized DB connection
     *
     * @return null|Zend_Db_Adapter_Interface
     */
    public function getDbAdapter()
    {
        if ((null === $this->_db)
            && (null !== ($adapter = $this->getAdapter()))
        ) {
        	
        	if($adapter == 'PDO_MYSQL') {
        		$this->_db = new Standard_Db_Adapter_Mysql($this->getParams()); 
        	} else {
        		$this->_db = Zend_Db::factory($adapter, $this->getParams());
        	}
        	
            
        }
        return $this->_db;
    }
	
	
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Db_Adapter_Abstract|null
     */
    public function init()
    {
    	
    	
		if (null !== ($db = $this->getDbAdapter())) {
			if ($this->isDefaultTableAdapter()) {
				Zend_Db_Table::setDefaultAdapter($db);
			}

			
			
			$config = $db->getConfig();
			
			$cfg = array(
				'host' => $config['host'],
				'username' => $config['username'],
				'password' => $config['password'],
				'dbname' => $config['dbname'],
			);
			
			
			$altDb = Zend_Db::factory('Mysqli', $cfg);
			Zend_Registry::getInstance()->set('altDb', $altDb);
			
			
			Zend_Registry::set('db', $db);
            
           
            return $db;
        }
    }
	
	
}