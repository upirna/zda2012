<?php 

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	/**
	 * Loading of models 
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function _initModelAutoloading()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->pushAutoloader(array('Standard_Loader_Autoloader', 'autoload'));
	}
	
	
    /**
     * Initilaize routes
     * 
     * @author Uros Pirnat
     * @return void
     */
	public function _initRouter() 
	{
		$front = Zend_Controller_Front::getInstance();
		$front->getRouter()->removeDefaultRoutes();
		
    	require_once 'Route.php';
    	$routes = Route::getRoutes();
	    if(is_array($routes)) {
	    	$routes = array_reverse($routes);
	    	foreach ($routes as $route) {
				$routerRoute  = new Zend_Controller_Router_Route($route['rule'], $route['default']);
				$front->getRouter()->addRoute($route['code'], $routerRoute);
			}
	    }
  
	}
	
	
	
}