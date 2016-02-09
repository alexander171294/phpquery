<?php
if(!defined(PHPQUERY_LOADER)) {
	include('../../index.html');
	die();
}

/** BBCODE PARSER CLASS 
 *  @ Project Name: BBParser Alias BBP
 *  @ Author: Alexander171294 - [H]arkonnen
 *  @ Date: 05/10/13
 *  @ Contact: alexander171294@gmail.com
 *  @ Status: Prototype
 *  @ Comment-Lang: es-AR
 *  @ PHP-Version: >=5.1
 *  @ Class-Version: 1.1.0 
 */  
 
/* Ésta clase está orientada al tratamiento efectivo de bbcode
   Para lo cual cuenta con varias clases, la principal para parsear los textos
   y clases secundarias que corresponden a bbcode en específico
*/

/** Property para php
 *  ésto fué escrito por alexander171294 - [H]arkonnen
 *  como parte del módulo BBParser 1.1.0
 *  @ Contact: alexander171294@gmail.com
 */  
trait MyProperty // mi hermosa clase property
{
    // llamando a funciones setters
    Public function __set($property, $value)
    {
        if(is_callable(array($this, 'set_'.$property), $value)) 
            return call_user_func(array($this, 'set_'.$property), $value); 
        else 
        // no hay funcion setter para este atributo (o no existe el atributo)
            return false; 
    }
    
    // llamando a funciones getters
    Public function __get($property)
    {
        if(is_callable(array($this, 'get_'.$property))) 
            return call_user_func(array($this, 'get_'.$property)); 
        else 
        // no hay funcion getter para este atributo (o no existe el atributo)
            return false; 
    }
}

/* Clase que define a un objeto BBCODE
    Es una clase básicamente declaratoria
*/

abstract class bbcode
{
    use MyProperty;
    /* 
        identificador del bbcode
        @obj_id (string)
    */ 
    private $obj_id = null;
    /* 
        su identidad en html
        @obj_html (string)
    */ 
    private $obj_html = null;
    /* 
        cantidad de coincidencias
        @obj_html (int)
    */ 
    private $obj_cc = 0;
    /* 
        delimitador inicial del bbcode (con que se inicia el bbcode)
        @delimiter_initial (string)
    */ 
    private $delimiter_initial = '[';
    /* 
        delimitador final del bbcode (con que se finaliza el bbcode)
        @delimiter_final (string)
    */ 
    private $delimiter_final = ']';
    /*
        variable que contiene el estado de errores
        @error (bool) false: sin errores; true: ocurrio un problema
    */
    private $error = false;
    /*
        variable que contiene el posible mensaje de error
        @error_detail (string)
    */
    private $error_detail = null;
    
    /*
        función constructora que asigna el id o clave del bbcode
        @obj_id (string) identificador del bbcode
        @obj_html (string) identidad en html
        @delimiter_initial (char) OPTIONAL caracter inicial del bbcode
        @delimiter_final (char) OPTIONAL caracter final del bbcode
    */
    public function __construct( $obj_id, $obj_html, $delimiter_initial = null, $delimiter_final = null)
    {
        $obj_id = (string) $obj_id; $obj_html = (string) $obj_html; $delimiter_initial = (string) $delimiter_initial; $delimiter_final = (string) $delimiter_final;
        try
        {
            $obj_id = trim($obj_id);
            if(strlen($obj_id)<1 || empty($obj_id)) throw new exception ('INVALID PARAM @OBJ_ID');
            $this->obj_id = $obj_id;
            $obj_html = trim($obj_html);
            if(strlen($obj_html)<1 || empty($obj_html)) throw new exception ('INVALID PARAM @OBJ_HTML');
            $this->obj_html = $obj_html;
            $this->delimiter_initial = empty($delimiter_initial) ? $this->delimiter_initial : $delimiter_initial;
            $this->delimiter_final = empty($delimiter_final) ? $this->delimiter_final : $delimiter_final;
            
        } catch (Exception $e)
        {
            $this->error_detail = $e->getMessage();
            $this->error = true;
        }
    }
    
