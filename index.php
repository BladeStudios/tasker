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
        //
        if(isset($_SESSION['id']))
        {
            require_once('database/Task.class.php');
            $task_obj = new Task();
            $tasklist = $task_obj->getTaskListForUser($_SESSION['id']);
            foreach($tasklist as $task)
            {
                echo '
                <div class="task">
                    <div class="task-name-row">
                        <span class="task-name">taskname</span>
                        <span class="task-status">TODO</span>
                    </div>
                    <div class="task-description">
                        description description description description description description description description description description description description description description description description description description description description
                    </div>
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
                            <td class="task-deadline">t</td>
                            <td class="task-difficulty">t</td>
                            <td class="task-time-spent">t</td>
                            <td class="task-value">t</td>
                            <td class="task-xp-earned">t</td>
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
                    <div class="task-details-added">sa</div>
                    <div class="task-details-created-by">das</div>
                    <div class="task-details-assigned-to">das</div>
                    <div class="task-details-visibility">dasd</div>
                    <div class="task-details-minimum-xp">das</div>
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

</body>
</html>