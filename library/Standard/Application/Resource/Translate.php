<?php 

class Standard_Application_Resource_Translate extends Zend_Application_Resource_Translate
{

    /**
     * Retrieve translate object
     *
     * @return Zend_Translate
     * @throws Zend_Application_Resource_Exception if registry key was used
     *          already but is no instance of Zend_Translate
     */
    public function getTranslate()
    {

        if (null === $this->_translate) {
            $options = $this->getOptions();

            if (!isset($options['content']) && !isset($options['data'])) {
                require_once 'Zend/Application/Resource/Exception.php';
                throw new Zend_Application_Resource_Exception('No translation source data provided.');
            } else if (array_key_exists('content', $options) && array_key_exists('data', $options)) {
                require_once 'Zend/Application/Resource/Exception.php';
                throw new Zend_Application_Resource_Exception(
                    'Conflict on translation source data: choose only one key between content and data.'
                );
            }

            if (empty($options['adapter'])) {
                $options['adapter'] = Zend_Translate::AN_ARRAY;
            }

            if (!empty($options['data'])) {
                $options['content'] = $options['data'];
                unset($options['data']);
            }

            if (isset($options['options'])) {
                foreach($options['options'] as $key => $value) {
                    $options[$key] = $value;
                }
            }

            if (!empty($options['cache']) && is_string($options['cache'])) {
                $bootstrap = $this->getBootstrap();
                if ($bootstrap instanceof Zend_Application_Bootstrap_ResourceBootstrapper &&
                    $bootstrap->hasPluginResource('CacheManager')
                ) {
                    $cacheManager = $bootstrap->bootstrap('CacheManager')
                        ->getResource('CacheManager');
                    if (null !== $cacheManager &&
                        $cacheManager->hasCache($options['cache'])
                    ) {
                        $options['cache'] = $cacheManager->getCache($options['cache']);
                    }
                }
            }

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;
            unset($options['registry_key']);

            if(Zend_Registry::isRegistered($key)) {
                $translate = Zend_Registry::get($key);
                if(!$translate instanceof Zend_Translate) {
                    require_once 'Zend/Application/Resource/Exception.php';
                    throw new Zend_Application_Resource_Exception($key
                                   . ' already registered in registry but is '
                                   . 'no instance of Zend_Translate');
                }

                $translate->addTranslation($options);
                $this->_translate = $translate;
            } else {

            	try {
            		
            		$configCache = new Zend_Cache_Core(array('automatic_serialization'=> true));
					$backend = new Zend_Cache_Backend_Apc();
					$configCache->setBackend($backend);	
					
					if($configCache->load('trogonZendTranslate', true)) {
						$this->_translate = $configCache->load('trogonZendTranslate', true);
					} else {
						$this->_translate = new Zend_Translate($options);
						$configCache->save($this->_translate, 'trogonZendTranslate', array(), null);
					}	
					
            	} catch (Exception $e) {
            		$this->_translate = new Zend_Translate($options);
            	}

            
                Zend_Registry::set($key, $this->_translate);
            }
        }

        return $this->_translate;
    }
    
    
    
}