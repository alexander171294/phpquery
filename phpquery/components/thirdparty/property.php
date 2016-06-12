<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

trait Property // mi hermosa clase property
{
    // llamando a funciones setters
    Public function __set($property, $value)
    {
        if(is_callable(array($this, 'set_'.$property), $value)) 
            return call_user_func(array($this, 'set_'.$property), $value); 
        else 
        // no hay funcion setter para este atributo (o no existe el atributo)
            throw new exception('The atribute $'.$property.' not exist'); 
    }
    
    // llamando a funciones getters
    Public function __get($property)
    {
        if(is_callable(array($this, 'get_'.$property))) 
            return call_user_func(array($this, 'get_'.$property)); 
        else 
        // no hay funcion getter para este atributo (o no existe el atributo)
            throw new exception('The atribute $'.$property.' not exist');  
    }
}