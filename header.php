<div id="top">
    <div id="app_title">TASKER</div>
    <div id="app_desc">Online Task Manager</div>
</div>
<div id="menu">
    <a href="index.php" class="menu_link"><?php echo $lang['home']; ?></a>
    
    <?php 
        if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']===false)
        {
            echo '<a href="login.php" class="menu_link">'.$lang['log_in'].'</a>';
        }
        else
        {
            echo '<a href="logout.php" class="menu_link">'.$lang['logged_in_as'].$_SESSION['login'].$lang['logout'].'</a>';
        }
        
        if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']===false)
            echo '<a href="register.php" class="menu_link">'.$lang['register'].'</a>';
    
    ?>
   
</div>