<?php
class Standard_Db_Adapter_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
	/**
     * Current Transaction Level
     *
     * @var int
     */
    protected $_transactionLevel = 0;
 
    public function __construct($config = array())
	{
		parent::__construct($config);
		$this->query('SET NAMES UTF8');
	}
	 
    
    /**
     * Begin new DB transaction for connection
     *
     * @return Standard_Db_Adapter_Mysql
     */
    public function beginTransaction()
    {
        if ( $this->_transactionLevel === 0 ) {
            parent::beginTransaction();
        }
        $this->_transactionLevel++;
 
	return $this;
    }
 
    /**
     * Commit DB transaction
     *
     * @return Standard_Db_Adapter_Mysql
     */
    public function commit()
    {
        if ( $this->_transactionLevel === 1 ) {
            parent::commit();
        }
        $this->_transactionLevel--;
 
        return $this;
    }
 
    /**
     * Rollback DB transaction
     *
     * @return Standard_Db_Adapter_Mysql
     */
    public function rollback()
    {
        if ( $this->_transactionLevel === 1 ) {
            parent::rollback();
        }
        $this->_transactionLevel--;
 
        return $this;
    }
 
    /**
     * Get adapter transaction level state. Return 0 if all transactions are complete
     *
     * @return int
     */
    public function getTransactionLevel()
    {
        return $this->_transactionLevel;
    }
	
}

