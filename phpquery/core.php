<?php

_::set_time();

require('class.date.php');
require('class.inputvars.php');

session_start();

define('E1', 'The controller hasn\'t is declared ::');
define('E2', 'The file of the controller isn\'t exists ::');
define('E3', 'The request action isn\'t exists ::');
define('E4', 'The function defined on the controller is not callable ::');
define('E5', 'Declaration of non-existent model ::');
define('E6', 'Failed to load component ::');
define('E7', 'define_autocall, the function isn\'t callable ::');
define('E8', 'attach_footer, the function isn\'t callable ::');

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
    
    // statics of use
    static protected $time = array();
    static protected $memory = array();
    
    static public function init($debug = true)
    {
        if($debug)
        {
            ini_set('display_errors', true);
        } else {
            ini_set('display_errors', false);
            error_reporting(0);
        }
        self::declare_component('error');
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
        } else _error_::set(E5.' models/'.$file.'.php', LVL_FATAL);
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
        if(file_exists(__DIR__.'/components/'.$name.'.php'))
        {
            return require_once(__DIR__.'/components/'.$name.'.php');
        } else if(file_exists(__DIR__.'/components/thirdparty/'.$name.'.php'))
        {
            return require_once(__DIR__.'/components/thirdparty/'.$name.'.php');
        } else _error_::set(E6.' components/thirdparty/'.$name.'.php', LVL_FATAL);
    }
    
    static public function define_autocall($function, $calculate_costs = false)
    {
        if($calculate_costs) {
            self::set_time('autocall');
        }
        if(is_callable($function))
        {
            $function();
            if($calculate_costs)
                echo 'COST OF AUTOCALL: '.self::get_cost('autocall');
        }
        else _error_::set(E7, LVL_FATAL);
    }
    
    static public function define_controller($action, $function, $calculate_costs = false)
    {
        if($calculate_costs) {
            self::set_time('controller_'.$action);
        }
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
                        // si exigimos calcular los costos
                        if(self::saved_costs('controller_'.$action))
                            echo 'COST OF CONTROLLER '.$action.': '.self::get_cost('controller_'.$action);
                        self::exec_footers();
                    } else _error_::set(E4.' '.htmlentities($action), LVL_FATAL);
                } else _error_::set(E3.' '.htmlentities($action), LVL_FATAL);
            } else _error_::set(E2.' '.htmlentities($action), LVL_FATAL);
        } else _error_::set(E1.' '.htmlentities($action), LVL_WARNING);
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
        if(is_callable($function))
            self::$footers[] = $function;
        else _error_::set(E8, LVL_FATAL);
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
    
    static public function set_time($index = 'coreMain')
    {
        self::$time[$index] =  microtime(true);
        self::$memory[$index] = memory_get_usage();
    }
    
    static public function get_cost($index = 'coreMain')
    {
        return number_format(microtime(true) - self::$time[$index], 4).' seconds of execution; '.self::formatbytes(memory_get_usage() - self::$memory[$index]).' used in memory';
    }
    
    static public function saved_costs($index = 'coreMain')
    {
        return isset(self::$time[$index]);
    }
    
    static private function formatbytes($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

spl_autoload_register(function($class){
            _::declare_model($class);
        });