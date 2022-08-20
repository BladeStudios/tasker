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

            require_once('database/User.class.php');
            $user_obj = new User();

            require_once('database/Task.class.php');
            $task_obj = new Task();
            $taskList = $task_obj->getTaskListForUser($_SESSION['id']);
            if($taskList===false) $ok = false;

            $tableNames = array(
                'task_difficulties',
                'task_statuses',
                'task_types',
                'task_visibilities'
            );

            $taskInfo = array();

            foreach($tableNames as $tableName)
            {
                $taskInfo[$tableName] = array();
                $data = $database->getTable($tableName);
                if(!$data) $ok = false;
                foreach($data as $val)
                {
                    $taskInfo[$tableName][$val['id']] = $val['name'];
                }
            }

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

            foreach($taskList as $task)
            {
                $createdBy = $user_obj->getUserById($task['creator_id'])['login'];

                switch($taskInfo['task_difficulties'][$task['difficulty_id']])
                {
                    case 'Easy': $difficultyColor = 'green'; break;
                    case 'Medium': $difficultyColor = 'yellow'; break;
                    case 'Hard': $difficultyColor = 'red'; break;
                    default: $difficultyColor = 'black';
                }

                if(empty($task['deadline'])) $task['deadline'] = 'none';

                echo '
                <div class="task" data-user-id="'.$task['id'].'">
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
                                <td class="task-time-spent">'.$task['time_spent'].'</td>
                                <td class="task-value">'.$task['time_exp'].' XP/min</td>
                                <td class="task-xp-earned">'.$task['total_exp'].'</td>
                            </tr>
                        </table>
                    </div>
                    <div class="task-buttons">
                        <button class="btnTaskStart btn btn-success">START TASK</button>
                        <button class="btnTaskPause btn btn-warning">PAUSE</button>
                        <button class="btnTaskFinish btn btn-danger">FINISH TASK</button>
                    </div>
                    <div class="task-more-details-row">
                        <a href="#">&#9660;more details</a>
                    </div>
                </div>
                <div class="task-more-details">
                    <div class="task-details-added">Added: '.$task['created'].'</div>
                    <div class="task-details-created-by">Created by: '.$createdBy.'</div>
                    <div class="task-details-assigned-to">Assigned to: '.$_SESSION['login'].'</div>
                    <div class="task-details-visibility">Visibility: '.$taskInfo['task_visibilities'][$task['visibility_id']].'</div>
                    <div class="task-details-minimum-xp">Minimum XP for this task: '.$task['base_exp'].'</div>
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
    $(document).ready(function(){
        $('.btnTaskStart').click(function(){
            taskElement = $(this).parent().parent();
            taskId = taskElement.data('user-id');
            alert('task id=' + taskElement.data('user-id'));
        })
    });
</script>

</body>
</html>