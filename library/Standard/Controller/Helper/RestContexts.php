<?php

class Standard_Controller_Helper_RestContexts extends Zend_Controller_Action_Helper_Abstract {
    
	
	/**
	 * Supported contexts
	 * 
	 * @var array
	 */
	protected $_contexts = array(
        'xml',
        'json',
    );

    
    /**
	 * Do it
	 *
	 * @return void
     */
    public function preDispatch()
    {
        $controller = $this->getActionController();
        if (!$controller instanceof Zend_Rest_Controller) {
            return;
        }

        $this->_initContexts();

        /**
         * Set a Vary response header based on the Accept header
         */ 
        $this->getResponse()->setHeader('Vary', 'Accept');
    }

    
    /**
     * Contexts
     * 
     * @return void
     */
    protected function _initContexts() {

    	$cs = $this->getActionController()->gethelper('contextSwitch');
        $cs->setAutoJsonSerialization(false);
        
        if(is_array($this->_contexts)) {
        	foreach ($this->_contexts as $context) {
	            foreach (array('index', 'post', 'get', 'put', 'delete') as $action) {
	                $cs->addActionContext($action, $context);
	            }
	        }	
        }


        $cs->initContext();
    }
}

