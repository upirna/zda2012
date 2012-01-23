<?php

class Standard_Controller_General extends Zend_Controller_Action  
{
	
	/**
	 * Root
	 * 
	 * @var string
	 */
	protected $_root;
	
	
	/**
	 * Role ID
	 * 
	 * @var unknown_type
	 */
	protected $_roleId;
	
	
	/**
	 * Acl
	 * 
	 * @var object
	 */
	protected $_acl;
	
	
	/**
	 * User
	 * 
	 * @var object
	 */
	protected $_user;
	
	
	/**
	 * Config
	 * 
	 * @var object
	 */
	protected $_config;
	
	
	/**
	 * Default currency
	 * 
	 * @var string
	 */
	protected $_defaultCurrency;
	
	
    /**
     * FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;	
	
	
    /**
     * Translator
     * 
     * @var object
     */
    protected $_translator;
    
    
    /**
     * Cache
     * 
     * @var Zend_Cache
     */
    protected $_cache;
    
    
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
     * Account ID
     * 
     * @var integer|null
     */
    protected $_accountId;
    
    
    /**
     * Webshop name
     * 
     * @var string
     */
    protected $_webshopName;
    
    
    /**
     * Counter type
     * 
     * @var string
     */ 
    protected $_counterType = 'default';
    
    
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
		 * Flash messenger
		 */
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');    
        
        $this->init();
		
	    $this->_createNavigation();
		$container = Zend_Registry::get('Zend_Navigation');
		$this->view->navigation($container);
			

        $menu = $this->view->navigation()->menu();
		$menu->setPartial(array('left-menu.phtml','default'));
		$leftMenu = $menu->render();
		$this->view->leftMenu = $leftMenu;		
		
		
		$menu->setPartial(array('top-menu.phtml','default'));
		$modules = $menu->render();
		$this->view->topMenu = $modules; 
		
		$menu->setPartial(array('submenu.phtml','default'));
		$modules = $menu->render();
		$this->view->subMenu = $modules;		
		
		
		$menu->setPartial(array('bottom-menu.phtml','default'));
		$bottomMenu = $menu->render();
		$this->view->bottomMenu = $bottomMenu;
		
		
        
	    if (Zend_Auth::getInstance()->hasIdentity()) {
	        $this->view->username = Zend_Auth::getInstance()->getStorage()->read()->username;
        }     
        
        
		$this->_createSymbolicLinks();
        


        
        $this->view->changeLangUrl = $this->view->url(array(), 'aboutUs');
        
