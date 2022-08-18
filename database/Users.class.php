<?php

class User
{
    private $tableName = 'users';

    public function addUser($login, $password, $email, $ip, $time, $browser, $system)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "INSERT INTO ".$this->tableName."
            (login, password, salt, email, position_id, status_id, last_ip, current_ip, online_from, total_online_time, last_activity, browser, system, create_time,
            banned_until, level, experience, timezone_id)
            VALUES (:login, :password, :salt, :email, :position_id, :status_id, :last_ip, :current_ip, :online_from, :total_online_time, :last_activity, :browser, :system, :create_time,
            :banned_until, :level, :experience, :timezone_id)";

            $st = $conn->prepare($sql);
            $st->bindParam('login',$login);
            $st->bindParam('password',$password);
            $st->bindParam('salt',$salt);
            $st->bindParam('email',$email);
            $st->bindParam('position_id',$position_id);
            $st->bindParam('status_id',$status_id);
            $st->bindParam('last_ip',$last_ip);
            $st->bindParam('current_ip',$current_ip);
            $st->bindParam('online_from',$online_from);
            $st->bindParam('total_online_time',$total_online_time);
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
            $online_from = null;
            $total_online_time = 0;
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
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:addUser(). Error info: '.$e->getMessage());
            return false;
        }
        
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
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:getIdByEmail(). Error info: '.$e->getMessage());
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
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:getIdByLogin(). Error info: '.$e->getMessage());
            return -1;
        }  
    }

    public function getUserByLogin($login)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM users WHERE login=:login";

            $st = $conn->prepare($sql);
            //$st->bindParam('login',$login);
            $data = [
                'login' => $login
            ];

            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return $st->fetchAll();
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:getUserByLogin(). Error info: '.$e->getMessage());
            return -1;
        }  
    }

    public function updateUser($id, $last_ip, $current_ip, $current_time, $user_os, $user_browser)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "UPDATE users SET last_ip=:last_ip, current_ip=:current_ip, status_id=1, online_from=:current_time, last_activity=:current_time, system=:user_os, browser=:user_browser WHERE id=:id";

            $st = $conn->prepare($sql);
            
            $data = [
                'id' => $id,
                'last_ip' => $last_ip,
                'current_ip' => $current_ip,
                'current_time' => $current_time,
                'user_os' => $user_os,
                'user_browser' => $user_browser
            ];

            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return true;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:updateUser(). Error info: '.$e->getMessage());
            return false;
        } 
    }

    public function onLogout($id)
    {
        require_once('database/Database.class.php');
        require_once('src/Info.class.php');
        require_once('src/Calc.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "UPDATE users SET status_id=0, last_activity=:current_time, total_online_time=:new_total_online_time WHERE id=:id";

            $st = $conn->prepare($sql);

            $info = new Info();
            $current_time = $info->getTime();

            $user = $this->getUserById($id);

            $calc = new Calc();
            $online_time = $calc->getSecondsBetweenDates($user['online_from'],$current_time);

            $new_total_online_time = $user['total_online_time'] + $online_time;

            
            $data = [
                'id' => $id,
                'current_time' => $current_time,
                'new_total_online_time' => $new_total_online_time
            ];

            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return true;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:updateUser(). Error info: '.$e->getMessage());
            return false;
        } 
    }

    public function getUserById($id)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM users WHERE id=:id";

            $st = $conn->prepare($sql);
            //$st->bindParam('login',$login);
            $data = [
                'id' => $id
            ];

            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return $st->fetchAll();
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Users.class.php:getUserById(). Error info: '.$e->getMessage());
            return false;
        }
    }
}


?>