<?php

define('DEVMODE', true);

define('REQUIRE_SEARCHER', true);
require('phpquery/core.php'); // el framework que es la puta hostia

dbData::$host = 'localhost';
dbData::$user = 'root';
dbData::$pass = '';
dbData::$db = 'adminwp';

//tplData::$extension = '.html';

// iniciamos el framework
_::init(DEVMODE);

// declaramos controladores
_::declare_controller('general', 'home', 'login', 'login2', 'registro', 'registro2', 'recordar_pass', 'recordar_pass2'); // en general.php el controlador home
_::declare_controller('gestion_wordpress', 'listado', 'add_folder', 'add_wordpress');
//_::declare_controller('cron', 'cron', 'sitemap');
//_::declare_controller('ajax', 'none');

// REQUERIMOS TODAS LAS FUNCIONES QUE SE EJECUTARAN EN EL HEADER
require('others/headers.php');

// REQUERIMOS TODAS LAS FUNCIONES QUE SE EJECUTARAN EN EL FOOTER
require('others/footer.php');

// ejecutamos el controlador correspondiente (usando variable action para saber cual)
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
_::execute($action);

_::$view->execute();