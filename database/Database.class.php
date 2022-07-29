<?php

class Database
{
    private $dbConn;

    public function connect()
    {
        require_once('db_config.php');
        require('src/Logger.class.php');

        try
        {
            $this->dbConn = new PDO("mysql:host=$host;dbname=$db_name",$db_user,$db_password);
            $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        catch(PDOException $e)
        {
            $logger = new Logger();
            $logger->log('Cannot connect to database '.$db_name.'. Error: '.$e->getMessage());
        }
    }

    public function disconnect()
    {
        $this->dbConn = null;
    }
}


?>