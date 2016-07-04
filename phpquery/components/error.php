<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

// errors levels
define('LVL_INFO', 10);
define('LVL_E404', 100);
define('LVL_WARNING', 40);
define('LVL_FATAL', 100);
define('LVL_CRASH', 110);

// class for control errors!
class _error_
{
    static protected $lastError = array();
    
    static public function set($msg, $error_lvl, $deep = 3)
    {
        if(self::is_stop($error_lvl))
        {
            die(self::format_message($msg, self::get_real_line($deep), $error_lvl, self::get_trace($deep)));
        } else echo self::format_message($msg, self::get_real_line($deep), $error_lvl, self::get_trace($deep));
    }
    
    static private function is_stop($lvl)
    {
        return $lvl>70;
    }
    
    static private function format_message($msg, $line, $elvl, $trace)
    {
        return '<h1>Error</h1> '.
                $msg.
                ' in line <b>'.$line.'</b> '.
                ' error level '.$elvl.'/100 <br /><br />'.
                $trace;
    }
    
    static public function get_trace($deep)
    {
        $trace = debug_backtrace();
        $endTrace = null;
        if(count($trace)>$deep)
        {
            for($i = $deep; $i<count($trace); $i++)
            {
                $endTrace .= 'File: <b>'.$trace[$i]['file'].'</b>, line '.$trace[$i]['line'].', function '.$trace[$i]['function'].' <br />';
            }
            return $endTrace;
        }
        return null;
    }
    
    static public function get_real_line($deep)
    {
        $trace = debug_backtrace();
        if(count($trace)>$deep) return $trace[$deep]['line'];
        return '0';
    }
}