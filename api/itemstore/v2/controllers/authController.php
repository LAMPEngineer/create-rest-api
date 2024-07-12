<?php
	use MyTraitController as MyTrait;

 /*
  *  Auth controller to have actions for items
  */
class AuthController extends MyController implements ControllerInterface, AuthInterface
{


	/**
	 * construct initialize db connection object
	 */
	public function __construct(ModelInterface $auth_model_interface)
	{
		//Get item model object
		$this->model = $auth_model_interface;
	}
	

	/**
	 * to get row count for a resource 
	 * @return int     row count
	 */
	public function getResultSetRowCount(): int
	{
		$num = 1;

/*		// call read action on model object
		$this->model->setId($this->id);

		// get row count
		 $num = $this->model->getResultSetRowCountById();*/

		return $num;
	}


	/**
	 * Action for GET verb to read an individual resource 
	 * 
	 * @return array   response
	 */
	function getAction(): array
	{
		$data = array('id'=>"Auth controller test");

		$response = array('message' => 'Auth resource - get one','status' => '1', 'data' => $data);

		return $response;
	}


	/**
	 * Action for GET verb to list resource 
	 * 
	 * @return array   response
	 */
	function getAllAction(): array
	{
	  // call read action on model object			
	  $data = array("id" => "Auth Testing");

	  $response = array('message' => 'Auth resource - get all','status' => '1', 'data' => $data);

	  return $response;	

	}



	/**
	 * Login action for POST verb. It generates 
	 * JWT token with user data.
	 * 
	 * @return array  generated token in 'jwt' key
	 */
	public function postLoginAction(): array
	{
		
		$users_controller = MyTrait::buildObject('Users');

		// set requested data to the controller 
		$users_controller->setData($this->data);

		// set requested format to the controller
		$users_controller->setFormat($this->format);

		$response = $users_controller->loginAction();

	    $user_data = $response['data'];

  		unset($response['data']); // unset user data from responce

  		// generate token
		$token = MyTrait::generateToken($user_data);

		$response['data']['jwt'] = $token;		

		return $response;
	}



	/**
	 * Read action for POST verb. To get jwt token, this method checks :
	 *  i.  post data and
	 *  ii. headers -> 'Authorization'
	 * 
	 * @return array 	keys - 'status', 'message' and 'user_data'
	 */
	public function postReadAction(): array
	{
	
		$response = MyTrait::readTokenFromHeadersOrPostData();
		
		return $response;
	}


}