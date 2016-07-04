<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

trait declare_controller
{

	static public function declare_controller()
	{
		$args = func_get_args();
		
		$file = $args[0];
		unset($args[0]);
		$controllers = array_values($args);
		
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