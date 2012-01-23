<?php 

class Gallery 
{
	
	/**
	 * Get album by ID
	 * 
	 * @author Uros Pirnat
	 * @param integer $albumId
	 * @return array
	 */
	static public function getAlbumById($albumId)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Columns
		 */
		$cols = array(
			'albumId' => 'id',
			'name',
			'description',
			'sys_created',
			'uniqueId' => 'unique_id',
			'timestamp'
		);
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('gallery_album', $cols);
		$select->where('gallery_album.id = ?', $albumId);
		$sql = $select->__toString();
		
		
		/**
		 * Fetch and return
		 */
		$result = $db->fetchRow($sql);
		
		return $result;
	}
	
	
	/**
	 * Get items by album ID
	 * 
	 * @author Uros Pirnat
	 * @param integer $albumId
	 * @return array
	 */
	static public function getItemsByAlbumId($albumId)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		
		
		/**
		 * Cols
		 */
		$cols = array(
			'itemId' => 'id',
			'file',
			'pathCode' => 'default_path_code',
			'albumId' => 'gallery_album_id',
			'sys_created'
		);
		
		
		/**
		 * Build SQL statement
		 */
		$select = $db->select();
		$select->from('gallery_item', $cols);
		$select->where('gallery_item.gallery_album_id = ?', $albumId);
		$sql = $select->__toString();
		
		
		/**
		 * Fetch and return
		 */
		$results = $db->fetchAll($sql);
		
		
		return $results;
		
	}
	
	
	
}