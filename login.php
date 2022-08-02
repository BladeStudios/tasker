<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Log in - Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require('header.php'); ?>
<div id="container">
    <br>
    <div id="registerform">
        <form action="src/loginengine.php" method="post">
            <?php echo $lang["nick"] ?><br/><input type="text" name="login"/><br/>
            <?php echo $lang["password"] ?><br/><input type="password" name="password"/><br/>
            </br><input type="submit" class="btn btn-success center-in-div" value="<?php echo $lang["loginbutton"] ?>"/>
        </form>
    </div>

</div>
<?php require('footer.php'); ?>

</body>
</html>