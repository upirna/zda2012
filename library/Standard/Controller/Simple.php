<?php 

class Standard_Controller_Simple extends Standard_Controller_General  
{
	
	/**
	 * Assign params
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		

		$this->_assign();

		
        $this->setRequest($request)
             ->setResponse($response)
             ->_setInvokeArgs($invokeArgs);
        $this->_helper = new Zend_Controller_Action_HelperBroker($this);
        
        
        
		/**
		 * Disable layout and view script
		 */
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout(); 
		
	}
	
	
}