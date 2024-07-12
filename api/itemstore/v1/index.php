<?php
/**
 *  Request handler that route request to right place.
 *  It calls action of the controller and return back
 *  response in right format as requested.
 *  
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


//autoload 
include (__DIR__ . "/autoload.php");

// request object
$request = new RequestController();


// check request URL
if(!isset($request->url_elements[5]) or $request->url_elements[5] ==''){
	// redirect to root
	header("Location: ../../../");
}

// route the request to the right place
$controller_name = ucfirst($request->url_elements[5]) . 'Controller';

if(class_exists($controller_name)){
	
	$controller = new $controller_name();
	
	// call action
	$result = $controller->processRequest($request);

	// view format
	$view_name = ucfirst($request->format) . 'View';

	if(class_exists($view_name)){

		$view = new $view_name();
		$view->render($result);		
	}

}

