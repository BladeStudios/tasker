<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'en_US';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Index - Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="libs/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="libs/jquery-3.6.0/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php require('header.php'); ?>
<div id="container">
<?php
    if(isset($_SESSION['error']))
    {
        echo '<div style="color: red; font-family: Verdana; font-size: 15pt">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['info']))
    {
        echo '<div style="color: blue; font-family: Verdana; font-size: 15pt">'.$_SESSION['info'].'</div>';
        unset($_SESSION['info']);
    }

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
    {
        if(isset($_SESSION['id']))
        {
            require_once('database/Database.class.php');
            $database = new Database();
            if(!$database->doesUserExist($_SESSION['id']))
            {
                unset($_SESSION['loggedin']);
                unset($_SESSION['id']);
                $_SESSION['error'] = $lang['e_account_permissions'];
                exit(header('Location: login.php'));
            }

            $ok = true; //flag to check if all data from the database was received successfully

            require_once('database/Task.class.php');
            $task_obj = new Task();
            $taskList = $task_obj->getTaskListForUser($_SESSION['id']);
            if($taskList===false) $ok = false;

            if(!$ok)
            {
                $_SESSION['error'] = $lang['e_database'];
                if(isset($_SESSION['error']))
                {
                    echo '<div style="color: red; font-family: Verdana; font-size: 15pt">'.$_SESSION['error'].'</div>';
                    unset($_SESSION['error']);
                }
                exit;
            }

            require_once('src/Info.class.php');
            $info = new Info();

            $taskInfo = [
                'task_difficulties' => $info->getTaskDifficulties(),
                'task_statuses' => $info->getTaskStatuses(),
                'task_types' => $info->getTaskTypes(),
                'task_visibilities' => $info->getTaskVisibilities(),
                'task_exp_per_min' => $info->getExpPerMin(),
                'task_minimum_exp' => $info->getMinimumExp()
            ];

            require_once('database/User.class.php');
            $user_obj = new User();

            foreach($taskList as $task)
            {
                $createdBy = $user_obj->getUserById($task['creator_id'])['login'];

                switch($taskInfo['task_difficulties'][$task['difficulty_id']])
                {
                    case 'Easy': $difficultyColor = 'green'; break;
                    case 'Medium': $difficultyColor = 'orange'; break;
                    case 'Hard': $difficultyColor = 'red'; break;
                    default: $difficultyColor = 'black';
                }

                if(empty($task['deadline'])) $task['deadline'] = 'none';

                if($info->isTaskRunning($task['started'],$task['stopped']))
                {
                    $startDisabled = ' disabled';
                    $pauseDisabled = '';
                }
                else
                {
                    $startDisabled = '';
                    $pauseDisabled = ' disabled';
                }

                echo '
                <div class="task" data-task-id="'.$task['id'].'" data-task-difficulty-id="'.$task['difficulty_id'].'">
                    <div class="task-name-row">
                        <span class="task-name">'.$task['name'].'</span>
                        <span class="task-status">'.$taskInfo['task_statuses'][$task['status_id']].'</span>
                    </div>
                    <div class="task-description">'.$task['description'].'</div>
                    <div class="task-info-table-container">
                        <table class="task-info-table">
                            <tr>
                                <td>Deadline</td>
                                <td>Difficulty</td>
                                <td>Time spent</td>
                                <td>Value</td>
                                <td>XP earned</td>
                            </tr>
                            <tr>
                                <td class="task-deadline">'.$task['deadline'].'</td>
                                <td class="task-difficulty" style="font-weight: bold; color: '.$difficultyColor.'">'.$taskInfo['task_difficulties'][$task['difficulty_id']].'</td>
                                <td class="task-time-spent">'.$info->convertSecondsToTime($task['time_spent']).'</td>
                                <td class="task-value">'.$taskInfo['task_exp_per_min'][$task['difficulty_id']].' XP/min</td>
                                <td class="task-xp-earned">'.$task['total_exp'].'</td>
                            </tr>
                        </table>
                    </div>
                    <div class="task-buttons">
                        <button class="btnTaskStart btn btn-success"'.$startDisabled.'>START TASK</button>
                        <button class="btnTaskPause btn btn-warning"'.$pauseDisabled.'>PAUSE</button>
                        <button class="btnTaskFinish btn btn-danger">FINISH TASK</button>
                    </div>
                    <div class="task-more-details-row">
                        <a href="#">&#9660;more details</a>
                    </div>
                </div>
                <div class="task-more-details">
                    <div class="task-details-added">Added: '.$info->convertTimeForTimezone($task['created'],'Europe/Paris').'</div>
                    <div class="task-details-created-by">Created by: '.$createdBy.'</div>
                    <div class="task-details-assigned-to">Assigned to: '.$_SESSION['login'].'</div>
                    <div class="task-details-visibility">Visibility: '.$taskInfo['task_visibilities'][$task['visibility_id']].'</div>
                    <div class="task-details-minimum-xp">Minimum XP for this task: '.$taskInfo['task_minimum_exp'][$task['difficulty_id']].'</div>
                </div>
                ';
            }
        }
        else
        {
            $_SESSION['error'] = $lang['user_id_missing'];
            $_SESSION['loggedin'] = false;
            exit(header('Location: login.php'));
        }
        
    }
    else
    {
        echo '<div id="text"><br>'.$lang['tasker_description'].'</div>';
    }
?>
</div>
<?php require('footer.php'); ?>

<script type="text/javascript">
    clocks = [];
    $(document).ready(function(){
        $('.btnTaskStart').click(function(){
            taskElement = $(this).parent().parent();
            taskId = taskElement.data('task-id');
            $(this).prop('disabled',true);

            pauseButton = $(this).parent().children('.btnTaskPause');
            pauseButton.prop('disabled',false);

            timeSpentElement = taskElement.find('.task-time-spent');
            toggleTask(taskId, 'start', timeSpentElement);
        });

        $('.btnTaskPause').click(function(){
            taskElement = $(this).parent().parent();
            taskId = taskElement.data('task-id');
            $(this).prop('disabled',true);

            startButton = $(this).parent().children('.btnTaskStart');
            startButton.prop('disabled',false);

            timeSpentElement = taskElement.find('.task-time-spent');
            toggleTask(taskId, 'pause', timeSpentElement);
        });

        function toggleTask(taskId, option, clockElement)
        {
            $.ajax('ajax.php', {
                type: 'POST',
                data: {id: taskId, fun: option},
                success: function(data, status, xhr){
                    toggleClock(taskId, data);
                }
            });
        }

        function toggleClock(taskId, data)
        {
            data = JSON.parse(data);
            switch(data.status)
            {
                case 'started': startClock(taskId, data.time_spent, data.exp_earned, data.exp_per_min); break;
                case 'paused': stopClock(taskId); break;
            }
        }

        function startClock(taskId, starting_seconds, exp_earned, exp_per_min)
        {
            console.log('clock started');
            if(!(clocks[taskId] && clocks[taskId].length)) clocks[taskId] = [];

            clocks[taskId][0] = starting_seconds;
            clocks[taskId][1] = exp_earned;
            clocks[taskId][2] = exp_per_min;

            updateTimer(taskId, clocks[taskId][0]);

            clocks[taskId][3] = setInterval(function(){
                updateTimer(taskId);
            }, 1000);
        }

        function stopClock(taskId)
        {
            console.log('clock stopped');
            clearInterval(clocks[taskId][3]);
        }

        function updateTimer(taskId)
        {
            clocks[taskId][0] += 1;
            if((clocks[taskId][0] % 20 == 0 && clocks[taskId][2] == 3) ||
            (clocks[taskId][0] % 10 == 0 && clocks[taskId][2] == 6) ||
            (clocks[taskId][0] % 5 == 0 && clocks[taskId][2] == 12))
                clocks[taskId][1] += 1;

            timerElement = $('.task[data-task-id="'+taskId+'"').find('.task-time-spent');
            timerElement.text(toHHMMSS(clocks[taskId][0]));

            expEarnedElement = $('.task[data-task-id="'+taskId+'"').find('.task-xp-earned');
            expEarnedElement.text(Math.floor(clocks[taskId][1]));
        }

        function toHHMMSS(number_of_seconds)
        {
            var sec_num = parseInt(number_of_seconds, 10);
            var hours   = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            if (seconds < 10) {seconds = "0"+seconds;}
            return hours+':'+minutes+':'+seconds;
        }
    });
</script>

</body>
</html>