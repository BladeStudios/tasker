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

            if($result['status_id']==3)
            {
                return [
                    'result' => false,
                    'status' => 'already finished',
                    'time_spent' => null,
                    'exp_earned' => null,
                    'exp_per_min' => null
                ];
            }

            $info = new Info();

            //get time_spent
            $time_spent = $result['time_spent'];
            $exp_earned = $result['time_spent'] * $info->getExpPerMin()[$result['priority_id']] / 60;

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
                'exp_per_min' => $info->getExpPerMin()[$result['priority_id']]
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

            if($result['status_id']==3)
            {
                return [
                    'result' => false,
                    'status' => 'already finished',
                    'time_spent' => null,
                    'exp_earned' => null,
                    'exp_per_min' => null
                ];
            }

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

            switch($result['priority_id'])
            {
                case 0: $new_exp = floor($new_time_spent/(60/$multiplier[0])); break; //low priority
                case 1: $new_exp = floor($new_time_spent/(60/$multiplier[1])); break; //medium priority
                case 2: $new_exp = floor($new_time_spent/(60/$multiplier[2])); break; //high priority
                default: $new_exp = floor($new_time_spent/(60/$multiplier[0])); break;
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
                'exp_per_min' => $info->getExpPerMin()[$result['priority_id']]
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

    public function finishTask($task_id)
    {
        require_once('database/Database.class.php');
        require_once('src/Info.class.php');
        require_once('src/Calc.class.php');
        require_once('src/Exp.class.php');

        try
        {
            session_start();
            $db_obj = new Database();
            $conn = $db_obj->connect();

            //get time when started
            $sql = "SELECT * FROM tasks WHERE id=:id";
            $st = $conn->prepare($sql);
            $data = ['id' => $task_id];
            $st->execute($data);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll()[0];

            if($result['status_id']==3)
            {
                return [
                    'result' => false,
                    'status' => 'already finished',
                    'time_spent' => null,
                    'exp_earned' => null,
                    'exp_per_min' => null
                ];
            }

            //get user data
            $sql2 = "SELECT * FROM users WHERE id=:user_id";
            $st = $conn->prepare($sql2);
            $data2 = ['user_id' => $result['executor_id']];
            $st->execute($data2);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result2 = $st->fetchAll()[0];

            //check if task is started
            $started = $result['started'];
            $stopped = $result['stopped'];

            $info = new Info();
            $finished= $info->getTime();

            $multiplier = $info->getExpPerMin();
            $minimumExp = $info->getMinimumExp();

            $old_experience = $result2['experience'];
            $exp = new Exp();

            if($started == null) //START never clicked
            {
                switch($result['priority_id'])
                {
                    case 0: $new_exp = $minimumExp[0]; break; //low priority
                    case 1: $new_exp = $minimumExp[1]; break; //medium priority
                    case 2: $new_exp = $minimumExp[2]; break; //high priority
                    default: $new_exp = $minimumExp[0]; break;
                }

                $sql = "UPDATE tasks SET stopped=:finished, time_spent=:time_spent, total_exp=:new_exp, status_id=3 WHERE id=:id";
                $st = $conn->prepare($sql);

                $data = [
                    'finished' => $finished,
                    'id' => $task_id,
                    'time_spent' => 0,
                    'new_exp' => $new_exp
                ];

                $st->execute($data);

                //update users table
                $new_experience = $old_experience + $new_exp;
                $new_level = $exp->getLevelAndPercentageByExp($new_experience)['level'];
                $_SESSION['user']['experience'] = $new_experience;
                $_SESSION['user']['level'] = $new_level;

                $sql2 = "UPDATE users SET level=:level, experience=:experience WHERE id=:executor_id";
                $st = $conn->prepare($sql2);

                $data2 = [
                    'executor_id' => $result['executor_id'],
                    'level' => $new_level,
                    'experience' => $new_experience
                ];

                $st->execute($data2);
                $should_clear_interval = false;
            }
            else if($stopped == null || $stopped < $started) //TASK STARTED, NOT PAUSED
            {
                $calc = new Calc();
                $diff = $calc->getSecondsBetweenDates($started,$finished);
                $time_spent = $result['time_spent'] + $diff;

                switch($result['priority_id'])
                {
                    case 0: $new_exp = max(floor($time_spent/(60/$multiplier[0])),$minimumExp[0]); break; //low priority
                    case 1: $new_exp = max(floor($time_spent/(60/$multiplier[1])),$minimumExp[1]); break; //medium priority
                    case 2: $new_exp = max(floor($time_spent/(60/$multiplier[2])),$minimumExp[2]); break; //high priority
                    default: $new_exp = max(floor($time_spent/(60/$multiplier[0])),$minimumExp[0]); break;
                }

                $sql = "UPDATE tasks SET stopped=:finished, time_spent=:time_spent, total_exp=:new_exp, status_id=3 WHERE id=:id";
                $st = $conn->prepare($sql);

                $data = [
                    'finished' => $finished,
                    'id' => $task_id,
                    'time_spent' => $time_spent,
                    'new_exp' => $new_exp
                ];

                $st->execute($data);

                //update users table
                $new_experience = $old_experience + $new_exp;
                $new_level = $exp->getLevelAndPercentageByExp($new_experience)['level'];
                $_SESSION['user']['experience'] = $new_experience;
                $_SESSION['user']['level'] = $new_level;

                $new_time_spent_tasks_overall = $result2['time_spent_tasks_overall'] + $time_spent;

                $sql2 = "UPDATE users SET level=:level, experience=:experience, time_spent_tasks_overall=:new_time_spent_tasks_overall WHERE id=:executor_id";
                $st = $conn->prepare($sql2);

                $data2 = [
                    'executor_id' => $result['executor_id'],
                    'level' => $new_level,
                    'experience' => $new_experience,
                    'new_time_spent_tasks_overall' => $new_time_spent_tasks_overall
                ];

                $st->execute($data2);
                $should_clear_interval = true;
                
            }
            else if($stopped > $started) //TASK PAUSED
            {
                $time_spent = $result['time_spent'];
                $new_time_spent_tasks_overall = $result2['time_spent_tasks_overall'] + $time_spent;

                switch($result['priority_id'])
                {
                    case 0: $new_exp = max(floor($time_spent/(60/$multiplier[0])),$minimumExp[0]); break; //low priority
                    case 1: $new_exp = max(floor($time_spent/(60/$multiplier[1])),$minimumExp[1]); break; //medium priority
                    case 2: $new_exp = max(floor($time_spent/(60/$multiplier[2])),$minimumExp[2]); break; //high priority
                    default: $new_exp = max(floor($time_spent/(60/$multiplier[0])),$minimumExp[0]); break;
                }

                $sql = "UPDATE tasks SET status_id=3 WHERE id=:id";
                $data = [ 'id' => $task_id ];
                $st = $conn->prepare($sql);
                $st->execute($data);

                //update users table
                $new_experience = $old_experience + $new_exp;
                $new_level = $exp->getLevelAndPercentageByExp($new_experience)['level'];
                $_SESSION['user']['experience'] = $new_experience;
                $_SESSION['user']['level'] = $new_level;

                $sql2 = "UPDATE users SET level=:level, experience=:experience, time_spent_tasks_overall=:new_time_spent_tasks_overall WHERE id=:executor_id";
                $st = $conn->prepare($sql2);

                $data2 = [
                    'executor_id' => $result['executor_id'],
                    'level' => $new_level,
                    'experience' => $new_experience,
                    'new_time_spent_tasks_overall' => $new_time_spent_tasks_overall
                ];

                $st->execute($data2);
                $should_clear_interval = false;
            }

            $db_obj->disconnect();

            $level = $exp->getLevelAndPercentageByExp($_SESSION['user']['experience'])['level'];
            $exp_gained_on_this_level = $exp->getLevelAndPercentageByExp($_SESSION['user']['experience'])['exp_gained_on_this_level'];
            $exp_to_advance = $exp->getLevelAndPercentageByExp($_SESSION['user']['experience'])['exp_to_advance'];

            return [
                'result' => true,
                'status' => 'finished',
                'login' => $result2['login'],
                'level' => $level,
                'exp_earned' => $new_exp,
                'exp_gained' => $exp_gained_on_this_level,
                'exp_to_advance' => $exp_to_advance,
                'should_clear_interval' => $should_clear_interval
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