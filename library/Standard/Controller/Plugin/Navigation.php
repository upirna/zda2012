<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';



/**
 * Application initialization plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 */
class Standard_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * View
	 * 
	 * @var unknown_type
	 */
	protected $_view;
	
	
	/**
	 * Request
	 * 
	 * @var object
	 */
	protected $_request;
	
	
	/**
	 * Webshop ID
	 * 
	 * @var integer
	 */
	protected $_webshopId;
	
	
	/**
	 * Language ID
	 * 
	 * @var integer
	 */
	protected $_languageId;

	
	/**
	 * User
	 * 
	 * @var object
	 */
	protected $_user;
	
	
    /**
     * Predispatch
     * 
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if($request->getModuleName() == 'default') {
    		if($request->getControllerName() == 'file') {
    			return;
    		}
    	}
    	

    	if (Zend_Auth::getInstance()->hasIdentity()) {
    		
    		/**
    		 * Set user
    		 */
    		$this->_user = Zend_Auth::getInstance()->getStorage()->read();
    		
	    		
	        /**
	         * Set language
	         */
	        $this->_languageId = $this->_request->getParam('languageId');
			$this->_languageId = Zend_Filter::filterStatic($this->_languageId, 'Digits');
			
			if(!$this->_languageId) {
				$this->_languageId = $this->_request->getHeader('Language-Id');  
			}
			
			if(!$this->_languageId) {
				if($this->_user) {
					$this->_languageId = $this->_user->languages_id; 
				}
			}
			
			$this->_request->setParam('languageId', $this->_languageId);
		
	        
    		
    		
	    	$layout = Zend_Layout::getMvcInstance();
	    	$this->_view = $layout->getView();
	    	$this->_request = $request;
	    	
	    	$this->_createNavigation();
    	}
    	
    }
    	

    
    

		
    
    
    
}

