<?php // default deep changed

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
    
    static public function set($msg, $error_lvl, $deep = 2)
    {
        if(self::is_stop($error_lvl))
        {
            die(self::format_message($msg, self::get_real_line($deep), $error_lvl, self::get_trace($deep)));
        } else if(_::isDebug()) echo self::format_message($msg, self::get_real_line($deep), $error_lvl, self::get_trace($deep));
    }
    
    static private function is_stop($lvl)
    {
        return $lvl>70;
    }
    
    static private function format_message($msg, $line, $elvl, $trace)
    {
        return '<h1>Error</h1> <h4>Trace:</h4>'.
                $trace.
                ' <b>Error:</b> '.$msg.
                ' in line <b>'.$line.'</b> '.
                ' error level '.$elvl.'/100 <br /><br />';
    }
    
    static public function get_trace($deep)
    {
        $trace = debug_backtrace();
        $endTrace = null;
        if(count($trace)>$deep)
        {
            for($i = $deep; $i<count($trace); $i++)
            {
                $endTrace = '[*] <b>'.$trace[$i]['file'].'</b>, line <b>'.$trace[$i]['line'].'</b>, function <i>'.$trace[$i]['function'].'()</i> <br />'.PHP_EOL.$endTrace;
            }
            $endTrace .= '[!] ';
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