<?php

// check requeriments

// check version
if(version_compare(PHP_VERSION, '5.5.0', '<')) die('<b>FATAL ERROR</b> PHPQuery require PHP_VERSION >= 5.5.0, please update PHP! - current version: '.PHP_VERSION);
// check permissions
//...
// validate core structure 
//...

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