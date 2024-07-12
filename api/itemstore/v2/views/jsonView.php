<?php
/**
 *  Json view 
 */
class JsonView implements FormatInterface
{
	/**
	 * Json view format
	 * @param  array $content 
	 * @return json       
	 */
	public function render($content)
	{
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json charset=utf8');
		echo json_encode($content);
		return true;
	}
}