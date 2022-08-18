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
            echo '<a href="index.php" class="btn btn-info menu-link">'.$lang['task-list'].'</a>&nbsp;';
            echo '<a href="addtask.php" class="btn btn-success menu-link">'.$lang['add-task'].'</a>&nbsp;';
            echo '<a href="logout.php" class="btn btn-danger menu-link">'.$lang['logged_in_as'].$_SESSION['login'].$lang['logout'].'</a>';
        }
    
    ?>
   
</div>