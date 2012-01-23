<?php


final class Standard_Version
{
    /**
     * Version identification - see compareVersion()
     */
    const VERSION = '0.2.0 - 821';
    
    /**
     * Tries to return the current SVN commit number otherwise returns the VERSION constant.
     * @return string
     */
    public static function getVersion()
    {
        if(file_exists(ROOT_PATH . '/.svn/entries')) {
        	$entries = file(ROOT_PATH . '/.svn/entries');
       		$entries = substr($entries[3], 0, -1);
        	if (is_numeric($entries)) {
            	return $entries;
        	}
        }
    	
    	return self::VERSION;
    }

    /**
     * Compare the specified Zend Framework version string $version
     * with the current Zend_Version::VERSION of Zend Framework.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return boolean           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }
}
