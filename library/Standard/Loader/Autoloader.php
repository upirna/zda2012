<?php 


class Standard_Loader_Autoloader
{
	
	/**
	 * Autload
	 * 
	 * @author Uros Pirnat
	 * @param string $class
	 * @return object|boolean
	 */
	public function autoload($class)
	{
		$file = ROOT_PATH  . '/models/' . $class . '.php';
		if (is_file($file)) {
			require_once($file);
			return $class;
		} else {
			
			$parts = explode('_', $class);
			
			if(count($parts) === 3) {
				
				$module = strtolower($parts[0]);
				$file = $parts[1];
				$isForm = $parts[2] == 'Form' ? true : false;
				
				if($isForm) {
					$file = APPLICATION_PATH . '/' . $module . '/forms/' . $file . '.php';
				
					if(is_file($file)) {
						require_once($file);
						return $class;
					}
				}
			}
		}
		
		
		return false;
	}
	
}