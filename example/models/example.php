<?php

// this represent structure of table example
class example extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_folder'; // one or multiple keys
    
    // the fields:
    public $id_folder;
    public $id_usuario;
    public $nombre_folder;
    
    // a function to get custom multiple records!
    static public function getAllByUser($id)
    {
        // in this case, we use parent function getAll and add a where clausule
        return parent::getAll('WHERE id_usuario = '.(int)$id.' ORDER BY nombre_folder ASC');
    }
    
    // in this function check if exist one record:
    static public function exists($id)
    {
        $query = _::$db->prepare('SELECT id_folder FROM folders WHERE id_folder = ?');
        $query->execute(array($id));
        $result = $query->fetch();
        return is_array($result) ? $result['id_folder'] : false;
    }
    
    // example of using db:
    static public function example()
    {
        // the framework use one PDO instance, and you be used to anything
        // you see php document of PDO class
        $pdo = _::$db; // equal PDO CLASS CONNECTED TO DB!
    }
    
}