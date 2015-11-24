<?php

// PHPQuery examples :)
// you can write this code in the way you want, it is very flexible

// first define DEVELOPER MODE, if the constant get value true, the framework show errors and remake the cache of tpl always.
// if the constant get value false, the framework DON'T SHOW ERRORS, and don't remake cache of tpls (only if there are not or you delete old cache)
define('DEVMODE', true);

// this is obsolete after version 1.0.1
// converted in _::declare_component('searcher');
define('REQUIRE_SEARCHER', true);
// now, require the core of PHPQuery, this is only line necessary
require('phpquery/core.php');

// this set new values for the default configuration of DB
// see default config in phpquery/default_config/dbData.php
dbData::$host = 'localhost';
dbData::$user = 'root';
dbData::$pass = '';
dbData::$db = 'adminwp';

// if you don't like .tpl extension in views, you set do you want
// you see default config in phpquery/default_config/tplData.php
//tplData::$extension = '.html';

// initialize the framework using debug mode (you can change the constant if like)
_::init(DEVMODE);

// the important, you need declare the controllers that exist
// first declare file (example.php), later you declare the controllers in the file
_::declare_controller('example', 'example', 'example_2', 'example_3', 'example_4'); // en general.php el controlador home
_::declare_controller('real_example', 'home', 'login', 'login2', 'registro', 'registro2');


// if you like add others definitions and declarations in external file
// you can require or include others files and declare it
require('others/headers.php');

// REQUERIMOS TODAS LAS FUNCIONES QUE SE EJECUTARAN EN EL FOOTER
require('others/footer.php');

// then set the variable for select controller (in this case "action")
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
// execute the action (that is, call controller set in $action if exist)
_::execute($action);
// to end, show all views seted using _::$view->show();
_::$view->execute();