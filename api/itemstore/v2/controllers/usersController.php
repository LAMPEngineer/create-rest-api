<?php
	use MyTraitController as MyTrait;

/*
 *  Items controller to have actions for items
 */

class UsersController extends MyController implements ControllerInterface
{

	protected $controller;
	
	/**
	 * construct initialize db connection object
	 */
	public function __construct(ModelInterface $users_model_interface)
	{
		//Get item model object
		$this->model = $users_model_interface;

		$this->controller = 'users';
	}


	/**
	 * function to check email id, if not present
	 * sends back the email else theow error
	 * 
	 * @param  string $email 
	 * @return string email  
	 */
	protected function checkForEmailId(string $email): string
	{
		if(($this->model->checkEmail($email)) == 0)
		{			
			return $email;

		} else {

			MyTrait::throwError('0', 'Email id already exists.');
		}		
	}



	/**
	 * Login action for POST verb. It checks for 
	 * all use-cases and return responces accordingly
	 * 
	 * @return array response with user data  
	 */
	public function loginAction(): array
	{
		$data = (object)$this->data;

		if(!empty($data->email) && !empty($data->password)){

			if($this->model->checkEmail($data->email) != 0){

			  	 // validation
			    $setter_value = $this->validateParameter('email', $data->email, false);
			  	
			    // values to model
			    $this->model->setEmail($setter_value);
			
				// call insert action on model object		
				$user_data = (object)$this->model->login();

				if(password_verify($data->password, $user_data->password)){			

					$user_data_arr = array('id' => $user_data->id, 'name' => $user_data->name, 'email' => $user_data->email);
					
					$response = array('message' => 'Login successful', 'status' => '1', 'data' => $user_data_arr);

				}else{
					MyTrait::throwError('0', 'Invalid credentials');
				}

			}else{
				MyTrait::throwError('0', 'Invalid credentials');
			}

		}else{
			MyTrait::throwError('0', 'All data needed');
		}

		return $response;
	}


	public function logoutAction()
	{
		$data = $this->data;

		$response = array('message' => 'Auth - Post Logout *** Action', 'status' => '1', 'data' => $data);

		return $response;
	}
	
}