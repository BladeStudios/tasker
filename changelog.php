<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'en_US';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Tasker</title>
    <link rel="stylesheet" href="libs/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require('header.php'); ?>
<div id="container">
<br>
<div id="title"><?php echo $lang['changelog']; ?></div>
<br>
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
        echo '<div id="changelog">'.nl2br(file_get_contents('docs/changelog')).'</div>';
    }
    else
    {
        exit(header('Location: index.php'));
    }
?>
</div>
<?php require('footer.php'); ?>

</body>
</html>