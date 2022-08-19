<?php

class Task
{
    private $tableName = 'tasks';

    public function addTask($creator_id,$executor_id,$type_id,$name,$description,$difficulty_id,$base_exp,$time_exp,$visibility_id,$deadline)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "INSERT INTO ".$this->tableName." (creator_id, executor_id, type_id, name, description, time_spent, started, stopped, status_id, difficulty_id, base_exp, time_exp, visibility_id, deadline)
            VALUES (:creator_id, :executor_id, :type_id, :name, :description, 0, null, null, 0, :difficulty_id, :base_exp, :time_exp, :visibility_id, :deadline)";

            $st = $conn->prepare($sql);

            $data = [
                'creator_id' => $creator_id,
                'executor_id' => $executor_id,
                'type_id' => $type_id,
                'name' => $name,
                'description' => $description,
                'difficulty_id' => $difficulty_id,
                'base_exp' => $base_exp,
                'time_exp' => $time_exp,
                'visibility_id' => $visibility_id,
                'deadline' => $deadline
            ];

            $st->execute($data);
            $db_obj->disconnect();
            return true;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Task.class.php:addTask(). Error info: '.$e->getMessage());
            return false;
        }
    }

    public function getTaskTypeId($type_name)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM task_types";

            $st = $conn->prepare($sql);

            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            $result = $st->fetchAll();
            foreach($result as $el)
            {
                if($el['name']===$type_name)
                    return $el['id'];
            }
            return false;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in User.class.php:getTaskTypeId(). Error info: '.$e->getMessage());
            return false;
        }
    }

    public function getTaskDifficultyId($difficulty_name)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM task_difficulties";

            $st = $conn->prepare($sql);

            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            $result = $st->fetchAll();
            foreach($result as $el)
            {
                if($el['name']===$difficulty_name)
                    return $el['id'];
            }
            return false;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in User.class.php:getTaskDifficultyId(). Error info: '.$e->getMessage());
            return false;
        }
    }

    public function getTaskVisibilityId($visibility_name)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM task_visibilities";

            $st = $conn->prepare($sql);

            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            $result = $st->fetchAll();
            foreach($result as $el)
            {
                if($el['name']===$visibility_name)
                    return $el['id'];
            }
            return false;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in User.class.php:getTaskDifficultyId(). Error info: '.$e->getMessage());
            return false;
        }
    }
}

?>