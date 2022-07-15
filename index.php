<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div id="top">
    <div id="app_title">TASKER</div>
    <div id="app_desc">Online Task Manager</div>
</div>
<div id="menu">
    <span class="menu_link"><?php echo $lang['home']; ?></span>
    <span class="menu_link"><?php echo $lang['login']; ?></span>
    <span class="menu_link"><?php echo $lang['register']; ?></span>
</div>
<div id="container"></div>
<footer class="footer">Powered by BladeStudios (C) 2022</footer>

</body>
</html>