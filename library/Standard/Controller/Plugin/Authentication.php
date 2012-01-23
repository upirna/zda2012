<?php

/**
 * Application initialization plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 */
class Standard_Controller_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{

	/**
	 * Before dispatch loop
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
    	
    	/**
    	 * Helpers
    	 */
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    	$urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
    	
        
        /**
         * Login with posted session ID
         */
		if ($request->getParam('PHPSESSID', false)) {
			session_id($request->getParam('PHPSESSID'));
        } 
        
        Zend_Session::start();
        
        
    }
	
    
    /**
     * Post dispatch
     * 
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request) {
    	
    	if(is_array($request)) {
			throw new Exception('$request must be instance of Zend_Controller_Request_Abstract');
		}
    	
		
    	/**
    	 * Add dojo view helpers
    	 */
    	$layout = Zend_Layout::getMvcInstance();
    	$layout->getView()->addHelperPath(APPLICATION_PATH . '/default/views/helpers', 'Zend_View_Helper');
    	$layout->getView()->addHelperPath('Zend/Dojo/View/Helper', 'Zend_Dojo_View_Helper');
    	
    }
    
    
    
    
    
    
}