    // si se quiere usar el objeto como una variable que contiene un valor (convertirlo a string)
    public function __toString()
    {
        return $this->delimiter_initial.$this->obj_id.$this->delimiter_final; // componemos el bbcode
    }
    
    ///////////////////////////////////////////////////////////
    // Funciones Setters (para cambio luego del constructor) //
    ///////////////////////////////////////////////////////////
    
    // steamos el nuevo id del objeto
    final public function set_obj_id($new_obj_id)
    {
        $obj_id = (string) $new_obj_id;
        try
        {
            $obj_id = trim($obj_id);
            if(strlen($obj_id)<1 || empty($obj_id)) throw new exception ('INVALID PARAM @OBJ_ID');
            $this->obj_id = $new_obj_id;
            return true;
        } catch (Exception $e)
        {
            $this->error_detail = $e->getMessage();
            $this->error = true;
            return false;
        }
    }
    
    // seteamos el nuevo html para este bbcode
    final public function set_obj_html($new_obj_html)
    {
        $new_obj_html = (string) $new_obj_html;
        try
        {
            $obj_html = trim($obj_html);
            if(strlen($obj_html)<1 || empty($obj_html)) throw new exception ('INVALID PARAM @OBJ_HTML');
            $this->obj_html = $new_obj_html;
            return true;
        } catch (Exception $e)
        {
            $this->error_detail = $e->getMessage();
            $this->error = true;
            return false;
        }
    }
    
    // seteamos los nuevos delimitadores para este bbcode
    public function set_delimiters($delimiter_initial, $delimiter_final)
    {
        $delimiter_initial = (string) $delimiter_initial; $delimiter_final = (string) $delimiter_final;
        try
        {
            $delimiter_initial = trim($delimiter_initial);
            if(strlen($delimiter_initial)<1 || empty($delimiter_initial)) throw new exception ('INVALID PARAM @delimiter_initial');
            $this->delimiter_initial = $delimiter_initial;
            $delimiter_final = trim($delimiter_final);
            if(strlen($delimiter_final)<1 || empty($delimiter_final)) throw new exception ('INVALID PARAM @delimiter_final');
            $this->delimiter_final = $delimiter_final;
            return true;
        } catch (Exception $e)
        {
            $this->error_detail = $e->getMessage();
            $this->error = true;
            return false;
        }
    }
    
    ///////////////////////////////////////////////////////////
    // Funciones Getters (para obtener atributos)            //
    ///////////////////////////////////////////////////////////
    
    // obtener el id de este bbcode
    final public function get_obj_id()
    {
        return $this->obj_id;
    }
    
    // obtener el html de este bbcode
    final public function get_obj_html()
    {
        return $this->obj_html;
    }
    
    // obtener los delimitadores de este bbcode
    final public function get_delimiter_initial()
    {
        return $this->delimiter_initial;
    }
    
    // obtener los delimitadores de este bbcode
    final public function get_delimiter_final()
    {
        return $this->delimiter_final;
    }
    
    // obtener si hay error
    final public function get_error()
    {
        return $this->error;
    }
    
    // obtener detalles del error
    final public function get_error_detail()
    {
        return $this->error_detail;
    }
}

class bbc_basic extends bbcode
{
    /*
        función que parsea el texto para bbcodes básicos
    */
    public function parser($text)
    {
        $text = (string) $text;
        $text = trim($text);
        if(!empty($text))
            return str_replace(parent::get_delimiter_initial().parent::get_obj_id().parent::get_delimiter_final(),parent::get_obj_html(),$text);
        else
            return false;
    }
}

class bbcode_double extends bbcode
{
    /* 
        identificador de cierre de bbcode (dado por delimitador inicial + identificador de cierre + id + delimitador final)
        @obj_close_id (string)
    */ 
    private $obj_close_id = '/';
    /* 
        identificador de cierre de html (por ejemplo </a>)
        @html_close (string)
    */ 
    private $html_close = null;
    
