<?php 



class Contact extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	protected $_name = 'page_contact';

	
	
	/**
	 * Save
	 * 
	 * @author Uros Pirnat
	 * @param array $data
	 * @param integer|null $contactId
	 */
	static public function save(array $data, $contactId = null)
	{
		/**
		 * Database instance
		 */
		$db = Zend_Registry::get('db');
		$db->beginTransaction();
		$success = false;
		
		try {
			
			if($contactId) {
				$db->update('page_contact', $data, "id = '$contactId'");
			} else {
				$db->insert('page_contact', $data);
			}
			
			
			$db->commit();
			
			$success = true;
			
		} catch(Exception $e) {
			$db->rollBack();
			echo $e->getMessage();
		}
		
		return $success;
		
	}
	
	
	
}
	