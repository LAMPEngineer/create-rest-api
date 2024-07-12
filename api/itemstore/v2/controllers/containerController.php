<?php 

/*
 *  Container controller provides actions to:
 *  
 *  i.  get object of a class
 *  ii. checks if container could return an object for a class
 *  
 */
class ContainerController implements ContainerInterface
{

	/**
	 *   creates object of a class, if class has 
	 *   dependent on other object that need to pass 
	 *   
	 * @param  [string] $class_name       class to cteate object
	 * @param  [object] $dependent_object optional
	 * @return [object] $class_object     class object                
	 */
	public static function get($class_name, $dependent_object=null): object
	{

		$class_object = new $class_name($dependent_object);


		return $class_object;

	}

	/**
	 * checks if container could return an object for a class
	 * 
	 * @param  [string]  $class_name  class to check
	 * @return boolean               
	 */
	public static function has($class_name): bool
	{

		return (class_exists($class_name)) ? true : false;

	}

}