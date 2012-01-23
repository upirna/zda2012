<?php


class Route extends Zend_Db_Table_Abstract 
{
	
	/**
	 * The default table name 
	 */
	protected $_name = 'page_route';

	
	/**
	 * Get all routes
	 *
	 * @return array
	 */
	static public function getRoutes() {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Build sql statement
		 */
		$select = $db->select();
		$select->from('page_route');
		$select->order('page_route.ord asc');
		$sql = $select->__toString();
		
		/**
		 * Fetch all
		 */
		$result = $db->fetchAll($sql);
		
		
		/**
		 * Parse
		 */
		foreach ($result as &$item)
		{
			$lines = explode(';', trim($item['default']));
			
			$item['default'] = array();
			foreach ($lines as $line)
			{
				if($line)
				{
					list($key, $value)  = explode('=',$line,2);
					
					$item['default'][$key] = $value;
				}
			}		
		}
		
		return $result;
	}
	
	/**
	 * Get by code
	 *
	 * @param string $code
	 * @return array
	 */
	static public function getByCode($code) {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Build sql statement
		 */
		$select = $db->select();
		$select->from('page_route');
		$select->where('page_route.code = ?', $code);
		$select->order('page_route.ord asc');
		$sql = $select->__toString();
		
		/**
		 * Fetch and return
		 */
		$result = $db->fetchRow($sql);
		
		
		return $result;
	}
	
}