    /*
        función que parsea el texto para bbcodes dobles
    */
    public function parser($text)
    {
        $text = (string) $text;
        $text = trim($text);
        if(!empty($text))
        {
            $key = parent::get_delimiter_initial().parent::get_obj_id().parent::get_delimiter_final().'?'.parent::get_delimiter_initial().$this->obj_close_id.parent::get_obj_id().parent::get_delimiter_final();
            $item = parent::get_obj_html().'$1'.$this->html_close;
            return preg_replace('#'.str_replace(array('[',']'),array('\[','\]'),str_replace('?','(.+)',$key)).'#i', $item, $text);
        }
        else
        {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double ERROR: function parser :: Param $text is invalid';
            return false;
        }
    }
    
    ///////////////////////////////////////////////////////////
    // Funciones Setters (para cambio luego del constructor) //
    ///////////////////////////////////////////////////////////
    
    /*
        función para setear el cierre del bbcode (por ejemplo /)
    */
    public function set_obj_close_id($id)
    {
        $id = (string) $id;
        $id = trim($id);
        if(!empty($id))
        {
            $this->obj_close_id = $id;
            return true;
        } else {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double ERROR: function set_obj_close_id :: Param $id is invalid';
            return false;
        }
    }
    
    /*
        función para setear el cierre de html (por ejemplo </a>)
    */
    public function set_html_close($html)
    {
        $html = (string) $html;
        $html = trim($html);
        if(!empty($html))
        {
            $this->html_close = $html;
            return true;
        } else {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double ERROR: function set_html_close :: Param $html is invalid';
            return false;
        }
        
    }
    
    ///////////////////////////////////////////////////////////
    // Funciones Getters (para obtener atributos)            //
    ///////////////////////////////////////////////////////////
    
    public function get_obj_close_id()
    {
        return $this->obj_close_id;
    }
    
    public function get_html_close()
    {
        return $this->html_close;
    }
}

class bbcode_double_params extends bbcode
{
    /* 
        identificador de cierre de bbcode (dado por delimitador inicial + identificador de cierre + id + delimitador final)
        @obj_close_id (string)
    */ 
    private $obj_close_id = '/';
    /* 
        identificador de cierre de html (por ejemplo </a>)
        @html_close (string)
    */ 
    private $html_close = null;
    
    /*
        función que parsea el texto para bbcodes dobles con parametros
    */
    public function parser($text)
    {
        $text = (string) $text;
        $text = trim($text);
        if(!empty($text))
        {
            $key = parent::get_delimiter_initial().parent::get_obj_id().'=?'.parent::get_delimiter_final().'?'.parent::get_delimiter_initial().$this->obj_close_id.parent::get_obj_id().parent::get_delimiter_final();
            $item = str_replace('?','$1',parent::get_obj_html()).'$2'.$this->html_close;
            return preg_replace('#'.str_replace(array('[',']'),array('\[','\]'),str_replace('?','(.+)',$key)).'#i', $item, $text);
        }
        else
        {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double_params ERROR: function parser :: Param $text is invalid';
            return false;
        }
    }
    
        /*
        función para setear el cierre de html (por ejemplo </a>)
    */
    
    /*
        función para setear el cierre del bbcode (por ejemplo /)
    */
    public function set_obj_close_id($id)
    {
        $id = (string) $id;
        $id = trim($id);
        if(!empty($id))
        {
            $this->obj_close_id = $id;
            return true;
        } else {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double ERROR: function set_obj_close_id :: Param $id is invalid';
            return false;
        }
    }
    
    public function set_html_close($html)
    {
        $html = (string) $html;
        $html = trim($html);
        if(!empty($html))
        {
            $this->html_close = $html;
            return true;
        } else {
            $this->error = true;
            $this->error_detail = 'Class bbcode_double ERROR: function set_html_close :: Param $html is invalid';
            return false;
        }
        
    }
    
