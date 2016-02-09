<?php

define('PHPQUERY_LOADER', true);

$version_array = array('version_tag' => '1.0-RC2', 'version_value' => 102);
require('version.php');

// check requeriments
// check version
if(version_compare(PHP_VERSION, '5.4.0', '<')){
	die('<b>FATAL ERROR</b> PHPQuery require PHP_VERSION >= 5.4.0, please update PHP! - current version: '.PHP_VERSION);
} else if(version_compare(PHP_VERSION, '5.6.0', '<')){
	// traits for php > 5.4 && php < 5.6
	if(defined('PHPQUERY54') && PHPQUERY54 == true)
	{
		require('compatibility/kernel_54/declare_controller.php');
	} else {
		die('<b>FATAL ERROR</b> PHPQuery require PHP_VERSION >= 5.6.0, please update PHP!, or enable PHPQUERY54 (warning: PHPQuery FOR PHP 5.4 IS EXPERIMENTAL) - current version: '.PHP_VERSION);
	}
} else if(version_compare(PHP_VERSION, '5.6.0', '>=')){
	// traits for php >= 5.6
	require('compatibility/kernel_56/declare_controller.php');
}

// support __DIR__ concatenation in php 5.4
define('DIR54', __DIR__);

// check permissions
//...
// validate core structure 
try {
	if(!file_exists(__DIR__.'/default_config/coreData.php')) throw new Exception('<b>FATAL ERROR</b> default_config/coreData.php is\'nt exists');
	if(!file_exists(__DIR__.'/kernel.php')) throw new Exception('<b>FATAL ERROR</b> kernel.php is\'nt exists');
	if(!file_exists(__DIR__.'/default_config/dbData.php')) throw new Exception('<b>FATAL ERROR</b> default_config/dbData.php is\'nt exists');
	if(!file_exists(__DIR__.'/default_config/tplData.php')) throw new Exception('<b>FATAL ERROR</b> default_config/tplData.php is\'nt exists');
	if(!file_exists(__DIR__.'/class.date.php')) throw new Exception('<b>FATAL ERROR</b> class.date.php is\'nt exists');
	if(!file_exists(__DIR__.'/class.inputvars.php')) throw new Exception('<b>FATAL ERROR</b> class.inputvars.php is\'nt exists');
} catch(Exception $e)
{
	die($e->getMessage());
}

// Load files //

// Default critical config
require('default_config/coreData.php');
// Load kernel
require('kernel.php');
// start stats
_::set_time();
// Default configs
require('default_config/dbData.php');
require('default_config/tplData.php');
// Core classes
require('class.date.php');
require('class.inputvars.php');