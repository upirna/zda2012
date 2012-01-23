<?php

/**
 * Basic actions
 * 
 * This controller helps to build layout and
 * is also an entry point for whole application.
 * 
 * 
 * @package    Default
 * @copyright  Copyright (c) 2005-2010 Toff
 * @version    $Id: IndexController.php 531 2011-01-18 08:21:48Z uros.pirnat $
 */



class IndexController extends Standard_Controller_General  
{
	
	
	/**
	 * Entry point for whole application
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function indexAction() 
	{
		
	    /**
	     * Add dojo modules and global javascript variables
	     */
	    $this->view->dojo()->requireModule('application.page.Homepage');
	    $this->view->dojo()->javascriptCaptureStart();
	    echo 'var baseUrl = "' . $this->view->baseUrl() . '";' . "\n";
	    echo 'dojo.addOnLoad(function() { new application.page.Homepage(); });' . "\n";
	    $this->view->dojo()->javascriptCaptureEnd();		
	    
	    
		/**
		 * Render
		 */
	    $this->_helper->layout->setLayout('homepage');
		$this->render('index');
	}
	

	/**
	 * Sitemap
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function sitemapAction()
	{
        
		/**
		 * Disable layout and view script
		 */
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout(); 	
		
		echo $this->view->navigation()->sitemap()->setFormatOutput(true);
	}	
	
	
	/**
	 * Get content
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    public function getContentAction() {
    	
    	
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	$todash = new Zend_Filter_Word_CamelCaseToDash();
        $code = $this->_request->getParam('code');
        $code = strtolower($todash->filter($code));
        
        
        if(strlen($code) > 0) {
        	
	        $menuItem = MenuItem::getMenuItemByCode($code);
	        
	        
	        
	        
	        if($menuItem) { 
	        	
		    	$layout = Zend_Layout::getMvcInstance();
		    	$view = $layout->getView();
		    	$view->headTitle($menuItem['title'], 'SET');
				$view->headTitle()->setSeparator(' - ');
		    	
		    	/**
		    	 * Get content and assign it to the view
		    	 */ 
		    	$content = Content::getContentByMenuItemId($menuItem['id']);
		    	
		    	
		    	if(is_array($content)) {
			    	foreach ($content as $item) {
		    			$item['keywords'] = $item['keywords'] === null ? '' : $item['keywords'];
		    			
		    			
		    			$content = $item['content'];
		    			$content = $this->_appendGallery($content);

		    			$this->view->$item['position'] = $content;
		    			$this->view->headMeta()->appendName('keywords', $item['keywords']);
		    		}
		    	}
		    	
				$layout->setLayoutPath($menuItem['layout_path']);
				$layout->setLayout($menuItem['layout']);
				$this->render($menuItem['script']);
				return;
	        } 
        }
        
        
        /**
         * Page does not exist... Throw exception
         */
		throw new Zend_Controller_Dispatcher_Exception('Page with such code does not exist!');
        
    }
    
    
    /**
     * Append galleries
     *
     * @author Uros Pirnat
     * @param string $content
     * @return string
     */
    protected function _appendGallery($content)
    {
    	preg_match_all('/<img[^>]+>/i', $content, $result); 


    	$galleries = array();
    	if(is_array($result) && count($result[0] > 0)) {
    		
    		$this->view->dojo()->requireModule('dojox.image.Lightbox');
    		$this->view->dojo()->requireModule('dojo.parser');
			
    		
    		$css = $this->view->baseUrl() . '/scripts/dojo/library/';
    		$css .= $this->_config->dojo->version . '/dojox/image/resources/image.css';
    		$this->view->headLink()->appendStylesheet($css);
    		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/styles/gallery.css');
    		
    		foreach($result[0] as $item) {
    			if(strpos($item, 'toffGalleryItem')) {
    				
    				preg_match('/id="(.+)"/Ui', $item, $matches);
    				$xhtmlId = $matches[1];
    				list($prefix, $id) = explode('-', $xhtmlId, 2);
    				
    				if($prefix === 'album') {
	    				$view = new Zend_View();
	    				$view->gallery = Gallery::getAlbumById($id);
	    				$view->items = Gallery::getItemsByAlbumId($id);
						$view->addScriptPath(APPLICATION_PATH . '/default/views/scripts');
	    				$galleryXhtml = $view->render('gallery.phtml');
	    				
	    				$content = str_replace($item, $galleryXhtml, $content);	
    				}
    				

    				
    			}
    		}
    	}
    	
		  	
		return $content;
	}
	
}

