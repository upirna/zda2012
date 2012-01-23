<?php


/**
 * AbsBaseUrl helper
 *
 * @uses helper Zend_View_Helper
 */
class Zend_View_Helper_Image {
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * Absolute base url
	 */
	public function image($image, $module, $lang = null) {
		
		$defaultTheme = Zend_Registry::get('config')->defaultTheme;
		
		
		$front = Zend_Controller_Front::getInstance();
		$url = rtrim($front->getBaseUrl(), '/');
		
		if(is_null($lang)) {
			$url .= '/images/' . $defaultTheme . '/' . $module . '/' . $image;
		} else {
			$url .= '/images/' . $defaultTheme . '/' . $module . '/' . $lang . '-' . $image;
		}
		
		return $url;
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
