<?php

// ORM for php, designed for PDO and phpquery
define('eORM1', 'eORM1::1 the fields on the model not match with fields in table ');
define('eORM2', 'eORM2::2 primary key isn\'t defined in the model ');
define('eORM3', 'eORM3::3 primary keys don\'t match in instantiation of table ');

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
    
    public function __construct($ids = null)
    {
        if(empty($this->primaryKeys)) _error_::set(eORM2.self::tablename(), LVL_FATAL);
        $this->describe();
        if(!empty($ids))
        {
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
    
    public function populate($where)
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
        $this->lastQuery = array('query' => 'SELECT * FROM '.self::tablename().$attach.' LIMIT 1', 'values' => $params);
        $query = $this->makeQuery();
        $values = $query->fetch(PDO::FETCH_ASSOC);
        if(is_array($values))
            foreach($values as $key => $value)
            {
                $this->$key = $value;
            }
    }
    
    public function save()
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
            $this->populate(array($pk => $id));
            
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
    
    private function makeQuery()
    {
        $q = self::$pdo->prepare($this->lastQuery['query']);
        $q->execute($this->lastQuery['values']);
        $this->lastError = $q->errorInfo();
        return $q;
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
        return ltrim(get_called_class(),'table_');
    }
    
    static public function getAll($adding = null, $style = PDO::FETCH_ASSOC)
    {
        if(!empty($adding)) $adding = ' '.$adding;
        $q = _::$db->prepare('SELECT * FROM '.self::tablename().$adding);
        $q->execute();
        return $q->fetchAll($style);
    }
}

table::setPDO(_::$db);