<?php

/**
 * Item model to run actions on DB
 */
class ItemModel
{

	/**
	 * to hold database connection object
	 * @var db object
	 */
	private $conn;


	/**
	 * table name
	 * @var string
	 */
	private $table;

	// define table properties
	private $id;
	private $name;
	private $description;
	private $createdAt;
	private $updatedAt;

	// getter and setter
	public function setId($id){ $this->id = $id; }
	public function getId(){ return $this->id; }
	public function setName($name){ $this->name = $name; }
	public function getName(){ return $this->name; }
	public function setDescription($description){ $this->description = $description; }
	public function getDescription(){ return $this->description; }
	public function setCreatedAt($createdAt){ $this->created_at = $createdAt; }
	public function getCreatedAt(){ return $this->createdAt; }
	public function setUpdatedAt($updatedAt){ $this->updatedAt = $updatedAt; }
	public function getUpdetedAt(){ return $this->updatedAt; }





	/**
	 * Class constructor
	 * @param object $db database connection object
	 */
	public function __construct($db)
	{
	  $this->conn = $db;

	  $this->table = 'items';

	}


	public function getAllItems(): array 
	{
      // prepare statement
      $stmt = $this->conn->prepare("SELECT * FROM ". $this->table);
      $stmt->execute();
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $items;

	}

	public function getItemDetailsById(): array
	{	
      $stmt =  $this->getResultSetById();
      $item = $stmt->fetch(PDO::FETCH_ASSOC);

      return $item;
	}


	public function getResultSetRowCountById():int 
	{
	  $num = 0;
	  $result = $this->getResultSetById();
	  $num = $result->rowCount();

	  return $num;
	}

	/**
	 * method to read collection as well as a resource
	 * from GET verb action call
	 *  
	 * @param  int|integer $id 			resource ID
	 * @return object               	PODStatement Object
	 */
	//public function read():object
	public function getResultSetById():object
	{
	  // query to get collection 
	  $query = "SELECT * FROM ". $this->table
					." WHERE id = :itenId";

      // prepare statement
      $stmt = $this->conn->prepare($query);

      // bind param
      $stmt->bindParam(':itenId', $this->id, PDO::PARAM_INT);

      // execute query
      $stmt->execute();

      return $stmt;

	}



	/**
	 * method to create a resource 
	 * call from POST verb action
	 * 
	 * @return boolean       
	 */
	public function insert():bool
	{
		// get model table fields
		$item_table_fields = $this->getItemTableFields();

		$set='';
		$i=0;
		$bind_param_array = array();

		// make SET and $bindParam variable
		foreach ($item_table_fields as $key => $value) {

			if (!empty($this->$key)) {

				$set .= ($i>0?',':'').'`'. $key . '`= :' .$key;	

				$bind_param_array[$key] =  $this->$key ;

				$i++;
			}
		}

		// insert query
		$query = "INSERT INTO "  . $this->table . " SET " . $set;

		$stmt = $this->conn->prepare($query);

		// bind param 
		foreach ($bind_param_array as $key => &$value) {
			$stmt->bindParam($key, $value);	
		}

		if($stmt->execute()){
			return true;
		}

		return false;

	}




	/**
	 * method to update a resource
	 * call from PATCH and PUT verb actions
	 * 
	 * @param  string $request_verb i.e PUT & PATCH
	 * @return boolean       
	 */
	public function update($request_verb):bool
	{

		$item_table_fields = $this->getItemTableFields();

		$set='';
		$i=0;
		foreach ($item_table_fields as $key => $value) {

			if($request_verb=='patch'){
				
				// make $set string for PATCH request
				if(!empty($this->$key)){
			
					$set .= ($i>0?',':'').'`'. $key . '`=';	
					
					$set .= ($this->$key === null?'NULL':'"'.$this->$key.'"');

					$i++;
				} 
			 
			}elseif($request_verb=='put'){

				// make $set string for PUT request
				$set .= ($i>0?',':'').'`'. $key . '`=';

				if(!empty($this->$key)){	

				$set .= ($this->$key === null?'NULL':'"'.$this->$key.'"');

				} else {
					$set .= 'NULL';
				}

				$i++;
			}
			
		}

		// query to update data 
		$query = "UPDATE "  . $this->table . " SET ".$set." WHERE id = :id"; 
	
		$stmt = $this->conn->prepare($query);

		// bind param
	    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

		if($stmt->execute()){
			return true;
		}

		return false;
	}




	/**
	 * method to delete a resource
	 * call from DELETE verb action
	 * 
	 * @return boolean       
	 */
	public function delete():bool
	{
		// query to delete data 
		$query = "DELETE FROM `" . $this->table. "` WHERE id = :id";

		$stmt = $this->conn->prepare($query);

	    // bind param
	    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

		if($stmt->execute()){
			return true;
		}

		return false;
	}

	/**
	 *  Item table fields with type and 
	 *  corresponding methods for setter/getter 
	 *  
	 * @return [type] array
	 */
	public function getItemTableFields(): array
	{
		return array('id'   => array('method' => 'id', 'type' => 'INT'),
                     'name' => array('method' => 'name', 'type' => 'STRING'),
                     'description' => array('method' => 'description', 'type' => 'STRING'),
                     'created_at' => array('method' => 'createdAt', 'type' => 'STRING'),
                     'updated_at' => array('method' => 'updatedAt', 'type' => 'STRING')
	                );
	}
	
	
}