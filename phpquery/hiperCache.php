<?php
ob_start();

/*
 MODO DE USO:
 primero crear una instancia de hiperCache con las directivas, luego llamar a load caché (al principio del archivo index)
 al final del index llamar a save_cache para guardar el caché si no existía (y si cumple con las directivas)
 
 usar directivas para explicar al caché lo que se desea o no cachear.
 
 array(
		'mode' => CONSTANT,
		'listRegex' => array(
			array('regex' => YOU_REGEX, 'expire' => TIMESTAMP OF EXPIRE),
			OTHER REGEX
		)
	  )
	  
 si usas una directiva en modo NE (no expire) el formato sería el siguiente:
  array(
		'mode' => CONSTANT_NE,
		'listRegex' => array(
			YOU_REGEX,
			OTHER REGEX
		)
	  )
 **/

define('PHPQ_HC_DEFAULT', 1); // cachear todo menos lo que cumpla con las regex
define('PHPQ_HC_SELECTIVO', 2); // cachear solo lo que cumpla con las regex
define('PHPQ_HC_DEFAULT_NE', 3); // cachear todo menos lo que cumpla con las regex pero sin tiempo de expiracion
define('PHPQ_HC_SELECTIVO_NE', 4);// cachear solo lo que cumpla con las regex pero sin tiempo de expiracion

class hiperCache
{
	protected $directives = array('mode' => PHPQ_HC_DEFAULT, 'listRegex' => array( array('regex' => '', 'expire' => 0) ));
	
	public function __construct($directives = null)
	{
		$this->directives = $directives;
	}
	
	// high quality cache
	public function load_HQ_cache()
	{
		$request = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/' ? $_SERVER['REQUEST_URI'] : '/index';
		if(file_exists(__DIR__.'/cache_html'.$request.'.html'))
		{
			echo '<!-- LOADING CACHE -->';
			include(__DIR__.'/cache_html'.$request.'.html');
			die();
		}
	}
	
	public function save_HQ_cache()
	{
		$request = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/' ? $_SERVER['REQUEST_URI'] : '/index';
		// prevent save cache?
		$finds = false;
		if($this->directives['mode'] >= 3) // NoExpire
		{
			foreach($this->directives['listRegex'] as $regex)
			{
				$sp = null;
				preg_match($regex, $request, $sp);
				if($sp != array())
				{
					$finds = true;
				}
			}
		} else { // Expire
			foreach($this->directives['listRegex'] as $regex)
			{
				$sp = null;
				preg_match($regex['regex'], $request, $sp);
				if($sp == array())
				{
					$finds = true;
					$expire = $regex['expire'];
				}
			}
		}
		if($finds)
		{
			if($this->directives['mode'] == PHPQ_HC_DEFAULT || $this->directives['mode'] == PHPQ_HC_DEFAULT_NE) return false;
		} else {
			if($this->directives['mode'] == PHPQ_HC_SELECTIVO || $this->directives['mode'] == PHPQ_HC_SELECTIVO_NE) return false;
		}
		$out = ob_get_contents();
		// TODO: agregar expire $expire
		// make directory
		$file = $request.'.html';
		$dirs = explode('/', $file);
		unset($dirs[count($dirs)-1]);
		$loc = implode('/', $dirs);
		if(!empty($loc)) mkdir(__DIR__.'/cache_html'.$loc, 0777, true);
		file_put_contents(__DIR__.'/cache_html'.$request.'.html', $out);
	}
}