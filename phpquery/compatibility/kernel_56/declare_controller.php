<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

trait declare_controller
{
	
	static public function declare_controller($file, ...$controllers)
	{
		$file = strtolower(trim($file));
		if(!isset(self::$files[$file]))
			self::$files[$file] = true;
			foreach($controllers as $controller)
			{
				$controller = strtolower(trim($controller));
				self::$controllers[$controller] = $file;
			}
	}
	
}