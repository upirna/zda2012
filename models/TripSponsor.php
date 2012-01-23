<?php



class TripSponsor 
{
      
    /**
     * Map
     * 
     * @var array
     */
    static public $map = array(
        'trip_sponsor' => array(
            'tripSponsorId' => 'id',
            'name' => 'name',
            'image' => 'image',
            'shortDescription' => 'short_description',
            'ord' => 'ord',
            'url' => 'url',
            'type' => 'type'
         )     
    );
    
    
    /**
     * Get all
     * 
     * @var array
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
        $select->from('trip_sponsor', self::$map['trip_sponsor']);
        $select->order('trip_sponsor.amount desc');
        $select->order('trip_sponsor.ord');
        $sql = $select->__toString();
        
        /**
         * Fetch and return
         */
        $results = $db->fetchAll($sql);
        
        return $results;
        
    }
    
    


    
    
}


