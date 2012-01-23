<?php 

class PageSetting
{
	/**
	 * Maps
	 * 
	 * @var array
	 */
	static public $map = array(
		'page_setting' => array(
			'settingId' => 'id',
			'name' => 'name',
			'value' => 'value',
			'description' => 'description'
		)
	);
	
	
	/**
	 * Get all
	 * 
	 * @author Uros Pirnat
	 * @param boolean $returnObject
	 * @return object|array
	 */
	static public function getAll($returnObject = false)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('page_setting', self::$map['page_setting']);
		$select->order('page_setting.name');
		
		
		if($returnObject) {
			return $select;
		}
		
		$sql = $select->__toString();
		$result = $db->fetchAll($sql);
		
		return $result;
		
	}
	
	
	/**
	 * Get options
	 * 
	 * @author Uros Pirnat
	 * @return array
	 */
	static public function getOptions()
	{
		$settings = self::getAll();
		
		$options = array();
		if(is_array($settings)) {
			foreach($settings as $pageOption) {
				
				$name = $pageOption['name'];
				$name = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
				$name[0] = strtolower($name[0]);
				
				
				$options[$name] = $pageOption['value'];
			}
		}
		
		return $options;
	}
	
	
	
	
}

