<?php

define('PHPQ_DEFAULT_VIEWS', DIR54.'/../views/');
define('PHPQ_DEFAULT_CACHE', DIR54.'/cache/');

class tplData
{
    static public $folder = PHPQ_DEFAULT_VIEWS;
    static public $cache = true;
    static public $extension = '.tpl';
    static public $cacheDir = PHPQ_DEFAULT_CACHE;
}