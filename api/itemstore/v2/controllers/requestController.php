<?php

use MyTraitController as MyTrait;
use ContainerController as Container;

/*
 *  Request handler to handle the RESTful requests
 */
class RequestController implements RequestInterface
{
	/**
	 * request url elements
	 * @var string
	 */
	public $url_elements;

	/**
	 * HTTP verbs viz. GET, POST, PUT, DELETE
	 * @var string
	 */
	public $verb;

	/**
	 * request parameters aka data
	 * @var array
	 */
	public $parameters;

	/**
	 * constructor to get incoming request
	 */
	public function __construct()
	{
		$this->verb = $_SERVER['REQUEST_METHOD'];
		$this->url_elements = explode('/', $_SERVER['PATH_INFO']);

		$this->parseIncomingParams();

		//initialise json as default format
		$this->format = 'json';
		if(isset($this->parameters['format'])){
			$this->format = $this->parameters['format'];
		}
	}



	/**
	 * Parse incoming parameters	
	 */
	public function parseIncomingParams()
	{
		$parameters = array();
		
		// first pull the GET vars
		if(isset($_SERVER['QUERY_STRING'])){
			parse_str($_SERVER['QUERY_STRING'], $parameters);
		}

		// pull POST/PUT bodies
		$body = file_get_contents("php://input");
		$content_type = false;
		if(isset($_SERVER['CONTENT_TYPE'])){
			$content_type = $_SERVER['CONTENT_TYPE'];
		}
		switch ($content_type) {
			case "application/json":
				$body_params = json_decode($body);
				if($body_params){
					foreach ($body_params as $param_name => $param_value) {
						$parameters[$param_name] = $param_value;
					}
				}
				$this->format = "json";

				break;
			
			case "application/x-www-form-urlencoded":
				parse_str($body, $postvars);
				foreach ($postvars as $field => $value) {
					$parameters[$field] = $value;
				}
				$this->format = "html";

				break;

			default:
				# we could parse other supported formats here
				break;
		}

		$this->parameters = $parameters;
	}




	/**
	 * function to check and process all request
	 * and pass-on request to corresponding function
	 * 
	 * @return array response
	 */
	public function processRequest():array
	{
		$service = '';
		$service_action ='';

		// check for auth request
		if($this->url_elements[1]=='auth'){
			$action_name = $service = 'Auth';

								// second element is the action 
			$service_action = (!empty($this->url_elements[2]) and ($this->url_elements[2] == 'login')) ? ucfirst($this->url_elements[2]) : MyTrait::throwError('0', 'ERROR: Bad Request');
				
		}else{
			// check headers for authorization token
			$all_headers = getallheaders(); 
			if(!empty($all_headers['Authorization'])) {

				//MyTrait::read token...
				$response = (object)MyTrait::readTokenFromHeadersOrPostData();

				if($response->status!='1')MyTrait::throwError('0', 'ERROR: Not authorize');
				
			 	} else MyTrait::throwError('0', 'ERROR: authorization token missing');


			/**
			 * Here, authentication done.
			 *  
			 * By default, first element is action i.e the controller 
			 * 
			 */
			$service = ucfirst($this->url_elements[1]);;
			
			switch ($service) {
				case 'Search':

					if(!empty($this->url_elements[2])){
						// second element is the action 
						$service_action = ucfirst($this->url_elements[2]);

						//create search object
						$service_action_obj  = MyTrait::buildObject($service_action);
						$service_obj = Container::get($service.'Controller', $service_action_obj);

						$service_action = '';
						
					} else MyTrait::throwError('0', 'ERROR: Bad Request');	

				break;
				
				default:
					$action_name = $service;
					$service_action ='';
					$service = 'Microservice';
					break;
			}

		}

		// create object
		$service_obj = ($service != 'Search') ? MyTrait::buildObject($action_name) : $service_obj;

		$process_action = 'process'. $service . 'Request';

		// route the request to the right place
		$result = $this->$process_action($service_obj, $service_action);

		$response = $this->sendResponse($result);

		return $response;
	}




	/**
	 * function to check and process microservice requests
	 * and pass-on request to corresponding controller
	 * 
	 * @param  ControllerInterface $controller object of Controller
	 * @return array response
	 */
	public function processMicroserviceRequest(ControllerInterface $controller):array
	{		
		$this->setControllerProperty($controller);

		// action id from request url
		if(isset($this->url_elements[2])){

			// for POST ID not needed
			if($this->verb=='POST'){
				return $response = array('message' => 'ERROR: Bad Request','status' => '0');
			}
			
			$this->resource_id = (int)$this->url_elements[2];

			// invalid resource id
			if(($this->resource_id == 0) or empty($this->resource_id)){

				$response = array('message' => 'ERROR: Bad Request','status' => '0');

			} else{

				// pass resource id to the controller				
				$controller->setId($this->resource_id);

				// get resource result set row count 
				$num = $controller->getResultSetRowCount();
		  	
		  		if($num > 0){

		  			 /*
		  			  * call action as of REST i.e - GET, PUT,
		  			  *	 PATCH & DELETE verb
		  			  */

		  			// call controller action
					$response = $this->callControllerAction($controller);

		  			
		  		} else {

		  			$response = array('message' => 'ERROR: resource id not found','status' => '0');
		  		}

			}			
		} else {

			// check for POST verb
			if($this->verb == 'POST'){

				$response = $controller->postAction();

				return $response;	
			}

			// check if GET request is for list resource
			if($this->verb == 'GET'){

				$response = $controller->getAllAction();

				return $response;				
			}

			// response for bulk action
			$response = array('message' => 'Bulk action curently not available!','status' => '0');
		}

	
		return $response;
	}





	/**
	 * function to process auth requests
	 * 
	 * @param  ControllerInterface $controller 
	 * @param  string $auth_action 
	 * @return array                          
	 */
	public function processAuthRequest(ControllerInterface $controller, string $auth_action): array
	{

		$this->setControllerProperty($controller);

		// call controller action
		$response = $this->callControllerAction($controller, $auth_action);
	
		return $response;

	}





	/**
	 * function to process search requests
	 * 
	 * @param  object $search_controller 
	 * @return array                          
	 */
	public function processSearchRequest(object $search_controller): array
	{

		// call controller action
		$response = $search_controller->indexAction();
		return $response;

	}





	/**
	 * function to send response in defined format
	 * by default is json format
	 * 
	 * @param  array $result 
	 * @return exit
	 */
	public function sendResponse(array $result)
	{

		// view format
		$view_name = ucfirst($this->format) . 'View';

		if(class_exists($view_name)){

			$view = Container::get($view_name);

			$view->render($result);		
		}

		exit;
	}




	/**
	 *  call action as of REST i.e - GET, PUT,
	 *	 PATCH & DELETE verb
	 *
	 * @param string $extra optional
	 * @return array response
	 */
	private function callControllerAction(ControllerInterface $controller, String $extra=''): array
	{

		// only POST allowed for Auth request
		if(!empty($extra) && ($this->verb != 'POST')){
			return array('message' => 'ERROR: method not supported','status' => '0');
		}

		$action = strtolower($this->verb) . $extra . 'Action';

		//call action
		$response = $controller->$action();

		return $response;
	}




	/**
	 *  function to set controller's property
	 *  
	 * @param ControllerInterface $controller 
	 */
	private function setControllerProperty(ControllerInterface $controller):void
	{
		// set requested data to the controller 
		$controller->setData($this->parameters);

		// set requested format to the controller
		$controller->setFormat($this->format);
		
	}


}