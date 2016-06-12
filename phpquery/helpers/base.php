<?php

console::setARGV($argv);

class console
{
    
    static protected $argv = array();
    
    static public function setARGV($argv)
    {
        self::$argv = $argv;
    }
    
    static public function earg($name)
    {
        return in_array($name, self::$argv);
    }
    
    static public function get_arg($name)
    {
        $id = array_search($name, self::$argv);
        if($id>0){
            return self::$argv[$id+1];
        }
    }
    
    static public function write($msg, $eol = true){
        $msg = $eol ? $msg.PHP_EOL : $msg;
        echo $msg;
        return true;
    }
    
    static public function read($str = null)
    {
        if(!empty($str)) self::write($str.' ', false);
        return trim(fgets(STDIN));
    }
}

class DB{
    
    static public $link = null;
    static protected $dbhost = null;
    static protected $dbuser = null;
    static protected $dbpass = null;
    static protected $dbname = null;
    
    static function init($dbhost, $dbuser, $dbpass, $dbname)
    {
        self::$link = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        self::$dbhost = $dbhost;
        self::$dbuser = $dbuser;
        self::$dbpass = $dbpass;
        self::$dbname = $dbname;
    }
    
    static function init_rescue()
    {
        self::$link = new mysqli(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname);
    }
    
    static function query($query)
    {
        self::init_rescue();
        return self::$link->query($query);
        self::end();
    }
    
    static function fetch($q)
    {
        return $q->fetch_array();
    }
    
    static function fetchAll($q)
    {
        $out = array();
        while($data = $q->fetch_array())
        {
            $out[] = $data;
        }
        return $out;
    }
    
    static function end()
    {
        self::$link->close();
    }
    
    static function escape($str)
    {
        return self::$link->escape_string($str);
    }
    
    static function last()
    {
        return self::$link->insert_id;
    }
    
    static function error()
    {
        return self::$link->error;
    }
}
