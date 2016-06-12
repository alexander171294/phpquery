<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

class errorData
{
    static public $die_on_lvl = 75;
    // callback set function called in error
    static public $callback = null;
}