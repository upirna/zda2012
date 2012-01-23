<?php 

class TripEvent extends Zend_Db_Table_Abstract
{
    
    /**
     * Map
     * 
     * @var array
     */
    static public $map = array(
        'trip_event' => array(
            'tripEventId' => 'id',
            'name' => 'name',
            'start' => 'start',
            'end' => 'end',
            'description' => 'description',
            'purpose' => 'purpose'
         )     
    );
    
    
    /**
     * Get all members
     *  
     * @author Uros Pirnat
     * @return array
     */
    static public function getAll()
    {
        /**
         * Database instance
         */
        $db = Zend_Registry::get('db');
        
        
        /**
         * Build SQL statement
         */
        $select = $db->select();
        $select->from('trip_event', self::$map['trip_event']);
        $select->order('trip_event.start asc');
        $sql = $select->__toString();
        
        
        /**
         * Fetch and return
         */
        $results = $db->fetchAll($sql);
        
        return $results;
        
    }
    
    
}

    
    
    