<?php 



class Standard_Application extends Zend_Application
{

	/**
	 * Cache
	 * 
	 * @var object
	 */
    protected $_configCache;

    
    /**
     * Construcotr
     * 
     * @author Uros Pirnat
     * @param string $environment
     * @param array $options
     * @param Zend_Cache_Core $configCache
     */
    public function __construct($environment, $options = null, Zend_Cache_Core $configCache = null)
    {
        $this->_configCache = $configCache;
        parent::__construct($environment, $options);
    }

    
    /**
     * Get cache ID
     * 
     * @author Uros Pirnat
     * @param unknown_type $file
     */
    protected function _cacheId($file)
    {
        return md5($file . '_' . $this->getEnvironment());
    }

    /**
     * Load configuration
     * 
     * @author Uros Pirnat
     * @see Zend_Application::_loadConfig()
     */
    protected function _loadConfig($file)
    {
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (
            $this->_configCache === null 
            || $suffix == 'php' 
            || $suffix == 'inc'
        ) {
            return parent::_loadConfig($file);
        }

        $configMTime = filemtime($file);
        
        $cacheId = $this->_cacheId($file);
        $cacheLastMTime = $this->_configCache->test($cacheId);
        
        if (
            $cacheLastMTime !== false 
            && $configMTime < $cacheLastMTime
        ) { 
            return $this->_configCache->load($cacheId, true);
        } else {
            $config = parent::_loadConfig($file);
            $this->_configCache->save($config, $cacheId, array(), null);

            return $config;
        }
    }
    
    
    /**
     * Get settings from database
     * 
     * @author Uros Pirnat
     * @return void
     */
    public function getOptionsFromDatabase()
    {
		require_once 'Settings.php';
		$settings = Settings::getAll();
		return $settings;
    }
    
    
    
    
}