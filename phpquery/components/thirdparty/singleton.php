<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

trait Singleton
{

    static private $instancia = null;
    
    static public function GetInstancia()
    {
    	$className = __CLASS__;
        if(!(self::$instancia instanceof $className))
        {
            self::$instancia = new $className;
        }
        return self::$instancia;
    }
    
    public function __clone()
    {
        trigger_error('No es posible realizar la clonación', E_USER_ERROR);
    }

}