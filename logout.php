<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'en_US';

    require_once('lang/'.$_SESSION['lang'].'.php');
    require_once('src/MenuFunctions.class.php');

    $menuFunctions = new MenuFunctions();
    if($menuFunctions->isLoggedIn())
    {
        if($menuFunctions->logout($_SESSION['user']['id']))
            $_SESSION['info'] = $lang['logout_successful'];
        else
            $_SESSION['error'] = $lang['logout_failed'];
    }
    else
        $_SESSION['error'] = $lang['logout_failed'];

    exit(header('Location: index.php'));

?>