<?php

interface ControllerInterface
{

	// setter methods
	public function setData($data);
	public function setFormat($format);
	public function setId($id);


	/**
	 * Action for GET verb to list resource 
	 * 
	 * @return array   response
	 */
	function getAllAction(): array;


	/**
	 * Action for GET verb to read an individual resource 
	 * 
	 * @return array   response
	 */
	function getAction(): array;


	/**
	 * Action for POST verb to create an individual resource 
	 * 
	 * @return array   response
	 */
	function postAction(): array;


	/**
	 * Action for PUT verb to update an individual resource 
	 * 
	 * @return array   response
	 */
	function putAction(): array;


	/**
	 * Action for PATCH verb to update an individual resource 
	 * 
	 * @return array   response
	 */
	function patchAction(): array;


	/**
	 * Action for DELETE verb to delete an individual resource 
	 * 
	 * @return array     response
	 */
	function deleteAction(): array;


}