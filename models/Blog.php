<?php 


class Blog
{
    
    /**
     * Map
     *
     * @var array
     */
    static public $map = array(
        'blog' => array(
            'blogId' => 'id',
            'title' => 'title',
            'author' => 'author',
            'content' => 'content',
            'date' => 'date'
        )
    );
    
    
    /**
     * Get all
     *  
     * @author Uros Pirnat
     * @return array
     */
    static public function getAll($returnObject = false)
    {
        /**
         * Database instance
         */
        $db = Zend_Registry::get('db');
        
        
        /**
         * Build SQL statement
         */
        $select = $db->select();
        $select->from('blog', self::$map['blog']);
        $sql = $select->__toString();
        
        
        /**
         * Fetch and return
         */
        $result = $db->fetchAll($sql);
        
        return $result;
        
    }
    
}