<?php

class TaskFunctions
{
    public function startTask($task_id)
    {
        require_once('database/Database.class.php');
        require_once('src/Info.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            //get time when started
            $sql = "SELECT * FROM tasks WHERE id=:id";
            $st = $conn->prepare($sql);
            $data = ['id' => $task_id];
            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll()[0];
            $info = new Info();

            //get time_spent
            $time_spent = $result['time_spent'];
            $exp_earned = $result['time_spent'] * $info->getExpPerMin()[$result['difficulty_id']] / 60;

            $sql = "UPDATE tasks SET started=:started, stopped = null, status_id=1 WHERE id=:id";
            $st = $conn->prepare($sql);


            $data = [
                'started' => $info->getTime(),
                'id' => $task_id
            ];

            $st->execute($data);
            $db_obj->disconnect();
            return [
                'result' => true,
                'status' => 'started',
                'time_spent' => $time_spent,
                'exp_earned' => $exp_earned,
                'exp_per_min' => $info->getExpPerMin()[$result['difficulty_id']]
            ];
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in TaskFunctions.class.php:startTask(). Error info: '.$e->getMessage());
            return false;
        }
    }

    public function pauseTask($task_id)
    {
        require_once('database/Database.class.php');
        require_once('src/Info.class.php');
        require_once('src/Calc.class.php');

        try
        {
            $db_obj = new Database();
            $conn = $db_obj->connect();

            $info = new Info();
            $paused = $info->getTime();

            //get time when started
            $sql = "SELECT * FROM tasks WHERE id=:id";
            $st = $conn->prepare($sql);
            $data = ['id' => $task_id];
            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll()[0];

            //check if task is started
            $started = $result['started'];
            $stopped = $result['stopped'];

            if($started==null || ($stopped!==null && $stopped > $started))
            {
                return [
                    'result' => false,
                    'status' => 'not started',
                    'time_spent' => null,
                    'exp_earned' => null,
                    'exp_per_min' => null
                ];
            }

            $calc = new Calc();
            $diff = $calc->getSecondsBetweenDates($started,$paused);

            $new_time_spent = $result['time_spent'] + $diff;
            $multiplier = $info->getExpPerMin();

            switch($result['difficulty_id'])
            {
                case 0: $new_exp = $new_time_spent/(60/$multiplier[0]); break; //easy task
                case 1: $new_exp = $new_time_spent/(60/$multiplier[1]); break; //medium task
                case 2: $new_exp = $new_time_spent/(60/$multiplier[2]); break; //hard task
                default: $new_exp = $new_time_spent/(60/$multiplier[0]); break;
            }

            $sql = "UPDATE tasks SET stopped=:stopped, time_spent=:new_time_spent, total_exp=:new_exp, status_id=2 WHERE id=:id";
            $st = $conn->prepare($sql);

            $data = [
                'stopped' => $paused,
                'id' => $task_id,
                'new_time_spent' => $new_time_spent,
                'new_exp' => $new_exp
            ];

            $st->execute($data);
            $db_obj->disconnect();
            return [
                'result' => true,
                'status' => 'paused',
                'time_spent' => $new_time_spent,
                'exp_earned' => $new_exp,
                'exp_per_min' => $info->getExpPerMin()[$result['difficulty_id']]
            ];
        }
        catch(PDOException $e)
        {
            require_once('src/Logger.class.php');
            $logger = new Logger();
            $logger->log('PDO Exception in TaskFunctions.class.php:startTask(). Error info: '.$e->getMessage());
            return [
                'result' => false,
                'status' => 'exception',
                'time_spent' => null,
                'exp_earned' => null,
                'exp_per_min' => null
            ];
        }
    }
}

?>