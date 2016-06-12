<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

define('PHPQ_DEFAULT_VIEWS', DIR54.'/../'.coreData::$v);
define('PHPQ_DEFAULT_CACHE', DIR54.'/cache/');

class tplData
{
    static public $folder = null;
    static public $cache = true;
    static public $extension = '.tpl';
    static public $cacheDir = PHPQ_DEFAULT_CACHE;
	
	static public function getFolder()
	{
		if(empty($folder))
			return DIR54.'/../'.coreData::$v;
		return $folder;
	}
}