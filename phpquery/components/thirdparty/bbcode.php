<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

define('BBPARSER_SIMPLE', 100); // [hr]
define('BBPARSER_DOUBLE', 101); // [b][/b]
define('BBPARSER_PARAMS', 102); // [url=$1]$2[/url]
define('BBPARSER_SUPRESS', 103); // [lock]etiqueta[/lock] -> <div>este contenido esta bloqueado</div>

/** BBCODE PARSER CLASS 
 *  @ Project Name: BBParser Alias BBP
 *  @ Author: Alexander171294 - [H]arkonnen
 *  @ Date: 05/10/13
 *  @ Contact: alexander171294@gmail.com
 *  @ Status: Prototype
 *  @ Comment-Lang: es-AR
 *  @ PHP-Version: >=5.1
 *  @ Class-Version: 2.0.0 
 */  
 
 class bbparser
 {
	protected $bbcodes = array();
	protected $start_limiter = '[';
	protected $end_limiter = ']';
	
	public function __construct($bblist)
	{
		$this->bbcodes = $bblist;
	}
	
	public function parse($content, &$supressed_content)
	{
		foreach($this->bbcodes as $bbcode)
		{
			if($bbcode['type'] === BBPARSER_SIMPLE)
				$content = $this->parse_simple($content, $bbcode);
			if($bbcode['type'] === BBPARSER_DOUBLE)
				$content = $this->parse_double($content, $bbcode);
			if($bbcode['type'] === BBPARSER_PARAMS)
				$content = $this->parse_triple($content, $bbcode);
			if($bbcode['type'] === BBPARSER_SUPRESS)
				$content = $this->parse_supress($content, $bbcode, $supressed_content);
		}
		$bbc = array_reverse($this->bbcodes);
		foreach($bbc as $bbcode)
		{
			if($bbcode['type'] === BBPARSER_SIMPLE)
				$content = $this->parse_simple($content, $bbcode);
			if($bbcode['type'] === BBPARSER_DOUBLE)
				$content = $this->parse_double($content, $bbcode);
			if($bbcode['type'] === BBPARSER_PARAMS)
				$content = $this->parse_triple($content, $bbcode);
			if($bbcode['type'] === BBPARSER_SUPRESS)
				$content = $this->parse_supress($content, $bbcode, $supressed_content);
		}
		return $content;
	}
	
	public function parse_simple($content, $bbc)
	{
		$id = $bbc['id'];
		$regex = $this->start_limiter.$id.$this->end_limiter;
		$replace = $bbc['html_base'];
		return str_replace($regex,$replace,$content);
	}
	
	public function parse_double($content, $bbc)
	{
		$id = $bbc['id'];
		$start = preg_quote('['.$id.']', '/');
		$end = preg_quote('[/'.$id.']', '/');
		$regex = '/'.$start.'([^[]+)'.$end.'/';
		$replace = $bbc['html_base'].'$1'.$bbc['html_final'];
		return preg_replace($regex, $replace, $content);
	}
	
	public function parse_triple($content, $bbc)
	{
		$id = $bbc['id'];
		$start = preg_quote('['.$id, '/').'=([^[]+)\]';
		$end = preg_quote('[/'.$id.']', '/');
		$regex = '/'.$start.'([^[]+)'.$end.'/';
		$base = str_replace('?', '$1', $bbc['html_base']);
		$replace = $base.'$2'.$bbc['html_final'];
		return preg_replace($regex, $replace, $content);
	}
	
	public function parse_supress($content, $bbc, &$sc)
	{
		$id = $bbc['id'];
		$start = preg_quote('['.$id.']', '/');
		$end = preg_quote('[/'.$id.']', '/');
		$regex = '/'.$start.'([^[]+)'.$end.'/';
		$replace = $bbc['html_base'];
		$matches = null;
		preg_match_all($regex,$content, $matches);
		$sc[] = $matches[1];
		return preg_replace($regex, $replace, $content);
	}
 }