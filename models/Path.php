<?php



class Path extends Zend_Db_Table_Abstract 
{
	
	/**
	 * The default table name 
	 */
	protected $_name = 'page_path';

	
	/**
	 * Get by code
	 *
	 * @param string $code
	 * @return array
	 */
	static public function getPathByCode($code) {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns
		 */
		$cols = array(
			'path_id' => 'id',
			'code',
			'path',
			'start_from_root',
		);
		
		
		/**
		 * Build sql statement
		 */
		$select = $db->select();
		$select->from('page_path', $cols);
		$select->where('page_path.code = ?', $code);
		$sql = $select->__toString();
		
		
		/**
		 * Fetch and return
		 */
		$result = $db->fetchRow($sql);
		
		
		if(!$result) {
			return null;
		}
		
		$double = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
		$path = $result['path'];
		if($result['start_from_root']) {
			$path = ROOT_PATH . DIRECTORY_SEPARATOR . $path;
		}
		
		$path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$path = $path . DIRECTORY_SEPARATOR;
		$path = str_replace($double, DIRECTORY_SEPARATOR, $path);
		
		return $path;
	}
	
}
