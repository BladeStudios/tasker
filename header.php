<div id="top">
    <div id="app_title">TASKER</div>
    <div id="app_desc">Online Task Manager</div>
</div>
<div id="menu">
    <a href="index.php" class="btn btn-secondary menu-link"><?php echo $lang['home']; ?></a>
    
    <?php 
        if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']===false)
        {
            echo '<a href="login.php" class="btn btn-secondary menu-link">'.$lang['log_in'].'</a>&nbsp;';
            echo '<a href="register.php" class="btn btn-secondary menu-link">'.$lang['register'].'</a>';
        }
        else
        {
            echo '<a href="logout.php" class="btn btn-secondary menu-link">'.$lang['logged_in_as'].$_SESSION['login'].$lang['logout'].'</a>';
        }
    
    ?>
   
</div>