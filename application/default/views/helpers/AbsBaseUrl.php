<?php


/**
 * AbsBaseUrl helper
 *
 * @uses helper Zend_View_Helper
 */
class Zend_View_Helper_AbsBaseUrl {
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * Absolute base url
	 */
	public function absBaseUrl() {
		
		$server = Zend_Controller_Front::getInstance()->getRequest()->getServer();

		$protocol = explode('/',$server['SERVER_PROTOCOL']);
		$protocol = array_shift($protocol);
		
		$absUrl = strtolower($protocol . '://' . $server['HTTP_HOST']);
		
		$front = Zend_Controller_Front::getInstance();
		$url = rtrim($front->getBaseUrl(), '/');
		
		return $absUrl . $url;
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
