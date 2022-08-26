<?php

class Task
{
    private $tableName = 'tasks';

    public function addTask($creator_id,$executor_id,$type_id,$name,$description,$difficulty_id,$visibility_id,$deadline)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "INSERT INTO ".$this->tableName." (creator_id, executor_id, type_id, name, description, created, time_spent, started, stopped, status_id, difficulty_id, visibility_id, deadline)
            VALUES (:creator_id, :executor_id, :type_id, :name, :description, :created, 0, null, null, 0, :difficulty_id, :visibility_id, :deadline)";

            $st = $conn->prepare($sql);

            require_once('src/Info.class.php');
            $info = new Info();
            $created = $info->getTime();

            $data = [
                'creator_id' => $creator_id,
                'executor_id' => $executor_id,
                'type_id' => $type_id,
                'name' => $name,
                'description' => $description,
                'created' => $created,
                'difficulty_id' => $difficulty_id,
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

    public function getTaskListForUser($user_id, $option)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            switch($option)
            {
                case 'all': $args = ""; break;
                case 'unfinished': $args = " AND status_id != 3"; break;
                case 'finished': $args = " AND status_id = 3"; break;
                default: $args = ""; break;
            }

            $sql = "SELECT * FROM ".$this->tableName." WHERE executor_id = :user_id".$args;

            $st = $conn->prepare($sql);

            $data = [
                'user_id' => $user_id
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
            $logger->log('PDO Exception in User.class.php:getTaskDifficultyId(). Error info: '.$e->getMessage());
            return false;
        }
    }
}

?>