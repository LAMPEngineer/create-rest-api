<?php
/**
 * Auth model to run actions on DB
 */
class authModel extends MyModel implements ModelInterface
{
	// define table properties
	protected $id;


	// setter and getter
	 
	

	/**
	 * Class constructor
	 * @param object $db database connection object
	 */
	public function __construct($db)
	{
	  $this->conn = $db;

	  $this->table = 'auth';

	}


	/**
	 *  Auth table fields with type and 
	 *  corresponding methods for setter/getter 
	 *  
	 * @return [type] array
	 */
	public function getTableFields(): array
	{
		return array('id'   => array('method' => 'id', 'type' => 'INT')
                     
	                );
	}


}