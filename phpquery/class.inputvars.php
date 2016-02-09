<?php

if(!defined(PHPQUERY_LOADER)) {
	include('index.html');
	die();
}

class objectVar
{
    private $val = null;
    private $real_val = null;
    private $no_br = null;
    private $toOut = null;
    
    public function __construct($val)
    {
        $this->real_val = $val;
        $this->no_br = htmlentities($val);
        $this->val = nl2br($this->no_br);
        $this->toOut = $this->val;
    }
    
    public function __toString()
    {
        
        return $this->toOut;
    }
    
    public function real()
    {
        $this->toOut = $this->real_val;
        return $this;
    }
    
    public function entities()
    {
    	$this->toOut = htmlentities($this->toOut);
    	return $this;
    }
    
    public function noParseBR()
    {
        $this->toOut = $this->no_br;
        return $this;
    }
    
    public function parseBBC()
    {
        $this->toOut = str_replace('<br>','[br]',str_replace('<br />','[br]',$this->val));
        return $this;
    }
    
    public function upper()
    {
        $this->val = strtoupper($this->val);
        $this->real_val = strtoupper($this->real_val);
        $this->no_br = strtoupper($this->no_br);
        return $this;
    }
    
    public function lower()
    {
        $this->val = strtolower($this->val);
        $this->real_val = strtolower($this->real_val);
        $this->no_br = strtolower($this->no_br);
        return $this;
    }
    
    public function int()
    {
        return (int) $this->toOut;
    }
    
    public function len()
    {
        return strlen($this->val);
    }
    
    public function isEmail()
    {
        return filter_var($this->val, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function urldecode()
    {
        $this->toOut = urldecode($this->toOut);
        return $this;
    }
    
    public function urlencode()
    {
        $this->toOut = urlencode($this->toOut);
        return $this;
    }
    
    public function b64_d()
    {
        $this->toOut = base64_decode($this->toOut);
        return $this;
    }
    
    public function b64_e()
    {
        $this->toOut = base64_encode($this->toOut);
        return $this;
    }
    
    public function hash()
    {
        $this->toOut = password_hash($this->toOut, PASSWORD_DEFAULT);
        return $this;
    }
    
    public function md5()
    {
        $this->toOut = md5($this->toOut);
        return $this;
    }
    
    public function check($theHash)
    {
        return password_verify($this->toOut , $theHash);
    }
    
    public function seo()
    {
        // Tranformamos todo a minusculas
        $url = $this->toOut;
        $url = strtolower($url);
             
        //Rememplazamos caracteres especiales latinos
        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
        $repl = array('a', 'e', 'i', 'o', 'u', 'n');
        $url = str_replace ($find, $repl, $url);
             
        // Añaadimos los guiones
        $find = array(' ', '&', '\r\n', '\n', '+'); 
        $url = str_replace ($find, '-', $url);
             
        // Eliminamos y Reemplazamos demás caracteres especiales
        $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
        $repl = array('', '-', '');
        $url = preg_replace ($find, $repl, $url);
        $this->toOut = $url;
        return $this;
    }
    
    public function find($match, $type = FILTER_DEFAULT)
    {
        if($type == FILTER_DEFAULT)
            return strpos($this->toOut, $match);
        else if($type == USE_REGEX)
        {
            $matchs = array();
            $res = preg_match_all($match,$this->toOut,$matchs);
            if($res) return $matchs;
            return false;
        } else return $this;
    }
    
    public function filter($match, $type = FILTER_DELETE, $value = null)
    {
        if($type == FILTER_DELETE)
        {
            $this->toOut = str_replace($match, $value, $this->toOut);
            return $this;
        } elseif($type == FILTER_TO_EOL)
        {
            $in = strpos($this->toOut, $match);
            $this->toOut = substr($this->toOut, 0, $in);
            return $this;
        } elseif($type == FILTER_TO_SOL)
        {
            $in = strpos($this->toOut, $match);
            $this->toOut = substr($this->toOut, $in, $this->len());
            return $this;
        } elseif($type == USE_REGEX)
        {
            $this->toOut = preg_replace($match, $value, $this->toOut);
            return $this;
        } else return null;
    }
    
}

class postVar extends objectVar
{
    public function __construct($index)
    {
        parent::__construct($_POST[$index]);
    }
    
}

class getVar extends objectVar
{
    public function __construct($index)
    {
        parent::__construct($_GET[$index]);
    }
}

class requestVar extends objectVar
{
    public function __construct($index)
    {
        parent::__construct($_REQUEST[$index]);
    }
}

class sessionVar extends objectVar
{
    private $changed = false;
    private $index = null;
    
    public function __construct($index)
    {
        if(isset($_SESSION[$index]))
            parent::__construct($_SESSION[$index]);
        $this->index = $index;
    }
    
    public function set($value)
    {
        $this->toOut = $value;
        $this->changed = true;
        return $this;
    }
    
    public function destroy()
    {
        unset($_SESSION[$this->index]);
        return null;
    }
    
    public function __destruct()
    {
        if($this->changed !== false)
        {
            $_SESSION[$this->index] = $this->toOut;
        }
    }
}

class cookieVar extends objectVar
{
    private $changed = false;
    private $index = null;
    private $lifetime = null;
    
    public function __construct($index)
    {
        if(isset($_COOKIE[$index])) parent::__construct($_COOKIE[$index]);
        $this->index = $index;
    }
    
    public function set($value, _date $lifetime)
    {
        $this->toOut = $value;
        $this->changed = true;
        $this->lifetime = $lifetime;
        return $this;
    }
    
    public function destroy()
    {
        setcookie($this->index, null, time()-1);
        return null;
    }
    
    public function __destruct()
    {
        if($this->changed !== false)
        {
            setcookie($this->index, $this->toOut, $this->lifeTime->count());
        }
    }
}