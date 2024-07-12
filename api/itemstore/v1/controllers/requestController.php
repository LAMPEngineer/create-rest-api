<?php
/*
 *  Request handler to handle the RESTful requests
 */
class RequestController
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
	 * request parameters
	 * @var array
	 */
	public $parameters;

	/**
	 * constructor to get incoming request
	 */
	public function __construct()
	{
		$this->verb = $_SERVER['REQUEST_METHOD'];
		$this->url_elements = explode('/', $_SERVER['REQUEST_URI']);

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

}