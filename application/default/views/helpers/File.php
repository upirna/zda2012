<?php


/**
 * File helper
 *
 * @uses helper Zend_View_Helper
 */
class Zend_View_Helper_File {
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * Absolute base url
	 */
	public function file($file, $path, array $options = array()) {
		
		$defaultParams = array(
			'module' => 'default',
			'controller' => 'file',
			'action' => 'file',
			'path' => $path,
			'file' => $file
		);
		
		$params = array_merge($defaultParams, $options);
		
		
		$this->view->url($params, 'default', true);
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
