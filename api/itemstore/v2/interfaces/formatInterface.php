<?php

interface FormatInterface
{

	/**
	 * Json view format
	 * @param  array $content 
	 * @return json       
	 */
	public function render($content);
	
}