<?php //changelog added validateValues()

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

// ORM for php, designed for PDO and phpquery
define('eORM1', 'eORM1::1 the fields on the model not match with fields in table ');
define('eORM2', 'eORM2::2 primary key isn\'t defined in the model ');
define('eORM3', 'eORM3::3 primary keys don\'t match in instantiation of table ');
define('eORM4', 'eORM3::4 !attempt to save a property of type ');

abstract class table
{
    
    protected $primaryKeys; // one or multiple keys
    static protected $pdo = null;
    static public $query = null;
    protected $fields = null;
    private $new = false;
    
    private $individualWhereClausule = null;
    private $iwc_values = null;
    
    public $lastQuery = null;
    public $lastError = null;
    public $void = false;
	
	static protected $randTable = array();
    
    public function __construct($ids = null)
    {
        if(empty($this->primaryKeys)) _error_::set(eORM2.self::tablename(), LVL_FATAL);
        $this->describe();
        if(!empty($ids))
        {
        	if(is_array($ids) && count($ids) == 1) $ids = $ids[0];
            if(is_array($this->primaryKeys))
            {
                if(count($this->primaryKeys) !== count($ids))  _error_::set(eORM3.self::tablename(), LVL_FATAL);
                $out = array();
                for($i = 0; $i<count($this->primaryKeys); $i++)
                {
                    if(!empty($ids[$i]))
                    $out[$this->primaryKeys[$i]] = $ids[$i];
                }
                $this->populate($out);
            } else {
                $this->populate(array($this->primaryKeys => $ids));
            }
        } else $this->new = true;
    }
    
    protected function describe()
    {
        $q = self::$pdo->prepare('DESCRIBE '.self::tablename());
        $q->execute();
        $this->fields = $q->fetchAll(PDO::FETCH_COLUMN);
        // comprobamos que existan todos los campos en la extension de la tabla
        foreach($this->fields as $field)
        {
            if(!property_exists($this, $field))
            {
                _error_::set(eORM1.' field:'.$field.' table:'.self::tablename(), LVL_WARNING);
            }
        }
    }
    
    public function populate($where, $preserve = false)
    {
        $attach = ' WHERE ';
        $params = array();
        foreach($where as $key => $value)
        {
            $attach .= $key.' = ? AND ';
            $params[] = $value;
        }
        $attach = rtrim($attach, 'AND ');
        $this->individualWhereClausule = $attach;
        $this->iwc_values = $params;
        if($preserve) $oldQ = $this->lastQuery;
        $this->lastQuery = array('query' => 'SELECT * FROM '.self::tablename().$attach.' LIMIT 1', 'values' => $params);
        $query = $this->makeQuery(!$preserve);
        if($preserve) $this->lastQuery = $oldQ;
        $values = $query->fetch(PDO::FETCH_ASSOC);
        if(is_array($values))
        {
            foreach($values as $key => $value)
            {
                $this->$key = $value;
            }
        } else $this->void = true;
    }
    
    public function save($showPopulateError = false)
    {
        if($this->new)
        { // insert
            $fields = trim(implode(', ',$this->fields),', ');
            $valuesString = null;
            $values = array();
            foreach($this->fields as $field)
            {
                $valuesString .= '?, ';
                $values[] = $this->$field;
            }
           
            $valuesString = trim($valuesString, ', ');

            $this->lastQuery = array('query' => 'INSERT INTO '.self::tablename().'('.$fields.') VALUES('.$valuesString.')', 'values' => $values);
            $this->makeQuery();
            
            $this->new = false;
            
            $id = self::$pdo->lastInsertId();
            if(!is_array($this->primaryKeys))
            {
            	$pk = $this->primaryKeys;
            	$this->$pk = $id;
            } else {
            	$pk = $this->primaryKeys[0];
            	$this->$pk = $id;
            }
            
            $this->populate(array($pk => $id), !$showPopulateError);
            
            return $id;
        } else { // update
            $values = array();
            $changes = null;
            foreach($this->fields as $field)
            {
                $changes .= $field.' = ?, ';
                $values[] = $this->$field;
            }
            $changes = trim($changes, ', ');
            $values = array_merge($values, $this->iwc_values);
            $this->lastQuery = array('query' => 'UPDATE '.self::tablename().' SET '.$changes.$this->individualWhereClausule, 'values' => $values);
            $this->makeQuery();
            // para control de errores
            return true;
        }
    }
    
