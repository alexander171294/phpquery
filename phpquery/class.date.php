<?php

if(!defined('PHPQUERY_LOADER')) {
	include('index.html');
	die();
}

class _date
{
    protected $time;
    protected $format = null;
    
    public function __construct($initial = 0)
    {
        if($initial < 1)
            $this->time = time();
        else
            $this->time = $initial;
    }
    
    public function seconds($cant)
    {
        $this->time += (int)$seconds;
        return $this;
    }
    
    public function fSecond($limitter=null)
    {
        $this->format .= date('s',$this->time).$limitter;
        return $this;
    }
    
    public function minutes($cant)
    {
        $this->time += 60*((int)$cant);
        return $this;
    }
    
    public function fMinute($limitter=null)
    {
        $this->format .= date('i',$this->time).$limitter;
        return $this;
    }
    
    public function hours($cant)
    {
        $this->time += 60*60*((int)$cant);
        return $this;
    }
    
    public function fHour($limitter=null)
    {
        $this->format .= date('H',$this->time).$limitter;
        return $this;
    }
    
    public function days($cant)
    {
        $this->time += 60*60*24*((int)$cant);
        return $this;
    }
    
    public function fDay($limitter = null)
    {
        $this->format .= date('d',$this->time).$limitter;
        return $this;
    }
    
    public function weeks($cant)
    {
        $this->time += 60*60*24*7*((int)$cant);
        return $this;
    }
    
    public function fortnights($cant)
    {
        $this->time += 60*60*24*15*((int)$cant);
        return $this;
    }
    
    public function months($cant)
    {
        $this->time += 60*60*24*30*((int)$cant);
        return $this;
    }
    
    public function fMonth($limitter=null)
    {
        $this->format .= date('m',$this->time).$limitter;
        return $this;
    }
    
    public function years($cant)
    {
        $this->time += 60*60*24*365*((int)$cant);
        return $this;
    }
    
    public function fYear($limitter=null)
    {
        $this->format .= date('Y',$this->time).$limitter;
        return $this;
    }
    
    public function count()
    {
        return $this->time;
    }
    
    public function format()
    {
        return $this->format;
    }
}