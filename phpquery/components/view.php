<?php

class template
{
    protected $includes = array();
    protected $values = array();
    protected $isAjax = false;
    
    public function show($file)
    {
        $this->includes[] = array('source' => tplData::$folder.$file.tplData::$extension, 'filename' => $file);
    }
    
    public function assign($var, $content)
    {
        $this->values[$var] = $content;
    }
    
    public function ajax($var)
    {
        $this->isAjax = true;
        echo json_encode($var);
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
        $regex2 = '<?php foreach(\$_[\'$1\']$2$3 as \$_[\'$4\'] => \$_[\'$5\']){ ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        $regex = '/\{loop=\$([a-zA-Z0-9_]*)(\-\>\[\')*(.*)\ as \$([a-zA-Z0-9_]*)}/';
        $regex2 = '<?php foreach(\$_[\'$1\']$2$3 as \$_[\'$4\']){ ?>';
        $source = preg_replace($regex, $regex2, $source);
        
        //if
        $regex = '/\{if=\"(.*)\"\}/';
        $regex2 = '<?php if($1){ ?>';
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
        $regex = '/\{dump=\$([a-zA-Z0-9_]*)(\-\>\[\')*([^\}]*)\}/';
        $regex2 = '<?=var_dump(\$_[\'$1\']$2$3);?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // primero procesamos las variables
        $regex = '/\{\$([a-zA-Z0-9_]*)(\-\>\[\')*([^\}]*)\}/';
        $regex2 = '<?=\$_[\'$1\']$2$3;?>';
        $source = preg_replace($regex, $regex2, $source);
        
        // ahora las constantes
        $regex = '/\{\#([a-zA-Z0-9_]*)\#\}/';
        $regex2 = '<?=$1;?>';
        $source = preg_replace($regex, $regex2, $source);
        
        $source = str_replace('{url}','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $source);
        
        // ESTO VA EN PREG_MATCH_ALL //
        // ahora la url
        /*
        $regex = '/\{url\:\?([a-zA-Z0-9_]*)\}/';
        
        // encontrar el page para eliminarle
        $match = null;
        preg_match($regex,$source,$match);
        $uri = EliminaParametroURL($_SERVER['REQUEST_URI'],$match[1]);
        $adding = strpos($uri,'?')!== false ? '&' : '?';
        $regex2 = 'http://'.$_SERVER['HTTP_HOST'].$uri.$adding.'$1';
        $source = preg_replace($regex, $regex2, $source);*/
        
        return $source;
    }
    
    public function loadCache()
    {
        $_ = $this->values;
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