    private function makeQuery($saveError = true)
    {
        $q = self::$pdo->prepare($this->lastQuery['query']);
        $this->validateValues();
        $q->execute($this->lastQuery['values']);
        if($saveError)
        	$this->lastError = $q->errorInfo();
        return $q;
    }
    
    private function validateValues()
    {
    	foreach($this->lastQuery['values'] as $key => $valueGroup)
    	{
    		if(is_object($valueGroup))
    			_error_::set(eORM4.'OBJECT in table '.self::tablename().' field '.$this->fields[$key], LVL_FATAL);
    		if(is_resource($valueGroup))
    			_error_::set(eORM4.'RESOURCE in table '.self::tablename().' field '.$this->fields[$key], LVL_FATAL);
    		if(is_link($valueGroup))
    			_error_::set(eORM4.'LINK in table '.self::tablename().' field '.$this->fields[$key], LVL_FATAL);
    		if(is_array($valueGroup))
    		{
    				_error_::set(eORM4.'ARRAY in table '.self::tablename().', then is transformed to JSON - field '.$this->fields[$key], LVL_WARNING);
    				$this->lastQuery['values'][$key] = json_encode($this->lastQuery['values'][$key]);
    		}
    	}
    }
    
    public function delete()
    {
        $this->lastQuery = array('query' => 'DELETE FROM '.self::tablename().' '.$this->individualWhereClausule, 'values' => $this->iwc_values);
        $this->makeQuery();
    }
    
    static public function setPDO($pdo)
    {
        self::$pdo = $pdo;
    }
    
    static public function tablename()
    {
        return str_replace('table_',null,get_called_class());
    }
    
    static public function getAll($adding = null, $arrayExec = array(), $style = PDO::FETCH_ASSOC)
    {
        if(!empty($adding)) $adding = ' '.trim($adding);
        $q = _::$db->prepare('SELECT * FROM '.self::tablename().$adding);
        $q->execute($arrayExec);
        return $q->fetchAll($style);
    }
	
	static public function getAllObjects($primarykeys, $adding = null, $arrayExec = array())
	{
		$array = self::getAll($adding, $arrayExec);
		return _::factory($array, $primarykeys, self::tablename());
	}
    
    static public function getUnique($adding = null, $arrayExec = array(), $style = PDO::FETCH_ASSOC)
    {
    	if(!empty($adding)) $adding = ' '.trim($adding);
    	$q = _::$db->prepare('SELECT * FROM '.self::tablename().$adding);
    	$q->execute($arrayExec);
    	return $q->fetch($style);
    }
	
	static public function count($pk, $adding = null, $arrayExec = array())
	{
		if(!empty($adding)) $adding = ' '.trim($adding);
        $q = _::$db->prepare('SELECT count('.$pk.') as total FROM '.self::tablename().$adding);
		$q->execute($arrayExec);
		return $q->fetch(PDO::FETCH_ASSOC)['total'];
	}
	
	static public function getRand($pk, $whereSection = null, $cantidad = 1, $noRepeat = false)
	{
		$records = self::count($pk, $whereSection);
		$out = null;
		for($i=0; $i<$cantidad; $i++)
        {
			$aleatorio = rand(0, $records-1);
			if($cantidad > 1)
			{
				$toOut = self::getUnique($whereSection.' LIMIT '.$aleatorio.', 1');
				if(!$noRepeat){
					$out[] = $toOut;
				} elseif($cantidad < $records) {
					if(in_array($toOut[$pk], self::$randTable))
						$out[] = self::getRand($pk, $whereSection, $cantidad, true);
					else
					{
						$out[] = $toOut;
						self::$randTable[] = $toOut[$pk];
					}
				}
			}
			else return self::getUnique($whereSection.' LIMIT '.$aleatorio.', 1');
        }
		self::$randTable = array();
		return $out;
	}
	
	static public function deleteAll($adding = null, $arrayExec = array())
    {
        if(!empty($adding)) $adding = ' '.trim($adding);
        $q = _::$db->prepare('DELETE FROM '.self::tablename().$adding);
        $q->execute($arrayExec);
        return true;
    }
}

table::setPDO(_::$db);