<?php 

class TripTeamMember extends Zend_Db_Table_Abstract
{
    
    /**
     * Map
     * 
     * @var array
     */
    static public $map = array(
        'trip_team_member' => array(
            'teamMemberId' => 'id',
            'firstname' => 'firstname',
            'lastname' => 'lastname',    
            'description' => 'description',
            'image' => 'image',
            'email' => 'email',
            'work' => 'work',
            'education' => 'education',
            'date_of_birth' => 'date_of_birth'
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
        $select->from('trip_team_member', self::$map['trip_team_member']);
        $select->order('firstname');
        $select->order('lastname');
        $sql = $select->__toString();
        
        
        /**
         * Fetch and return
         */
        $results = $db->fetchAll($sql);
        
        return $results;
        
    }
    
    
}

    
    
    