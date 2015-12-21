<?php

define('eTPL1', 'eTPL1::1 don\'t exists view ');
define('eTPL2', 'eTPL2::2 you can\'t name a variable with void string');

class template
{
    protected $includes = array();
    protected $values = array();
    public $isAjax = false;
    
    
    public function show($file)
    {
        if(!file_exists(tplData::$folder.$file.tplData::$extension)) _error_::set(eTPL1.$file.tplData::$extension, LVL_FATAL);
        else $this->includes[] = array('source' => tplData::$folder.$file.tplData::$extension, 'filename' => $file);
    }
    
    public function assign($var, $content)
    {
        if(empty($var)) _error_::set(eTPL2, LVL_FATAL);
        $this->values[$var] = $content;
    }
    
    public function ajax($var)
    {
        $this->ajax_plain(json_encode($var));
    }
    
    public function ajax_plain($data)
    {
    	$this->isAjax = true;
    	echo $data;
    }
    
    public function execute()
    {
        if($this->isAjax) return false;
        
        $forceReWork = false;
        if(!tplData::$cache)
            $forceReWork = true;
        if(DEVMODE)
            $forceReWork = true;
            
        foreach($this->includes as $include)
        {
            // crear index
            if(!file_exists(tplData::$cacheDir.$include['filename'].'.tmp') or $forceReWork)
                $this->reWork($include);
        }
        
        $this->loadCache();
        
        return true;
    }
    
    public function reWork($fileData)
    {
        $source = file_get_contents($fileData['source']);
        
        $source = $this->replaceEntities($source);
        
        // aquí procesamos las variables
        file_put_contents(tplData::$cacheDir.$fileData['filename'].'.tmp', $source);
    }
    
    public function replaceEntities($source)
    {

        //bucles
        $regex = '/\{loop=\$([a-zA-Z0-9_]*)(\-\>\[\')*(.*)\ as \$([a-zA-Z0-9_]*) to \$([a-zA-Z0-9_]*)}/';
        $regex2 = '<?php foreach(\$$1$2$3 as \$$4 => \$$5){ ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        $regex = '/\{loop=\$([a-zA-Z0-9_]*)(\-\>\[\')*(.*)\ as \$([a-zA-Z0-9_]*)}/';
        $regex2 = '<?php foreach(\$$1$2$3 as \$$4){ ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        //if
        $regex = '/\{if=\"([^\"]*)\"\}/';
        $regex2 = '<?php if($1){ ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        //function
        $regex = '/\{function=\"([^\"]*)\"\}/';
        $regex2 = '<?=$1;?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // cierres
        $regex = '/\{\/loop\}/';
        $regex2 = '<?php } ?>';
        $source = preg_replace($regex, $regex2, $source);
        $regex = '/\{\/if\}/';
        $regex2 = '<?php } ?>';
        $source = preg_replace($regex, $regex2, $source);
        $regex = '/\{else\}/';
        $regex2 = '<?php } else { ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // var dump
        $regex = '/\{dump=([^\}]*)\}/';
        $regex2 = '<?=var_dump($1);?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // primero procesamos las variables
        $regex = '/\{\$([^\}]*)\}/';
        $regex2 = '<?=\$$1;?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // ahora las constantes
        $regex = '/\{\#([a-zA-Z0-9_]*)\#\}/';
        $regex2 = '<?=$1;?>';
        $source = preg_replace($regex, $regex2, $source);
        
        $source = str_replace('{url}','http://'.$_SERVER['HTTP_HOST'], $source);
        $source = str_replace('{url:full}','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $source);
        
        // ahora las globales
        $regex = '/\{\%([a-zA-Z0-9_]*)\%\}/';
        $regex2 = '<?=_::$globals[\'$1\'];?>';
        $source = preg_replace($regex, $regex2, $source);
        
        return $source;
    }
    
    public function loadCache()
    {
        $_ = $this->values;
        extract($_); //this replace all .h
        foreach($this->includes as $include)
        {
            include(tplData::$cacheDir.$include['filename'].'.tmp');
        }
    }
}


function EliminaParametroURL($url, $parametro)
{
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    
    parse_str($qspart, $qsvars);
    
    unset($qsvars[$parametro]);
    
    $nuevoqs = http_build_query($qsvars);
    
    return $urlpart . '?' . $nuevoqs;
} 

return new template();

// errores