    ///////////////////////////////////////////////////////////
    // Funciones Getters (para obtener atributos)            //
    ///////////////////////////////////////////////////////////
    
    public function get_obj_close_id()
    {
        return $this->obj_close_id;
    }
    
    public function get_html_close()
    {
        return $this->html_close;
    }

}

class bbc_smiley extends bbcode
{
    /*
        función que parsea el texto para bbcodes básicos
    */
    public function parser($text)
    {
        $text = (string) $text;
        $text = trim($text);
        if(!empty($text))
            return str_replace(parent::get_obj_id(),parent::get_obj_html(),$text);
        else
            return false;
    }
}

//////////////////////////////////////
//         CLASE PARSEADORA         //
//////////////////////////////////////

// clase que parsea los bbcode
class BBParser
{
    use MyProperty;
    // arreglo de errores
    private $errors = array();
    // cantidad de errores
    private $errors_count = 0;
    // cantidad de bbcode simples
    private $simple_count = 0;
    // cantidad de bbcode dobles
    private $double_count = 0;
    // cantidad de bbcode doble con parametros
    private $double_params_count = 0;
    // cantidad de bbcode smiley
    private $smiley_count = 0;
    // listado de bbcodes disponibles
    private $bbcode = null;
    // texto a parsear
    private $text = null;
    
    // función constructora
    public function __construct( $bbcode_list, $text )
    {
        $text = (string) $text;
        try
        {
            if(!is_array($bbcode_list)) throw new exception ('Class bbcode_parser INSTANCE ERROR: the param $bbcode_list is invalid type');
            if(count($bbcode_list)<1) throw new exception ('Class bbcode_parser INSTANCE ERROR: the param $bbcode_list is invalid array');
            $text = trim($text);
            if(empty($text)) throw new exception ('Class bbcode_parser INSTANCE ERROR: empty param $text');
            $this->bbcode = $bbcode_list;
            $this->text = $text;
        } catch (Exception $e)
        {
            $this->errors[$this->errors_count] = $e->getMessage();
            $this->errors_count++;
        }
    }
    
    // función para parsear
    public function parser()
    {
        if(empty($this->bbcode)) 
        {
            $this->errors[$this->errors_count] = 'Class bbcode_parser Function parser(); NOT VALID BBCODE_LIST';
            $this->errors_count++;
            return false;
        }
        for($i = 0; $i<count($this->bbcode); $i++)
        {
            if($this->bbcode[$i] instanceof bbc_basic)
                $this->text = $this->parser_bbc_basic($this->bbcode[$i]);
            elseif($this->bbcode[$i] instanceof bbcode_double)
                $this->text = $this->parser_bbc_double($this->bbcode[$i]);
            elseif($this->bbcode[$i] instanceof bbcode_double_params)
                $this->text = $this->parser_bbc_double_params($this->bbcode[$i]);
            elseif($this->bbcode[$i] instanceof bbc_smiley)
                $this->text = $this->parser_bbc_smiley($this->bbcode[$i]);
            else
            {
                $this->errors[$this->errors_count] = 'Class bbcode_parser Function parser(); The item #' . $i . ' of array $bbcode_list is not a bbc valid class';
                $this->errors_count++;
            }
        }
        return true;
    }
    
    // parse bbcode_basic
    private function parser_bbc_basic( bbc_basic $bbc )
    {
        $bbc = (object) $bbc;
        $this->simple_count++;
        if($result = $bbc->parser($this->text))
            return $result;
        else
        {
            $this->errors[$this->errors_count] =  $bbc->get_error_detail();
            $this->errors_count++;
            return false;
        }
    }
    
