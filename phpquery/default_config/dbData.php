<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

class dbData
{
    static public $host = 'localhost';
    static public $user = 'root';
    static public $pass = null;
    static public $db = null;
}