<?php

/**
 * Items model to run actions on DB
 */
class ItemsModel extends MyModel implements ModelInterface
{

	// define table properties
	protected $id;
	protected $name;
	protected $description;
	protected $createdBy;
	protected $updatedBy;


	// setter and getter
	public function setId($id){ $this->id = $id; }
	public function getId(){ return $this->id; }
	
	public function setName($name){ $this->name = $name; }
	public function getName(){ return $this->name; }
	
	public function setDescription($description){ $this->description = $description;}
	public function getDescription(){ return $this->description; }

	public function setCreatedBy($createdBy){ $this->created_by = $createdBy;}
	public function getCreatedBy(){ return $this->createdBy; }

	public function setUpdatedBy($updatedBy){$this->updated_by = $updatedBy;}
	public function getUpdetedBy(){ return $this->updatedBy; }
	

	/**
	 * Class constructor
	 * @param object $db database connection object
	 */
	public function __construct($db)
	{
	  $this->conn = $db;

	  $this->table = 'items';

	}






	/**
	 *  Table fields with type and 
	 *  corresponding methods for setter/getter 
	 *  
	 * @return array
	 */
	public function getTableFields(): array
	{
		return array('id'   => array('method' => 'id', 'type' => 'INT'),
                     'name' => array('method' => 'name', 'type' => 'STRING'),
                     'description' => array('method' => 'description', 'type' => 'STRING'),
                     'created_by' => array('method' => 'createdBy', 'type' => 'INT'),
                     'updated_by' => array('method' => 'updatedBy', 'type' => 'INT')
	                );
	}
	
	
}