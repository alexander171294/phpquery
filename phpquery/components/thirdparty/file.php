<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

Class File
{
	
	protected $kb = 1024;
	protected $mb = 1024;
	protected $gb = 1024;
	private $name = null;
	private $peso = null;
	private $type = null;
	private $location = null;
	
	private $error = false;
	private $error_msg = null;
	
	public function __construct($location = null, $name = null)
	{
		try
		{
			if(!empty($location))
			{
				if(empty($name)) throw new exception('El nombre del fichero es requerido');
				if(!file_exists($location.$name)) throw new exception('El fichero no existe');
				$this->name = $name;
				$this->location = $location;
				$this->peso = filesize($location.$name);
			}
		} catch(Exception $e) 
		{
			$this->error = true;
			$this->error_msg = $e->getMessage();	
		}
	}
	
	public function download($name)
	{
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"{$name}\"\n");
		$fp=fopen($this->location.$this->name, "r");
		fpassthru($fp);
		die();
	}
	
	public function upload($nombre_campo, $carpeta, $uniqid = false, $namecrypt = false, $extension_valida = array('rar'), $file_type = null, $kbmax = 0, $dir = true)
	{
		try 
		{
			$this->name = $_FILES[$nombre_campo]['name'];
			$this->size = $_FILES[$nombre_campo]['size'];
			$this->type = $_FILES[$nombre_campo]['type'];
			$this->location = $carpeta;
			if(empty($_FILES[$nombre_campo])) throw new exception('Archivo no seleccionado');
			$id = null;
			if($uniqid) $id = uniqid(microtime());
			$this->name = $id.$this->name;
			// verificamos si la extensión es válida
			$valido = false;
			for($i = 0; $i<count($extension_valida); $i++)
				if($this->validate_extension($this->name, $extension_valida[$i]))
					$valido = true;
			if(!$valido) throw new exception('El archivo es de una extensión inválida');
			// verificamos si el tipo de archivo es valido
			if(!empty($file_type) && $this->type!=$file_type) throw new exception('El tipo de archivo es inválido');
			// verificamos si tiene el tamaño correcto
			if($kbmax>0 && $this->size > $kbmax*$this->kb) throw new exception('El archivo pesa demaciado');
			// lo encryptamos si es preciso
			if($namecrypt)
				$this->name = 'file_'.md5($this->name).'.cab';
			// verificamos que el directorio tenga los permisos
			//------------ TODO
			// ahora lo vamos a mover
			if (!move_uploaded_file($_FILES[$nombre_campo]['tmp_name'], $this->location.$this->name)) throw new exception('No se puede mover fichero subido a carpeta especificada');
			// retornamos el directorio donde se guardó
			if($dir)
				return $this->location.$this->name;
			else
				return $this->name;
		} catch (Exception $e)
		{
			$this->error = true;
			$this->error_msg = $e->getMessage();
			return false;
		}
	}
	
	public Function validate_extension($filename, $extension)
	{
		$ext_init = strlen($filename) - strlen($extension);
		$last_ext = null;
		for($i=$ext_init; $i<strlen($filename); $i++)
			$last_ext.=$filename[$i];
		if($last_ext == $extension) 
			return true;
		else
			return false;
	}
	
	public function get_name() { return $this->name; }
	public function set_name($value) { $this->name = $value; }
	
	public function get_peso() { return $this->peso; }
	public function set_peso($value) { ; }
	
	public function get_type() { return $this->type; }
	public function set_type($value) { ; }
	
	public function get_location() { return $this->location; }
	public function set_location($value) { $this->location = $value; }
	
	public function get_error() { return $this->error; }
	public function set_error($value) { ; }
	
	public function get_error_msg() { return $this->error_msg; }
	public function set_error_msg($value) { ; }

}