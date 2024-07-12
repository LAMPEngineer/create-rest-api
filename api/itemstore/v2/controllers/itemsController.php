<?php
	use MyTraitController as MyTrait;
	
/*
 *  Items controller to have actions for items
 */

class ItemsController extends MyController implements ControllerInterface
{
	
	protected $controller;

	/**
	 * construct initialize db connection object
	 */
	public function __construct(ModelInterface $items_model_interface)
	{
		//Get item model object
		$this->model = $items_model_interface;
		$this->controller = 'items';
	}


	/**
	 * method to get user id from JWT token and call the model 
	 * setter to set user_id for created_by or updated_by field
	 *
	 * 
	 * @param  string $setter 		name of setter 
	 * @return void        
	 */
	protected function callSetter($setter) : void
	{
		// read jwt token from header
		$response = (object)MyTrait::readHeaderGetUserDataFromJWT();

		if($response->status == '1' ){
			$user_id = $response->data->id;

			$this->model->$setter($user_id);
		}

	}



}