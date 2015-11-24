<?php

require('class.date.php');
require('class.inputvars.php');

session_start();

define('E1', '<b>Error</b> E1::1 el controlador no fue declarado [core#100]');
define('E2', '<b>Error</b> E2::2 no existe el archivo controlador correspondiente [core#099]');
define('E3', '<b>Error</b> E3::3 acción solicitada no disponible [core#098]');
define('E4', '<b>Error</b> E4::4 La funcion definida en el controlador no es calleable [core#097]');
define('E5', '<b>Error</b> E5::5 Declaraci&oacute;n de modelo inexistente [core#073]');
define('E6', '<b>Error</b> E6::6 EL COMPONENTE NO EXISTE [core#??]');

require('default_config/dbData.php');

require('default_config/tplData.php');

class _
{
    static public $db = null;
    static public $view = null;
    static public $models = null;
    static public $session = null;
    
    static public $post = array();
    static public $get = array();
    static public $request = array();
    static public $cookie = array();
    
    static protected $files = null;
    static protected $controllers = array();
    static protected $actions = null;
    static protected $footers = array();
    static protected $extras = array();
    static protected $loaded = array();
    
    static public function init($debug = true)
    {
        if($debug)
        {
            ini_set('display_errors', true);
        } else {
            ini_set('display_errors', false);
            error_reporting(0);
        }
        self::$view = self::declare_component('view'); // raintpl?
        self::$db = self::declare_component('db');
        self::declare_component('orm');
        self::$post = self::parse_post();
        self::$get = self::parse_get();
        self::$request = self::parse_request();
        self::$session = self::parse_session();
        self::$cookie = self::parse_cookie();
        
        self::load_requires();
    }
    
    static public function declare_controller($file, ...$controllers)
    {
        $file = strtolower(trim($file));
        if(!isset(self::$files[$file]))
            self::$files[$file] = true;
        foreach($controllers as $controller)
        {
            $controller = strtolower(trim($controller));
            self::$controllers[$controller] = $file;
        }
    }

    static public function declare_model($file)
    {
        $file = strtolower(trim($file));
        if(file_exists('models/'.$file.'.php'))
        {
            require_once('models/'.$file.'.php');
        } else die(E5.' models/'.$file.'.php');
    }
    
    static public function declare_extra($file)
    {
        if(!in_array($file, self::$extras))
        {
            self::$extras[] = $file;
            require_once('extras/'.$file.'.php');
        }
    }
    
    static public function declare_component($name)
    {
        if(file_exists('components/'.$name.'.php'))
        {
            return require_once('components/'.$name.'.php');
        } else if(file_exists('components/thirdparty/'.$name.'.php'))
        {
            return require_once('components/thirdparty/'.$name.'.php');
        } else die(E6.' components/thirdparty/'.$file.'.php');
    }
    
    static public function define_autocall($function)
    {
        $function();
    }
    
    static public function define_controller($action, $function)
    {
        self::$actions[$action] = $function;
    }
    
    static public function execute($action)
    {

        if(isset(self::$controllers[$action]))
        {
            if(file_exists('controllers/'.self::$controllers[$action].'.php'))
            {
                if(!in_array($action, self::$loaded))
                {
                    self::$loaded[] = $action;
                    require('controllers/'.self::$controllers[$action].'.php');
                }
                if(isset(self::$actions[$action]))
                {
                    $call = self::$actions[$action];
                    if(is_callable($call))
                    {
                        $call();
                        self::exec_footers();
                    } else die(E4.' '.htmlentities($action));
                } else die(E3.' '.htmlentities($action));
            } else die(E2.' '.htmlentities($action));
        } else die(E1.' '.htmlentities($action));
    }
    
    // alias of autocall, for backward compatibility reasons.
    static public function attach_header($function)
    {
        self::define_autocall($function);
    }
    
    // this is really interesting, i like delete this, but change my opinion.
    static private function exec_footers()
    {
        foreach(self::$footers as $footer)
        {
            $footer();
        }
    }
    
    static public function attach_footer($function)
    {
        self::$footers[] = $function;
    }
    
    static private function parse_session()
    {
        $out = array();
        if(is_array($_SESSION))
        {
            foreach($_SESSION as $key => $val)
            {
                $out[$key] = new sessionVar($key);
            }
        }
        return $out;
    }
    
    static public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    static private function parse_cookie()
    {
        // HAY QUE AGREGAR SOPORTE PARA ARREGLOS POR URL y POST
        $out = array();
        if(is_array($_COOKIE))
        {
            foreach($_COOKIE as $key => $val)
            {
                $out[$key] = new cookieVar($key);
            }
        }
        return $out;
    }
    
    static private function parse_post()
    {
        // HAY QUE AGREGAR SOPORTE PARA ARREGLOS POR URL y POST
        $out = array();
        if(is_array($_POST))
        {
            foreach($_POST as $key => $val)
            {
                if(is_array($val))
                {
                    foreach($val as $key2 => $val2)
                    {
                        $out[$key][$key2] = new postVar($key);
                    }
                } else $out[$key] = new postVar($key);
            }
        }
        return $out;
    }
    
    static private function parse_get()
    {
        // HAY QUE AGREGAR SOPORTE PARA ARREGLOS POR URL y POST
        $out = array();
        if(is_array($_GET))
        {
            foreach($_GET as $key => $val)
            {
                if(is_array($val))
                {
                    foreach($val as $key2 => $val2)
                    {
                        $out[$key][$key2] = new getVar($key);
                    }
                } else $out[$key] = new getVar($key);
            }
        }
        return $out;
    }
    
    static private function parse_request()
    {
        // HAY QUE AGREGAR SOPORTE PARA ARREGLOS POR URL y POST
        $out = array();
        if(is_array($_REQUEST))
        {
            foreach($_REQUEST as $key => $val)
            {
                if(is_array($val))
                {
                    foreach($val as $key2 => $val2)
                    {
                        $out[$key][$key2] = new requestVar($key);
                    }
                } else $out[$key] = new requestVar($key);
            }
        }
        return $out;
    }
    
    static public function factory($array, $pk, $class)
    {
        $out = array();
            foreach($array as $value)
            {
                $out[] = new $class($value[$pk]);
            }
        return $out;
    }
    
    static public function redirect($target, $internal = true)
    {
        if($internal)
        {
            self::execute($target);
        } else {
            header('Location: '.$target);
        }
    }
    
    static public function load_requires()
    {
        // deprecated :(
        //if(defined('REQUIRE_SEARCHER') and REQUIRE_SEARCHER == true) load_component('searcher');
        // for backward compatibility reasons:
        if(defined('REQUIRE_SEARCHER') and REQUIRE_SEARCHER == true)
            self::declare_component('searcher');
    }
    
}

spl_autoload_register(function($class){
            _::declare_model($class);
        });