    // parse bbcode_double
    private function parser_bbc_double( bbcode_double $bbc )
    {
        $bbc = (object) $bbc;
        $this->double_count++;
        if($result = $bbc->parser($this->text))
            return $result;
        else
        {
            $this->errors[$this->errors_count] =  $bbc->get_error_detail();
            $this->errors_count++;
            return false;
        }
    }
    
    // parse bbcode_double_params
    private function parser_bbc_double_params( bbcode_double_params $bbc )
    {
        $bbc = (object) $bbc;
        $this->double_params_count++;
        if($result = $bbc->parser($this->text))
            return $result;
        else
        {
            $this->errors[$this->errors_count] =  $bbc->get_error_detail();
            $this->errors_count++;
            return false;
        }
    }
    
    // parse bbcode_double_params
    private function parser_bbc_smiley( bbc_smiley $bbc )
    {
        $bbc = (object) $bbc;
        $this->smiley_count++;
        if($result = $bbc->parser($this->text))
            return $result;
        else
        {
            $this->errors[$this->errors_count] =  $bbc->get_error_detail();
            $this->errors_count++;
            return false;
        }
    }
    
    // si quieren usar el objeto como una variable con texto lo convertimos a string
    public function __toString()
    {
        $this->parser();
        return $this->text;
    }
    
    // GETTERS
    public function get_errors()
    {
        return $this->errors;
    }
    public function get_errors_count()
    {
        return $this->errors_count;
    }
    public function get_simple_count()
    {
        return $this->simple_count;
    }
    public function get_double_count()
    {
        return $this->double_count;
    }
    public function get_double_params_count()
    {
        return $this->double_params_count;
    }
    public function get_text()
    {
        return $this->text;
    }
}

// crear objeto bbcode simple
function bbp_generate_bbcode_simple($obj_id, $obj_html, $delimiter_initial = null, $delimiter_final = null)
{
    if(empty($delimiter_initial))
    {
        return new bbc_basic((string)$obj_id, (string)$obj_html);
    } else {
        return new bbc_basic((string)$obj_id, (string)$obj_html, (string)$delimiter_initial, (string)$delimiter_final);
    }
}

// crear objeto bbcode smiley
function bbp_generate_bbcode_smiley($obj_id, $obj_html)
{
        return new bbc_smiley((string)$obj_id, (string)$obj_html);
}

// crear objeto bbcode doble
function bbp_generate_bbcode_double($obj_id, $obj_html, $html_close, $delimiter_initial = null, $delimiter_final = null)
{
    if(empty($delimiter_initial))
    {
        $bbc = new bbcode_double((string)$obj_id, (string)$obj_html);
        $bbc->set_html_close((string) $html_close);
        return $bbc;
    } else {
        $bbc = new bbcode_double((string)$obj_id, (string)$obj_html, (string)$delimiter_initial, (string)$delimiter_final);
        $bbc->set_html_close((string) $html_close);
        return $bbc;
    }
}

// crear objeto bbcode doble con parametros
function bbp_generate_bbcode_double_params($obj_id, $obj_html, $html_close, $delimiter_initial = null, $delimiter_final = null)
{
    if(empty($delimiter_initial))
    {
        $bbc = new bbcode_double_params((string)$obj_id, (string)$obj_html);
        $bbc->html_close = (string) $html_close;
        return $bbc;
    } else {
        $bbc = new bbcode_double_params((string)$obj_id, (string)$obj_html, (string)$delimiter_initial, (string)$delimiter_final);
        $bbc->html_close = (string) $html_close;
        return $bbc;
    }
}

// parsear un texto usando una lista de codigos bbc
function bbp_parse($list_bbcode, $text_parse, &$error_array)
{
    $bbp = new BBParser( (array) $list_bbcode, (string) $text_parse );
    $error_array = $bbp->errors;
    $bbp->parser();
    return $bbp->text;
    /*
      Otra forma de hacer lo de arriba podría ser:
      echo $bbp;
      sin la linea 616, 617 (osea poniendo return $bbp y luego un echo a eso)
    */
}

