<?php 

// Define path to root directory
defined('ROOT_PATH')
    || define('ROOT_PATH',
              realpath(dirname(__FILE__) . '/../'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
              realpath(dirname(__FILE__) . '/../application'));


$enviorment = 'production';
if(file_exists(APPLICATION_PATH . '/configs/enviorment.ini')) {
	$enviorment = file_get_contents(APPLICATION_PATH . '/configs/enviorment.ini');
}              
              
              
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : $enviorment));



if(!file_exists(APPLICATION_PATH . '/configs/system.ini')) {
	throw new Exception('System.ini is missing!');
}

if(!file_exists(APPLICATION_PATH . '/configs/application.ini')) {
	throw new Exception('Application.ini is missing!');
}

if(!file_exists(APPLICATION_PATH . '/configs/user.ini')) {
	throw new Exception('User.ini is missing!');
}
                                         
                                         
// Parse configuration file
$config = parse_ini_file(APPLICATION_PATH . '/configs/system.ini');

// Check if isset($config['framework'] is set
if(!isset($config['framework'])) {
	throw new Exception(sprintf("Path to framework is not defined! 
		Please set 'framework' in configuration file!"));
}

// Check if base directory exist
if(!file_exists($config['framework'] . '/' . 'Zend')) {
	throw new Exception(sprintf("In directory '%s' does not 
		exist Zend Framework!", $config['framework']));
}

// Set include path
set_include_path(get_include_path() . PATH_SEPARATOR . $config['framework'] . '/'
 . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . ROOT_PATH . '/models');

 
// Zend_Application 
require_once 'Zend/Application.php';
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';
require_once 'Zend/Session.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Backend/Apc.php';
require_once 'Zend/Cache.php';
require_once ROOT_PATH . '/library/Standard/Db/Adapter/Mysql.php';
require_once ROOT_PATH . '/library/Standard/Application.php';





    
try {
	$configCache = new Zend_Cache_Core(array('automatic_serialization'=> true ));
	$backend = new Zend_Cache_Backend_Apc();
	$configCache->setBackend($backend);	
} catch(Exception $e) {
	$configCache = null;
}



// Create application, bootstrap, and run
$application = new Standard_Application(
	APPLICATION_ENV, 
	array( 
		'config' => array(
			APPLICATION_PATH . '/configs/application.ini',
			APPLICATION_PATH . '/configs/system.ini',
			APPLICATION_PATH . '/configs/user.ini'
		)
	),
	$configCache
);


$application->bootstrap(array('db'));
$application->bootstrap();


$options = $application->getOptions();
$options['pageSettings'] = PageSetting::getOptions();

Zend_Registry::set('config', new Zend_Config($options));



$locale = new Zend_Locale(Zend_Registry::get('config')->locale);
Zend_Registry::set('Zend_Locale', $locale);

$application->run();
$bootstrap = $application->getBootstrap();



