<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

_::declare_component('property');

Class mailer
{	
	// uses
	use property;
	
	// atributes
	private $receptor = null;
	private $receptor_alias = null;
	private $emisor = null;
	private $emisor_alias = null;
	private $asunto = null;
	private $charset = 'UTF-8';
	private $content_type = 'text/html';
	protected $mime_version = '1.0';
	private $mensaje = null;
	
	protected $error = false;
	protected $error_message = null;
	
	public function __construct($emisor, $receptor, $asunto, $mensaje, $emisor_alias = null, $receptor_alias = null)
	{
		try 
		{
			if(!$this->validar_email($emisor)) Throw new exception('Invalid mail $emisor');
			if(!$this->validar_email($receptor)) Throw new exception('Invalid mail $receptor');
			$asunto = trim($asunto);
			if(empty($asunto) && strlen($asunto)<3) throw new exception('Invalid subject $asunto');
			$mensaje = trim(utf8_encode($mensaje));
			if(empty($mensaje) && strlen($mensaje)<10) throw new exception('Invalid menssage $mensaje');
			$this->receptor = $receptor;
			$this->emisor = $emisor;
			$this->asunto = $asunto;
			$this->mensaje = $mensaje;
			if(!empty($emisor_alias))
			{
				$emisor_alias = trim($emisor_alias);
				if(strlen($emisor_alias)>3)
					$this->emisor_alias = $emisor_alias;
				else 
					throw new exception('Emisor Alias is invalid');
			}
			if(!empty($receptor_alias))
			{
				$emisor_alias = trim($receptor_alias);
				if(strlen($receptor_alias)>3)
					$this->receptor_alias = $receptor_alias;
				else
					throw new exception('Receptor Alias is invalid');
			}
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function validar_email($email)
	{
		return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',$email);
	}
	
	public function send()
	{
		if(!$this->error)
		{
			mail($this->receptor, $this->asunto, $this->mensaje, $this->fabricar_cabeceras());
			return true;
		} else { return false; }	
	}
	
	private function fabricar_cabeceras()
	{
		$cabeceras = null;
		$cabeceras  = 'MIME-Version: ' . $this->mime_version . "\r\n";
		$cabeceras .= 'Content-type: ' . $this->content_type . '; charset=' . $this->charset . "\r\n";
		if(!empty($this->receptor_alias)) 
		{
			$cabeceras .= 'To: ' . $this->receptor_alias . ' <' . $this->receptor . '>' . "\r\n";
		} else {
			$cabeceras .= 'To: ' . $this->receptor . "\r\n";
		}
		if(!empty($this->emisor_alias))
		{
			$cabeceras .= 'From: ' . $this->emisor_alias . ' <' . $this->emisor . '>' . "\r\n";
		} else {
			$cabeceras .= 'From: ' . $this->emisor . "\r\n";
		}
		return $cabeceras;
	}
	
	public function get_receptor() { return $this->receptor; }
	public function set_receptor($value)
	{
		try 
		{
			$receptor = $value;
			if(!$this->validar_email($receptor)) Throw new exception('Invalid mail $receptor');
			$this->receptor = $receptor;
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_receptor_alias() { return $this->receptor_alias;}
	public function set_receptor_alias($value)
	{
		try
		{
			$receptor_alias = $value;
			$receptor_alias = trim($receptor_alias);
			if(!strlen($receptor_alias)>3) throw new exception('Receptor Alias is invalid');
			$this->receptor_alias = $receptor_alias;
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_emisor() { return $this->emisor; }
	public function set_emisor($value)
	{
		try 
		{
			$emisor = $value;
			if(!$this->validar_email($emisor)) Throw new exception('Invalid mail $emisor');
			$this->emisor = $emisor;
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_emisor_alias() { return $this->emisor_alias; }
	public function set_emisor_alias($value)
	{
		try 
		{
			$emisor_alias = $value;
			$emisor_alias = trim($emisor_alias);
			if(!strlen($emisor_alias)>3) throw new exception('Emisor Alias is invalid');
			$this->emisor_alias = $emisor_alias;	
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_asunto() { return $this->asunto; }
	public function set_asunto($value)
	{
		try 
		{
			$asunto = value;
			$asunto = trim($asunto);
			if(empty($asunto) && strlen($asunto)<3) throw new exception('Invalid subject $asunto');
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_charset() { return $this->charset; }
	public function set_charset($value)
	{
		$this->charset = $value;
	}
	
	public function get_content_type() { return $this->content_type; }
	public function set_content_type($value)
	{
		$this->content_type = $value;
	}
	
	public function get_mime_version() { return $this->mime_version; }
	public function set_mime_version($value) { ; } // no podemos cambiar el mime_version
	
	public function get_mensaje() { return $this->mensaje; }
	public function set_mensaje($value) 
	{ 
		try 
		{
			$mensaje = $value;
			$mensaje = trim(utf8_encode($mensaje));
			if(empty($mensaje) && strlen($mensaje)<10) throw new exception('Invalid menssage $mensaje');
			$this->mensaje = $mensaje;
		} catch (Exception $e) { $this->error = true; $this->error_message = $e->getMessage(); }
	}
	
	public function get_error() { return $this->error; }
	public function set_error($value) { ; } // no permitimos setear un error
	
	public function get_error_message() { return $this->error_message; }
	public function set_error_message($value) { ; } // no permitimos setear un mensaje de error
	
}