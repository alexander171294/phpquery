<?php

class folders extends table
{
    protected $primaryKeys = 'id_folder'; // one or multiple keys
    
    public $id_folder;
    public $id_usuario;
    public $nombre_folder;
    
    static public function getAllByUser($id)
    {
        // obtener carpetas del usuario ordenadas alfabeticamente
        return parent::getAll('WHERE id_usuario = '.(int)$id.' ORDER BY nombre_folder ASC');
    }
    
    static public function exists($id)
    {
        $query = _::$db->prepare('SELECT id_folder FROM folders WHERE id_folder = ?');
        $query->execute(array($id));
        $result = $query->fetch();
        return is_array($result) ? $result['id_folder'] : false;
    }
    
}