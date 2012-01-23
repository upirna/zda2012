<?php



class ErrorController extends Zend_Controller_Action
{
	
	/**
	 * Initialize
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function init()
	{
		/**
		 * Add CSS
		 */
		$baseUrl = $this->getFrontController()->getBaseUrl();
		$cssLink = $baseUrl . '/styles/default.css';
		$this->view->headLink()->appendStylesheet($cssLink);
	}
	
	
	/**
	 * Handle application errors and errors in the controller 
	 * chain arising from missing controllers
	 * classes and/or action methods 
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    public function errorAction() 
    {
    	
    	/**
    	 * Get error handler and clear body
    	 */
        $error = $this->_getParam('error_handler');
        $this->getResponse()->clearBody();
        
        
        
        switch ($error->type) {
			
        	/**
        	 * 404 error -- controller or action not found  
        	 */
        	case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            	
            	
            	$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
            	$this->view->error = $error;
                $this->render('404');
                break;
                
            /**
             * Application error; display error page, but don't change
             * status code
             */
            default:
            	
            	/**
            	 * Log error
            	 */
				//$log = Zend_Registry::get('Zend_Log');
            	//$log->info($errors->exception, Zend_Log::ALERT);  
                
				
				/**
				 * Render
				 */
            	$this->view->error = $error;
                $this->render('error');
                break;
        }
        
      
    }
}
