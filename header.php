<div id="top">
    <div id="app_title">TASKER</div>
    <div id="app_desc">Online Task Manager</div>
</div>
<div id="menu">
    
    
    <?php
        if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']===false)
        {
            echo '<a href="index.php" class="btn btn-secondary menu-link">'.$lang['home'].'</a>&nbsp;';
            echo '<a href="login.php" class="btn btn-secondary menu-link">'.$lang['log_in'].'</a>&nbsp;';
            echo '<a href="register.php" class="btn btn-secondary menu-link">'.$lang['register'].'</a>';
        }
        else
        {
            require_once('database/Task.class.php');
            $task = new Task();
            $unfinished_tasks = count($task->getTaskListForUser($_SESSION['user']['id'],'unfinished'));
            $finished_tasks = count($task->getTaskListForUser($_SESSION['user']['id'],'finished'));

            require_once('src/Exp.class.php');
            $exp = new Exp();
            $exp_gained_on_this_level = $exp->getLevelAndPercentageByExp($_SESSION['user']['experience'])['exp_gained_on_this_level'];
            $exp_to_advance = $exp->getLevelAndPercentageByExp($_SESSION['user']['experience'])['exp_to_advance'];
            $percentage = floor($exp_gained_on_this_level/$exp_to_advance*100);

            echo '<div id="menu-links">';
            echo '<a href="index.php" class="btn btn-info menu-link">'.$lang['task-list'].' ('.$unfinished_tasks.')</a>';
            echo '<a href="taskhistory.php" class="btn btn-secondary menu-link">'.$lang['task-history'].' ('.$finished_tasks.')</a>';
            echo '<a href="highscores.php" class="btn btn-warning menu-link">'.$lang['highscores'].'</a>';
            echo '<a href="task.php?fn=add" class="btn btn-success menu-link">'.$lang['add-task'].'</a>';
            echo '<a href="logout.php" class="btn btn-danger menu-link">'.$lang['logout'].'</a></div>';

            echo
            '<div id="user">
                <div id="user-left">
                    <div id="nick-and-level">'.$_SESSION['user']['login'].' (Level '.$_SESSION['user']['level'].')</div>
                    <div id="experience-bar">
                        <div id="progress-text">'.$exp_gained_on_this_level.'/'.$exp_to_advance.' XP</div>
                        <div id="progress-bar" style="width: '.$percentage.'%"></div>
                    </div>
                </div>
                <div id="photo"><img src="img/user.png"/></div>
            </div>';
        }
    
    ?>
   
</div>