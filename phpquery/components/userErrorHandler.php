<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

class _e
{
	static protected $errors = array();
	static protected $language = null;
	
	static public function set($errorKey, $errorMessage)
	{
		self::$errors[$errorKey] = array('code' => count(self::$errors), 'key' => $errorKey, 'message' => $errorMessage);
	}
	
	static public function get($errorKey)
	{
		$errorMessage = self::getTranslated($errorKey);
		// si no hay en idioma usamos el predefinido
		if(empty($errorMessage)) $errorMessage = self::$errors[$errorKey]['message'];
		return json_encode(array('error'=>true, 'code' => self::$errors[$errorKey]['code'], 'key' => $errorKey, 'message' => $errorMessage));
	}
	
	static public function getTranslated($errorKey)
	{
		$out = null;
		if(is_array(self::$language) && isset(self::$language[$errorKey]))
			$out = self::$language[$errorKey];
		return $out;
	}
	
	public function loadLanguage($file)
	{
		self::$language = get_object_vars(json_decode(file_get_contents($file)));
	}
}