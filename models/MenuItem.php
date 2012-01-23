<?php



class MenuItem extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	protected $_name = 'page_menu_item';
	
	
	/**
	 * Get all
	 * 
	 * @return array
	 */
	static public function getAll() {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns
		 */
		$cols = array(
			'menu_item_id' => 'id',
			'title',
			'description',
			'parent_id',
			'ord',
			'link',
			'menu_position_code' => 'page_menu_position_code',
			'active',
			'layout_id' => 'page_layout_id',
			'route_id' => 'page_route_id',
		    'image' => 'image',
			'route_params',
			'html_title',
			'keywords'
		);
		
		$routeCols = array(
			'rule',
			'default',
			'rule_code' => 'code'
		);
		
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('page_menu_item', $cols);
		$select->joinLeft('page_route', 'page_route.id = page_menu_item.page_route_id', $routeCols);
		$select->order('page_menu_item.parent_id');
		$select->order('page_menu_item.ord');
		$sql = $select->__toString();
		
		
		$tmpResults = $db->fetchAll($sql);
		
		$results = array();
		foreach($tmpResults as $result) {
			$id = $result['menu_item_id'];
			$results[$id] = $result;
			$compiled = self::compileRouteParams($result['route_params']);
			$results[$id]['url_params'] = $compiled;
		}
		
		
		return $results;
		
	}
	
	
	/**
	 * Compile 
	 * 
	 * @param string $string
	 * @return array
	 */
	static public function compileRouteParams($string) {
		
		$compiled = array();
		
		$lines = explode(';', trim($string));
		if(is_array($lines)) {
			foreach ($lines as $line) {
				if($line) {
					list($key, $value) = explode('=', $line, 2);
					$compiled[$key] = trim($value);
				}
			}		
		}
		
		return $compiled;
		
	}
	
	
	/**
	 * Get menu item by code
	 * 
	 * @param string $code
	 * @return array
	 */
	static public function getMenuItemByCode($code) {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns
		 */
		$layoutCols = array(
			'layout_path',
			'layout',
			'script'
		);
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('page_menu_item');
		$select->join('page_layout', 'page_layout.id = page_menu_item.page_layout_id', $layoutCols);
		$sql = $select->__toString();
		$results = $db->fetchAll($sql);
		
		
		/**
		 * Loop over and compile
		 */
		foreach($results as $key => $result) {
			$results[$key]['url_params'] = self::compileRouteParams($result['route_params']);
		}	

		
		/**
		 * Get by code
		 */ 
		$return = array();
		foreach ($results as $row) {
			if(isset($row['url_params'])) {
				if(isset($row['url_params']['code']) && $row['url_params']['code'] == $code) {
					$return = $row;
				}
			}
		}
		
		
		
		return $return;	
		
	}
	
	
	/**
	 * Get menu item by id
	 * 
	 * @param int $menuItemId
	 * @return array
	 */
	static public function getMenuItemById($menuItemId) {
		
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns
		 */
		$cols = array(
			'id',
			'title',
			'description',
			'parent_id',
			'ord',
			'page_menu_position_code',
			'active',
			'page_layout_id',
			'page_route_id',
			'route_params',
			'html_title',
			'keywords',
			'lang',
			'page_trunk_code',
			'link',
			'force_editing',
			'menu_image',
			'clickable',
			'sys_created',
			'sys_updated'
		);
		
		
		$layoutCols = array(
			'layout_path',
			'layout',
			'script'
		);
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('page_menu_item', $cols);
		$select->join('page_layout', 'page_layout.id = page_menu_item.page_layout_id', $layoutCols);
		$select->where('page_menu_item.id = ?', $menuItemId);
		$sql = $select->__toString();
		
		
		/**
		 * Fetch and return
		 */
		$result = $db->fetchRow($sql);
		
		return $result;	
		
	}
	
	
	
	
	
	
}
