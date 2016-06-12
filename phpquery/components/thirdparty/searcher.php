<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

/** Código BuscadorClass
 *  @ Author: Alexander Eberle <alexander171294@live.com>
 *  @ Date: 17/10/13 - 22/06/15
 *  @ Status: Prototype
 *  @ Version: 2.0
 */

define('BUSCADOR_TEXTO_PEQUENIO', 111);
define('BUSCADOR_PALABRAS_PEQUENIAS', 112);
define('BUSCADOR_MUCHAS_PALABRAS', 113);

class Buscador {
    
    protected $texto = null;
    protected $words = array();
    protected $max_process_words = 10; // cantidad máxima de palabras a procesar
    protected $limit_querys = 20; // cantidad de querys máximas a procesar
    protected $delimiter = '%';
    protected $results = array();
    
    public $error = false;
    public $error_id = 0;
    
    public function __construct($texto)
    {
        try
        {
            if(strlen($texto) < 4) throw new \Exception(BUSCADOR_TEXTO_PEQUENIO);
            $this->texto = $texto;
        } catch(\Exception $e)
        {
            $this->error = true;
            $this->error_id = $e->getMessage();
        }
        
    }
    
    public function getQuerys()
    {
        try
        {
            $this->parseInput(); // filtramos texto
            $this->parseWords(); // separamos palabras
            $this->filterWords(); // quitamos palabras chicas y pasamos por strtolower
            $this->orderWords(); // ordenamos las palabras
            return $this->generateQuerys(); // generamos las consultas  
        } catch(\Exception $e)
        {
            $this->error = true;
            $this->error_id = $e->getMessage();
        }
        
    }
    
    public function filterQuerys($primaryKey, $limit = false)
    {
        $limit = ($limit === false) ? $this->limit_querys : $limit;
        $out = array();
        for($i = 0; $i<count($this->results) and $i<$limit; $i++)
        {
            for($z = $i+1; $z<count($this->results) and $z<$limit; $z++)
            {
                if($this->results[$i][$primaryKey] == $this->results[$z][$primaryKey]) // si esta repetido
                {
                    unset($this->results[$i]);
                    // volvemos a ordenar el arreglo
                    $this->results = array_values($this->results);
                }
            }
            $out[$i] = $this->results[$i];
        }
        return $out;
    }
    
    public function merge($results)
    {
        if(!empty($results) and $results !== array() and $results !== false and count($results) > 0)
        {
            $this->results = array_merge($this->results, $results);
        }
    }
    
    private function parseInput()
    {
        $this->texto = urldecode($this->texto);
        $this->texto = str_replace('-', ' ', $this->texto);
        $this->texto = str_replace('+', ' ', $this->texto);
        $this->texto = preg_replace('([^A-Za-z0-9\s])', null, $this->texto);
        if(strlen($this->texto) < 4) throw new \Exception(BUSCADOR_TEXTO_PEQUENIO);
    }
    
    private function parseWords()
    {
        $this->words = explode(' ',$this->texto);
    }
    
    private function filterWords()
    {
        $out = array();
        foreach($this->words as $word)
        {
            if(strlen($word)>3)
                $out[] = strtolower($word);
        }
        $this->words = $out;
        if(count($this->words) < 1) throw new \Exception(BUSCADOR_PALABRAS_PEQUENIAS);
    }
    
    private function orderWords()
    {
        if(count($this->words) > $this->max_process_words) throw new \Exception(BUSCADOR_MUCHAS_PALABRAS);
        $words = array();
        $fwords = array();
        $out = array();
        $aux = array();
        for($i = 0; $i<count($this->words); $i++)
            if(isset($words[$this->words[$i]]))
                $words[$this->words[$i]]++;
            else
                $words[$this->words[$i]] = 1;
        // numeramos los indices
        foreach($words as $word => $value)
        {
            $fwords[] = array('palabra' => $word, 'repeticiones' => $value);
        }
        $out = array();
        for($i = 0; $i<count($fwords); $i++)
            for($i2 = $i; $i2<count($fwords); $i2++)
                if(!isset($out[$i]) OR ($out[$i]['repeticiones'] < $fwords[$i2]['repeticiones']))
                {
                    $aux = isset($out[$i]) ? $out[$i] : null;
                    $out[$i] = $fwords[$i2];
                    $fwords[$i2] = $aux;
                }
        $words = $out;
        $out = array();
        foreach($words as $word)
        {
            $out[] = $word['palabra'];
        }
        $this->words = $out;
    }
    
    private function generateQuerys()
    {
        if(count($this->words) > $this->max_process_words) throw new \Exception(BUSCADOR_MUCHAS_PALABRAS);
        $querys = array();
        // para la derecha
        for($i = 0; $i<count($this->words); $i++)
        {
            $queryClean = null;
            for($i2 = $i; $i2<count($this->words); $i2++)
            {
                $queryClean .= $this->words[$i2].' ';
            }
            $querys[] = $this->delimiter.trim($queryClean).$this->delimiter;
        }
        // para la izquierda
        for($i = count($this->words)-1; $i>=0; $i--)
        {
            $queryClean = null;
            for($i2 = $i; $i2>=0; $i2--)
            {
                $queryClean .= $this->words[$i2].' ';
            }
            $querys[] = $this->delimiter.trim($queryClean).$this->delimiter;
        }
        // ordenamos por mallor palabra
        $aux = null;
        for($i = 0; $i<count($querys); $i++)
            for($z = $i; $z<count($querys); $z++)
                if(strlen($querys[$i]) < strlen($querys[$z]))
                {
                    $aux = $querys[$i];
                    $querys[$i] = $querys[$z];
                    $querys[$z] = $aux;
                }
        return $querys;
    }
    
    
}
