<?php
/**
 *  Parent model to process requests
 *   and have action for child model 
 * 
 */
class MyModel
{
	/**
	 * to hold database connection object
	 * @var db object
	 */
	protected $conn;


	/**
	 * table name
	 * @var string
	 */
	protected $table;
	

	/**
	 * To get all users
	 * 
	 * @return [array] users array
	 */
	public function getAll(): array 
	{
      // prepare statement
      $stmt = $this->conn->prepare("SELECT * FROM ". $this->table);
      $stmt->execute();
      $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $all_data;

	}



	/**
	 *  To get user by Id
	 *  
	 * @return [array] user array
	 */
	public function getDetailsById(): array
	{	
      $stmt =  $this->getResultSetById();
      $id_data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $id_data;
	}



	/**
	 *  To get result set row count
	 *  
	 * @return [integer] result set count
	 */
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
					." WHERE id = :id";

      // prepare statement
      $stmt = $this->conn->prepare($query);

      // bind param
      $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

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
		$table_fields = $this->getTableFields();

		$set='';
		$i=0;
		$bind_param_array = array();

		// make SET and $bindParam variable
		foreach ($table_fields as $key => $value) {

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

		$table_fields = $this->getTableFields();

		$set='';
		$i=0;
		foreach ($table_fields as $key => $value) {

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



}
