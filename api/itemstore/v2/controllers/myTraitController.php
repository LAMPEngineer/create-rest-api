<?php

use \Firebase\JWT\JWT;
use ContainerController as Container;


trait MyTraitController
{
	/**
	 * method to build controller object
	 * 
	 * @param  string $action_name      the controller
	 * @return object              		controller object
	 */

	public function buildObject(string $action_name): object
	{

		$controller_name = $action_name . 'Controller';

		if(class_exists($controller_name)){
			
			// PDO db object
			$db = Container::get('DatabaseConfig');
			$conn = $db->connect();

			// model object
			$model_name = $action_name . 'Model';

			$model = Container::get($model_name, $conn);


			//$controller object 
			$controller = Container::get($controller_name, $model);

			return $controller;
		}

	}

	public function readTokenFromHeadersOrPostData(): array
	{		  
		$request = Container::get('requestController');
		$data = (object)$request->parameters; // to check post data

		$response = (!empty($data->jwt))? self::readJWTTokenGetUserData($data->jwt) : self::readHeaderGetUserDataFromJWT();
		
		return $response;
	}

	/**
	 *  private method to read JWT token and get user data
	 *  
	 * @param  string $data_jwt 	JWT token
	 * @return array    keys - 'status', 'message' and 'user_data'      		
	 */
	private function readJWTTokenGetUserData($data_jwt): array
	{
		$response = self::readToken($data_jwt);
		$response['message'] .= ' from post data';
		return $response;
	}

	/**
	 * Method to read headers and get user data from JWT.
	 * If error, it sends responces with:
	 * keys - 'status', 'message'
	 *
	 * 
	 * @return array  		keys - 'status', 'message' and 'user_data'
	 * 	 
	 */
	public function readHeaderGetUserDataFromJWT(): array
	{
		$all_headers = getallheaders();
		if(!empty($all_headers['Authorization'])) {			

			$jwt_token = $all_headers['Authorization'];
			$response = self::readToken($jwt_token);
			$response['message'] .= ' from headers'; 			

			} else {
				self::throwError('0', "ERROR: cann't read jwt token");
			}

			return $response;
	}


	/**
	 * Generates token with user data using Firebase JWT
	 * encode method. It gets JWT constants from config.
	 * 
	 * 
	 * @param  array $user_data		    user data to generate JWT 
	 * @return string $token 	        generated token           
	 */
	public function generateToken(array $user_data): string
	{

		$iss = env('JWT_ISS');
		$iat = env('JWT_IAT');
		$nbf = $iat + env('JWT_NBF');
		$exp = $iat + env('JWT_EXP');
		$aud = env('JWT_AUD');

		$payload_info = array(
				"iss"  => $iss,
				"iat"  => $iat,
				"nbf"  => $nbf,
				"exp"  => $exp,
				"aud"  => $aud,
				"data" => $user_data
			);

		$secret_key = env('JWT_SECRET');
		$algo = env('JWT_ALGO');

		$token =  JWT::encode($payload_info, $secret_key, $algo); // JWT encode method

		return $token;
	}


	/**
	 * Methot to read JWT token. It reads config to get JWT constants.
	 * 
	 * @param  array $jwt_token	JWT token  
	 * @return array            keys - 'status', 'message' and 'user_data'
	 */
	public function readToken($jwt_token): array
	{

		try{
			$secret_key = env('JWT_SECRET');
			$algo = env('JWT_ALGO');
			
			
			JWT::$leeway = 60; // to fix - 'Cannot handle token prior to 2020-07-28T13:04:20+0200'

			//JWT decode method
			$decoded_data = JWT::decode($jwt_token, $secret_key, array($algo));
			$data = $decoded_data->data; // user data
			
			$response = array('message' => 'Read jwt token successfully','status' => '1', 'data' => $data);

		}catch(Exception $ex){
			self::throwError('0', $ex->getMessage());
		}

		return $response;

	}



	/**
	 * To throw error if occure. It uses view format of request object.
	 * It sends responces with:
	 * keys - 'status', 'message'
	 * 
	 * 
	 * @param  string $code    		status code to return
	 * @param  string $message 		message to return
	 * 
	 * @return exit         
	 */
	public function throwError($code, $message): void
	{
		$content = array('message' => $message,'status' => $code);

		$request = Container::get('requestController');
		// view format
		$view_name = ucfirst($request->format) . 'View';

		if(class_exists($view_name)){

			$view = Container::get($view_name);
			$view->render($content);		
		}

		exit;		
	}

	
}