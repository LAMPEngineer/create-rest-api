<?php

/**
 * Users model to run actions on DB
 */
class UsersModel extends MyModel implements ModelInterface
{


	// define table properties
	protected $id;
	protected $name;
	protected $email;
	protected $password;

	// setter and getter
	public function setId($id){ $this->id = $id; }
	public function getId(){ return $this->id; }
	
	public function setName($name){ $this->name = $name; }
	public function getName(){ return $this->name; }
	
	public function setEmail($email){ $this->email = $email; }
	public function getEmail(){ return $this->email; }
	
	public function setPassword($password){ 
		$this->password = $this->passwordHash($password);	   
	 }

	public function getPassword(){ return $this->password; }


	/**
	 * Class constructor
	 * @param object $db database connection object
	 */
	public function __construct($db)
	{
	  $this->conn = $db;

	  $this->table = 'users';

	}




	/**
	 *  User table fields with type and 
	 *  corresponding methods for setter/getter 
	 *  
	 * @return [type] array
	 */
	public function getTableFields(): array
	{
		return array('id'   => array('method' => 'id', 'type' => 'INT'),
                     'name' => array('method' => 'name', 'type' => 'STRING'),
                     'email' => array('method' => 'email', 'type' => 'STRING'),
                     'password' => array('method' => 'password', 'type' => 'STRING')
	                );
	}


	/**
	 * function to check if email is exists in the DB
	 * 
	 * @param  [string] $email
	 * @return [int]      
	 */
	public function checkEmail(string $email):int
	{
		$num = 0;
		// select query 
		$query = "SELECT * FROM ". $this->table
						." WHERE email = :email";

	    // prepare statement
	    $stmt = $this->conn->prepare($query);

	    // bind param
	    $stmt->bindParam(':email', $email);

		$stmt->execute();

		$num = $stmt->rowCount();

		return $num;			

	}

	/**
	 * function to check login 
	 * 
	 * @return array $user_data
	 */
	public function login():array
	{
		// select query 
		$query = "SELECT * FROM ". $this->table
						." WHERE email = :email";

	    // prepare statement
	    $stmt = $this->conn->prepare($query);

	    // bind param
	    $stmt->bindParam(':email', $this->email);

		$stmt->execute();

      	$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $user_data;			

	}

	/**
	 * function to hash the passward
	 *
	 * @param [string] $password 
	 * @return [string] hashed passward
	 */
	protected function passwordHash(string $passward):string
	{
		return password_hash($passward, PASSWORD_DEFAULT);
	}


}