<?php
require_once __DIR__."/../Config/co.php";
class Model{
    private static $instance = null;
    private $pdo;
    private function __construct() {
        require_once __DIR__."/../Config/co.php";
        $hostname = co::getHostname();  
        $databaseName = co::getDatabase();
        $login = co::getLogin();
        $password = co::getPassword();


        $this->pdo = new PDO("mysql:host=$hostname;dbname=$databaseName", $login, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    
    private static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new Model(); 
        }
        return static::$instance; 
    }

    public static function getPdo() {
        return static::getInstance()->pdo;
    }
}

?>