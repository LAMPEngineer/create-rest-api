<?php
	
/*
 *  Search controller 
 */

class SearchController 
{

	/**
	 * to hold action controller object
	 * @var object
	 */
	private $action_controller;


	
	/**
	 * initialize controller object
	 * 
	 * @param ControllerInterface $action_controller_object - controller injection - action controller of type ControllerInterface
	 * 
	 */
	public function __construct(ControllerInterface $action_controller_object)
	{
		$this->action_controller = $action_controller_object;
	}




	/**
	 * index Action 
	 * 
	 * @return array - the search result
	 */
	public function indexAction(): array
	{
		$response = $this->action_controller->getAllAction();

		return $response;
	}


}