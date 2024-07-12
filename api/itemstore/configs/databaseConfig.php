<?php

/*
 *  DB connection with PDO
 */
class DatabaseConfig 
{

	private $conn;

	//DB connect
	public function connect()
	{
		global $config;

		$this->conn = null;

		try{
			//PDO object
			$this->conn = new PDO('mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),env('DB_USERNAME'), env('DB_PASSWORD'));

			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch(PDOException $e) {
			echo 'Connection Error: '.$e->getMessage();			
		}

		return $this->conn;
	}
	
}
