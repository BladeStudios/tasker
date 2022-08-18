<?php
    session_start();

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
        exit(header('Location: index.php'));

    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Log in - Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="libs/bootstrap-3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require('header.php'); ?>
<div id="container">
    <br>
    <?php
        if(isset($_SESSION['error']))
        {
            echo '<div style="color: red; font-family: Verdana; font-size: 15pt">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
        if(isset($_SESSION['registered']) && $_SESSION['registered']===true)
        {
            echo '<div style="color: green; font-family: Verdana; font-size: 15pt">'.$lang['registration_successful'].'</div>';
            $_SESSION['registered']=false;
        }
    ?>
    <div id="loginform">
        <form action="loginengine.php" method="post">
            <?php echo $lang["login"] ?><br/><input type="text" name="login"/><br/>
            <?php echo $lang["password"] ?><br/><input type="password" name="password"/><br/>
            </br><input type="submit" class="btn btn-success center-in-div" value="<?php echo $lang["loginbutton"] ?>"/>
        </form>
    </div>

</div>
<?php require('footer.php'); ?>

</body>
</html>