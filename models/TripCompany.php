<?php 

class TripCompany extends Zend_Db_Table_Abstract
{
    
    /**
     * Map
     * 
     * @var array
     */
    static public $map = array(
        'trip_company' => array(
            'tripCompanyId' => 'id',
            'name' => 'name',
            'description' => 'description',    
            'image' => 'image'
         )     
    );
    
    
    /**
     * Get all members
     *  
     * @author Uros Pirnat
     * @return array
     */
    static public function getAll($type = null)
    {
        /**
         * Database instance
         */
        $db = Zend_Registry::get('db');
        
        
        /**
         * Build SQL statement
         */
        $select = $db->select();
        $select->from('trip_company', self::$map['trip_company']);
        
        if ($type !== null) {
            $select->where('type = ?', $type);
        }
        
        
        $sql = $select->__toString();
        
        
        /**
         * Fetch and return
         */
        $results = $db->fetchAll($sql);
        
        return $results;
        
    }
    
    
}

    
    
    