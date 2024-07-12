<?php
/**
 *  Parent controller to process requests
 *   and have action for child controller 
 * 
 */

use RequestController as Request;

class MyController
{
	/**
	 * to hold db connection object
	 * @var object
	 */
	protected $conn;

	/**
	 * to hold controller name
	 * @var string
	 */
	protected $controller;

	/**
	 * to hold ation id i.e resource id
	 * @var int
	 */
	protected $resource_id;

	/**
	 * to hold request verb in lower case
	 * i.e get, post, put, patch & delete
	 * 
	 * @var string
	 */
	protected $request_verb;

	/**
	 * to hold requested output format
	 * @var string
	 */
	protected $format;

	/**
	 * construct initialize db connection object
	 */
	public function __construct()
	{
		$db = new DatabaseConfig;
		$this->conn = $db->connect();

	}

	/**
	 * function to check and process all request
	 * verbs - GET, POST, PUT, PATCH and DELETE
	 * 
	 * @param  Request $request      object of Request
	 * @return array                 response
	 */
	public function processRequest(Request $request):array
	{
		// requested verb
		$this->request_verb = strtolower($request->verb);

		// requested controller
		$this->controller = $request->url_elements[5];

		// requested data
		$this->data = $request->parameters;

		// requested format
		$this->format = $request->format;

		// action id from request url
		if(isset($request->url_elements[6])){

			// for POST ID not needed
			if($this->request_verb=='post'){
				return $response = array('message' => 'ERROR: Bad Request','status' => '0');
			}
			
			$this->resource_id = (int)$request->url_elements[6];

			// invalid resource id
			if(($this->resource_id == 0) or empty($this->resource_id)){

				$response = array('message' => 'ERROR: Bad Request','status' => '0');

			} else{

				// create resource id variable and pass action id
				$resource_id_str = substr($this->controller, 0, -1).'_id';
				$this->$resource_id_str = $this->resource_id;

				// get resource result set row count 
				$num = $this->getResultSetRowCount();
		  	
		  		if($num > 0){

		  			 /*
		  			  * call action acording to GET, PUT,
		  			  *	 PATCH & DELETE verb
		  			  */
		  			$action = $this->request_verb.'Action';

		  			$response = $this->$action();
		  			
		  		} else {

		  			$response = array('message' => 'ERROR: resource id not found','status' => '0');
		  		}

			}			
		} else {

			// check for POST verb
			if($this->request_verb == 'post'){

				$response = $this->postAction();

				return $response;	
			}

			// check if GET request is for list resource
			if($this->request_verb == 'get'){

				$response = $this->getAllAction();

				return $response;				
			}

			// response for bulk action
			$response = array('message' => 'Bulk action curently not available!','status' => '0');
		}

		return $response;
	}


	/**
	 *  Validate parameter for string, int, boolean etc.
	 *  and also htmlspecialchars & strip_tags
	 *  
	 * @param  [string]  $fieldName 
	 * @param  [string]  $value     
	 * @param  [boolean] $required  
	 * 
	 * @return [string]  $value          
	 */
	public function validateParameter($fieldName, $value, $required = true)
	{
		$item_table_fields = $this->item_model->getItemTableFields();

		if($required == true && empty($value) == true){
			return array('message' => $fieldName.' parameter is required.','status' => '0');
		}

		switch ($item_table_fields[$fieldName]['type']) {
			
			case 'BOOLEAN':
				if (!is_bool($value)) {

					$this->throwError('0', 'Data type is not valid for '. $fieldName);
				}
				break;

			case 'INT':
				if (!is_int($value)) {

					$this->throwError('0', 'Data type is not valid for '. $fieldName);
				}
				break;

			case 'INTEGER':
				if (!is_numeric($value)) {
					$this->throwError('0', 'Data type is not valid for '. $fieldName);
				}
				break;

			case 'STRING':
				if (!is_string($value)) {
					$this->throwError('0', 'Data type is not valid for '. $fieldName);
				}				
				break;
			
			default:
				$this->throwError('0', 'Data type is not valid for '. $fieldName);
				break;
		}

		return htmlspecialchars(strip_tags($value));
		
	}


	/**
	 * To throw error if occure. It uses view
	 *  format i.e. json to send message
	 * 
	 * @param  [string] $code    
	 * @param  [string] $message 
	 * 
	 * @return exit         
	 */
	public function throwError($code, $message)
	{
		$content = array('message' => $message,'status' => $code);

		// view format
		$view_name = ucfirst($this->format) . 'View';

		if(class_exists($view_name)){

			$view = new $view_name();
			$view->render($content);		
		}

		exit;		
	}

}