        $sponsors = TripSponsor::getAll();
        $this->view->bottomSponsors = $sponsors;
        
        
	}
	
    
	/**
	 * Init function
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    public function init()
    {
        $this->_javascriptCounter();
        
    }
    
   
	/**
	 * Add jquery javascript counter
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    protected function _javascriptCounter()
    {
        $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/countdown/jquery.countdown.js');
        
		$this->view->dojo()->javascriptCaptureStart();
			
			echo 'dojo.addOnLoad(function() {';
            echo "$('#countdown').countdown({until: new Date(2012, 2, 15), 
            layout: 'še {dn} dni, {hn} ur, {mn} minut in {sn} sekund'});";
			echo '});' . "\n";	
			
			
		$this->view->dojo()->javascriptCaptureEnd(); 
    }
    
	
	/**
	 * Add jquery javascript counter (english)
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    protected function _javascriptEnglishCounter()
    {
        $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/countdown/jquery.countdown.js');
        
		$this->view->dojo()->javascriptCaptureStart();
			
			echo 'dojo.addOnLoad(function() {';
            echo "$('#countdown').countdown({until: new Date(2012, 2, 15), 
            layout: '{dn} days, {hn} hours, {mn} minutes and {sn} seconds'});";
			echo '});' . "\n";	
			
			
		$this->view->dojo()->javascriptCaptureEnd(); 
    }
    
    
    
	/**
	 * Create symbolic links to dojo library etc.
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	protected function _createSymbolicLinks()
	{
		/**
		 * Dojo
		 */
		if($this->_config->dojo->path) {
			$dojoLibPath = ROOT_PATH . DIRECTORY_SEPARATOR .'public';
			$dojoLibPath .= DIRECTORY_SEPARATOR . 'scripts';
			$dojoLibPath .= DIRECTORY_SEPARATOR . 'dojo';
			$dojoLibPath .= DIRECTORY_SEPARATOR . 'library';
			
			if(!is_dir($dojoLibPath)) {
				if($this->_isWindows()){
					$call = 'mklink /D ' . $dojoLibPath . ' ' . $this->_config->dojo->path;
        			exec($call);
        		} else {
					$call = 'ln -s ' . $this->_config->dojo->path . ' ' . $dojoLibPath;
        			shell_exec($call);
        		}
			}
		}
		
		
		/**
		 * Tinymce
		 */
		if(isset($this->_config->tinymce->path)) {
			if($this->_config->tinymce->path) {
				
				$tinymceLibPath = ROOT_PATH . DIRECTORY_SEPARATOR .'public';
				$tinymceLibPath .= DIRECTORY_SEPARATOR . 'scripts';
				$tinymceLibPath .= DIRECTORY_SEPARATOR . 'tiny_mce';
				
				if(!is_dir($tinymceLibPath)) {
					if($this->_isWindows()){
						$call = 'mklink /D ' . $tinymceLibPath . ' ' . $this->_config->tinymce->path;
	        			exec($call);
	        		} else {
						$call = 'ln -s ' . $this->_config->tinymce->path . ' ' . $tinymceLibPath;
	        			shell_exec($call);
	        		}
				}
			}
		}
		
	}
	
	
	/**
	 * Return true if windows
	 * 
	 * @author Uros Pirnat
	 * @return boolean
	 */
	protected function _isWindows() {
		
		if(PHP_OS == 'WINNT' || PHP_OS == 'WIN32'){
        	return true;
        }
        
        return false;
	}

	
	/**
	 * Assign
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	protected function _assign()
	{
		if(Zend_Registry::isRegistered('roleId')) {
			$this->_roleId = Zend_Registry::get('roleId');
		}
		
		if(Zend_Registry::isRegistered('Zend_Acl')) {
			$this->_acl = Zend_Registry::get('Zend_Acl');
		}
		
		if(Zend_Registry::isRegistered('Zend_Translate')) {
			$this->_translator = Zend_Registry::get('Zend_Translate');
		}
		
		if(Zend_Registry::isRegistered('Zend_Cache')) {
			$this->_cache = Zend_Registry::get('Zend_Cache');
		}
		
		
		$this->_root = ROOT_PATH;
		$this->_config = Zend_Registry::get('config');
		$this->_defaultCurrency = $this->_config->currency;
		
		
	}
	
		
    /**
     * Build menu array
     * 
     * @return array
     */
	protected function _buildMenuArray($tmpMenuItems) {
		
		
		$menuItems = array();
		if(is_array($tmpMenuItems)) {
			foreach($tmpMenuItems as $id => $item) {
				
				if(!$item['active']) {
					continue;
				}
				
				$params = $item['url_params'];
				
				
				if($item['link']) {
					$uri = $item['link'];
				} else {
					$uri = $this->view->url($params, $item['rule_code']);
				}
				
				
				$tmp = array(
					'id' => $item['menu_item_id'],
			        'label' => $item['title'],
			        'title' => $item['title'],
			        'order' => $item['ord'],
				    'image' => $item['image'],
					'params' => $params,
					'parent_id' => $item['parent_id'],
					'uri' => $uri,
					'active' => false
				);
				
				
				$menuItems[$item['menu_item_id']] = $tmp;
			}
		}
		
		
		$tree = $this->_mapTree($menuItems);
		
		
		return $tree;
	}
	
	
	/**
	 * Map category tree (dereferncing tehnique)
	 * 
	 * @param array $dataset
	 * @return array
	 */
	protected function _mapTree($dataset) {
		
		$tree = array();
		foreach ($dataset as $id => &$node) {
			
			if (
				!array_key_exists('parent_id', $node) or 
				$node['parent_id'] === null
			) {
				$tree[$id] = &$node;
			} else {
				
				if(!isset($dataset[$node['parent_id']]['pages'])) {
					$dataset[$node['parent_id']]['pages'] = array();
				}
				
				$dataset[$node['parent_id']]['pages'][$id] = &$node;
			}
		}
	
		return $tree;
	}
    
    
	/**
	 * Set visibility of menu elements
	 * 
	 * @param array $container
	 */
    protected function _setState($container) {
    	
    	foreach($container as $page) {
    		
    		$page->visible = false;
    		if(!$page->hidden) {
    			$page->visible = true;
    		}
    		
			if($page->selected) {
	    		$this->_setState($page->pages);
	    				
			}
    	}

    	
    	return $container;
    }
    
    
    /**
     * Create navigation
     * 
     * @return void
     */
    protected function _createNavigation() 
    {
        
        $menuItems = MenuItem::getAll();
		$pages = $this->_buildMenuArray($menuItems);
        $container = new Zend_Navigation($pages);
			
		
		$activeNav = $container->findBy('uri', $_SERVER['REQUEST_URI']);
		
		
		if($activeNav) {
			$activeNav->setActive(true);
			$parentIds = $this->_findParents($activeNav->id, $menuItems, 'menu_item_id');
			
			
			foreach ($parentIds as $parentId) {
				$parentItem = $container->findBy('id', $parentId);
				$parentItem->setActive(true);
			}
		}
		
		Zend_Registry::set('Zend_Navigation', $container);
		
    }
    
    
    /**
     * Find parents
     * 
     * @author Uros Pirnat
     * @param integer $id
     * @param array $dataset
     * @param string $key
     * @param array $parents
     */
    protected function _findParents($id, array $dataset, $key, &$parents = array())
    {
		foreach($dataset as $item) {
			if($item[$key] == $id) {
				if(!is_null($item['parent_id'])) {
					$parents[] = $item['parent_id'];
					$this->_findParents($item['parent_id'], $dataset, $key, $parents);
				}	
			}
		}
		
		
		return $parents;
	}
	
	
	/**
	 * Set default mail transport
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	protected function _setDefaultMailTransport()
	{
		$settings = array(
			'auth' => isset($this->_config->mail->auth) ? $this->_config->mail->auth : null,
			'username' => isset($this->_config->mail->username) ? $this->_config->mail->username : null,
			'password' => isset($this->_config->mail->password) ? $this->_config->mail->password : null,
			'ssl' => isset($this->_config->mail->ssl) ? $this->_config->mail->ssl : null,
			'port' => isset($this->_config->mail->port) ? $this->_config->mail->port : null
		);	

					
		$tr = new Zend_Mail_Transport_Smtp($this->_config->mail->smtp, $settings);
		Zend_Mail::setDefaultTransport($tr);
	}
	
	
}