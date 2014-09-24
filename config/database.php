<?php

class DatabaseConfig
{

    private $db;
    private $validates = array('host', 'database', 'username', 'password');

    private function __construct($db)
    {
        $this->db = $db;
        $this->_throwIsValid();
        $this->_load();
    }

    private function _load()
    {
        $db =& $this->db;
        ORM::configure("mysql:host={$db->host};dbname={$db->database}");
        ORM::configure('username', $db->username);
        ORM::configure('password', $db->password);
    }

    private function _throwIsValid()
    {
        $db =& $this->db;
        foreach ($this->validates as $key) {
            if (!property_exists($db, $key)) {
                throw new Exception("has not property [${key}]");
            }
        }
        return true;
    }

    public static function load($db)
    {
        new self($db);
    }

}