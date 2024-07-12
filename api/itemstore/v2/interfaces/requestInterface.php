<?php

interface RequestInterface
{

	/**
	 * Parse incoming parameters	
	 */
	public function parseIncomingParams();


	/**
	 * function to check and process all request
	 * and pass-on request to corresponding function
	 * 
	 * @return array response
	 */
	public function processRequest():array;



	/**
	 * function to check and process microservice requests
	 * and pass-on request to corresponding controller
	 * 
	 * @param  ControllerInterface $controller object of Controller
	 * @return array response
	 */
	public function processMicroserviceRequest(ControllerInterface $controller):array;



	/**
	 * function to process auth requests
	 * 
	 * @param  ControllerInterface $controller 
	 * @param  string $auth_action 
	 * @return array                          
	 */
	public function processAuthRequest(ControllerInterface $controller, string $auth_action): array;




	/**
	 * function to process search requests
	 * 
	 * @param  object $search_controller 
	 * @return array                          
	 */
	public function processSearchRequest(object $search_controller): array;

	


	/**
	 * function to send response in defined format
	 * by default is json format
	 * 
	 * @param  array $result 
	 * @return exit
	 */
	public function sendResponse(array $result);
	
}