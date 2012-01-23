<?php



class Content extends Zend_Db_Table_Abstract 
{
	
	/**
	 * Maps
	 * 
	 * @var array
	 */
	static public $map = array(
		'page_content_page_menu_item' => array(
			'content_id' => 'page_content_id',
			'menu_item_id' => 'page_menu_item_id'
		),
		
		'page_menu_item' => array(
			'title',		
			'description',		
			'parent_id',		
			'ord',		
			'menu_position_code' => 'page_menu_position_code',		
			'active',		
			'layout_id' => 'page_layout_id',		
			'route_id' => 'page_route_id',		
			'route_params',		
			'html_title',		
			'keywords'
		),
		
		'page_content' => array(
			'position' => 'page_position_code',
			'content' => 'content',
			'ord' => 'ord',
			'description' => 'description'	
		)
		
		
		
	);
	
	
	/**
	 * The default table name 
	 */
	protected $_name = 'page_content';

	
	/**
	 * Get content by menu item id
	 *
	 * @param int $menuItemId
	 * @return array
	 */
	static public function getContentByMenuItemId($menuItemId)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns of table page_content_page_menu_item
		 */
		$contentMenuCols = self::$map['page_content_page_menu_item'];
		$menuItemCols = self::$map['page_menu_item'];
		$contentCols = self::$map['page_content'];
		
		
		/**
		 * Build select statement
		 */
		$select = $db->select();
		$select->from('page_content_page_menu_item', $contentMenuCols);
		$select->join('page_menu_item', 'page_menu_item.id = page_content_page_menu_item.page_menu_item_id', $menuItemCols);
		$select->join('page_content', 'page_content.id = page_content_page_menu_item.page_content_id', $contentCols);
		$select->where('page_menu_item.id = ?', $menuItemId);
		$select->order('page_content.ord asc');
		$sql = $select->__toString();
		
		
		/**
		 * Fetch and return
		 */
		$result = $db->fetchAll($sql);
		
		return $result;
		
	}
	
	
	/**
	 * Get by route code
	 * 
	 * @author Uros Pirnat
	 * @param string $code
	 * @return array
	 */
	static public function getByRouteCode($code)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Cols
		 */
		$contentMenuCols = self::$map['page_content_page_menu_item'];
		$menuItemCols = self::$map['page_menu_item'];
		$contentCols = self::$map['page_content'];
		
		
		/**
		 * Build select statement
		 */
		$select = $db->select();
		$select->from('page_content_page_menu_item', $contentMenuCols);
		$select->join('page_menu_item', 'page_menu_item.id = page_content_page_menu_item.page_menu_item_id', $menuItemCols);
		$select->join('page_route', 'page_route.id = page_menu_item.page_route_id', array());
		$select->join('page_content', 'page_content.id = page_content_page_menu_item.page_content_id', $contentCols);
		$select->where('page_route.code = ?', $code);
		$sql = $select->__toString();
		
		$results = $db->fetchAll($sql);
		
		return $results;
		
	}
	
	

	
	
}
