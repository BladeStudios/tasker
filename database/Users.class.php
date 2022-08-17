<?php

class User
{
    private $tableName = 'users';

    public function addUser($login, $password, $email, $ip, $time, $browser, $system)
    {
        require_once('database/Database.class.php');

        $db_obj = new Database();
        $conn = $db_obj->connect();

        $sql = "INSERT INTO ".$this->tableName."
        (login, password, salt, email, position_id, status_id, current_ip, last_ip, last_activity, browser, system, create_time,
        banned_until, level, experience, timezone_id)
        VALUES (:login, :password, :salt, :email, :position_id, :status_id, :current_ip, :last_ip, :last_activity, :browser, :system, :create_time,
        :banned_until, :level, :experience, :timezone_id)";

        $st = $conn->prepare($sql);
        $st->bindParam('login',$login);
        $st->bindParam('password',$password);
        $st->bindParam('salt',$salt);
        $st->bindParam('email',$email);
        $st->bindParam('position_id',$position_id);
        $st->bindParam('status_id',$status_id);
        $st->bindParam('current_ip',$current_ip);
        $st->bindParam('last_ip',$last_ip);
        $st->bindParam('last_activity',$last_activity);
        $st->bindParam('browser',$browser);
        $st->bindParam('system',$system);
        $st->bindParam('create_time',$create_time);
        $st->bindParam('banned_until',$banned_until);
        $st->bindParam('level',$level);
        $st->bindParam('experience',$experience);
        $st->bindParam('timezone_id',$timezone_id);

        $salt = null;
        $position_id = 0;
        $status_id = 0;
        $current_ip = $ip;
        $last_ip = $ip;
        $last_activity = $time;
        $create_time = $time;
        $banned_until = null;
        $level = 1;
        $experience = 0;
        $timezone_id = 1;

        

        $st->execute();
        $db_obj->disconnect();
        return true;
    }

    public function getIdByEmail($email)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT id FROM users WHERE email=:email";

            $st = $conn->prepare($sql);
            $st->bindParam('email',$email);

            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return $st->fetchAll();
        }
        catch(PDOException $e)
        {
            echo "Error: ".$e->getMessage();
            return -1;
        }  
    }

    public function getIdByLogin($login)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT id FROM users WHERE login=:login";

            $st = $conn->prepare($sql);
            $st->bindParam('login',$login);

            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return $st->fetchAll();
        }
        catch(PDOException $e)
        {
            echo "Error: ".$e->getMessage();
            return -1;
        }  
    }
}


?>