<?php
    session_start();

    require_once('src/MenuFunctions.class.php');

    $menuFunctions = new MenuFunctions();
    if($menuFunctions->isLoggedIn())
        $menuFunctions->logout($_SESSION['id']);

    exit(header('Location: index.php'));

?>