<?php

class Database
{
    private $dbConn;

    public function connect()
    {
        require('db_config.php');
        require_once('src/Logger.class.php');

        try
        {
            $this->dbConn = new PDO("mysql:host=$host;dbname=$db_name",$db_user,$db_password);
            $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->dbConn;
        }
        catch(PDOException $e)
        {
            $logger = new Logger();
            $logger->log('Cannot connect to database '.$db_name.'. Error: '.$e->getMessage());
            return null;
        }
    }

    public function disconnect()
    {
        $this->dbConn = null;
    }

    public function getTable($table_name)
    {
        try
        {
            $conn = $this->connect();
            $sql = "SELECT * FROM ".$table_name;

            $st = $conn->prepare($sql);
            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $this->disconnect();
            return $st->fetchAll();
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Database.class.php:getTable(). Error info: '.$e->getMessage());
            return false;
        }
    }
}


?>