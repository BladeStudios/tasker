<?php

class Task
{
    private $tableName = 'tasks';

    public function addTask($creator_id,$executor_id,$type_id,$name,$description,$priority_id,$visibility_id,$deadline)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "INSERT INTO ".$this->tableName." (creator_id, executor_id, type_id, name, description, created, time_spent, started, stopped, status_id, priority_id, visibility_id, deadline, total_exp)
            VALUES (:creator_id, :executor_id, :type_id, :name, :description, :created, 0, null, null, 0, :priority_id, :visibility_id, :deadline, :total_exp)";

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
                'priority_id' => $priority_id,
                'visibility_id' => $visibility_id,
                'deadline' => $deadline,
                'total_exp' => 0
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

    public function editTask($task_id,$name,$description,$priority_id,$editor_id,$time_stopped,$prev_stopped,$prev_time_spent)
    {
        require_once('database/Database.class.php');

        try
        {
            require_once('src/Calc.class.php');
            $calc = new Calc();
            $diff = $calc->getSecondsBetweenDates($time_stopped,$prev_stopped);

            require_once('src/Info.class.php');
            $info = new Info();

            $new_time_spent = $prev_time_spent - $diff;
            $new_exp = $info->getExpByTimeSpent($new_time_spent,$priority_id);

            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "UPDATE ".$this->tableName." SET name=:name, description=:description, priority_id=:priority_id, editor_id=:editor_id, edited=:edited, stopped=:time_stopped, time_spent=:new_time_spent, total_exp=:new_exp WHERE id=:task_id";

            $st = $conn->prepare($sql);

            require_once('src/Info.class.php');
            $info = new Info();
            $edited = $info->getTime();

            $data = [
                'name' => $name,
                'description' => $description,
                'priority_id' => $priority_id,
                'editor_id' => $editor_id,
                'edited' => $edited,
                'task_id' => $task_id,
                'time_stopped' => $time_stopped,
                'new_time_spent' => $new_time_spent,
                'new_exp' => $new_exp
            ];

            $st->execute($data);
            $db_obj->disconnect();
            return true;
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Task.class.php:editTask(). Error info: '.$e->getMessage());
            return false;
        }
    }

    public function getTask($task_id)
    {
        require_once('database/Database.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $sql = "SELECT * FROM ".$this->tableName." WHERE id = :task_id";

            $st = $conn->prepare($sql);

            $data = [ 'task_id' => $task_id ];

            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $db_obj->disconnect();
            return $st->fetchAll()[0];
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in Task.class.php:getTask(). Error info: '.$e->getMessage());
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
                case 'all': $args = " ORDER BY status_id DESC, priority_id DESC"; break;
                case 'unfinished': $args = " AND status_id != 3 ORDER BY status_id DESC, priority_id DESC, stopped DESC, created DESC"; break;
                case 'finished': $args = " AND status_id = 3 ORDER BY stopped DESC"; break;
                default: $args = " ORDER BY status_id DESC, priority_id DESC"; break;
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
            $logger->log('PDO Exception in Task.class.php:getTaskListForUser(). Error info: '.$e->getMessage());
            return false;
        }
    }
}

?>