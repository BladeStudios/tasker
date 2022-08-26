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
        echo '<br>
        <div id="title">'.$lang['task-history'].'</div>
        <br>';
        if(isset($_SESSION['user']['id']))
        {
            require_once('database/Database.class.php');
            $database = new Database();
            if(!$database->doesUserExist($_SESSION['user']['id']))
            {
                unset($_SESSION['loggedin']);
                unset($_SESSION['user']);
                $_SESSION['error'] = $lang['e_account_permissions'];
                exit(header('Location: login.php'));
            }

            $ok = true; //flag to check if all data from the database was received successfully

            require_once('database/Task.class.php');
            $task_obj = new Task();
            $taskList = $task_obj->getTaskListForUser($_SESSION['user']['id'],'finished');
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

                switch($taskInfo['task_statuses'][$task['status_id']])
                {
                    case 'TO DO': $statusColor = '#3399FF'; break;
                    case 'IN PROGRESS': $statusColor = '#00CC00'; break;
                    case 'PAUSED': $statusColor = '#FFFF33'; break;
                    case 'DONE': $statusColor = '#FF3333'; break;
                    default: $statusColor = '#FFFFFF'; break;
                }

                if(empty($task['deadline'])) $task['deadline'] = 'none';

                if($info->isTaskRunning($task['started'],$task['stopped']))
                {
                    $startDisabled = ' disabled';
                    $pauseDisabled = '';
                    $finishDisabled = '';
                    $timeSpent = $task['time_spent'] + (strtotime($info->getTime()) - strtotime($task['started']));
                    $isTaskFinished = 'false';
                }
                else if($task['status_id']==3) //task finished
                {
                    $startDisabled = ' disabled';
                    $pauseDisabled = ' disabled';
                    $finishDisabled = ' disabled';
                    $timeSpent = $task['time_spent'];
                    $isTaskFinished = 'true';
                }
                else
                {
                    $startDisabled = '';
                    $pauseDisabled = ' disabled';
                    $finishDisabled = '';
                    $timeSpent = $task['time_spent'];
                    $isTaskFinished = 'false';
                }

                echo '
                <div class="task" data-task-id="'.$task['id'].'" data-task-difficulty-id="'.$task['difficulty_id'].'" data-time-spent="'.$timeSpent.'" data-exp-per-min="'.$info->getExpPerMin()[$task['difficulty_id']].'" data-is-task-finished="'.$isTaskFinished.'">
                    <div class="task-name-row">
                        <div class="task-name">'.$task['name'].'</div>
                        <div class="task-status" style="background-color: '.$statusColor.'">'.$taskInfo['task_statuses'][$task['status_id']].'</div>
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
                        <button class="btnTaskFinish btn btn-danger"'.$finishDisabled.'>FINISH TASK</button>
                    </div>
                    <div class="task-more-details-row">
                        &#9660;more details
                    </div>
                </div>
                <div class="task-more-details" data-task-id="'.$task['id'].'">
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
        $('.task-more-details-row').click(function(){
            taskElement = $(this).parent();
            taskId = taskElement.data('task-id');

            moreDetailsElement = $('.task-more-details[data-task-id="'+taskId+'"');
            moreDetailsElement.toggle();
        });
    });
</script>

</body>
</html>