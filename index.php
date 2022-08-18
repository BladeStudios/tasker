<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Index - Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="libs/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require('header.php'); ?>
<div id="container">
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
?>
<div id="text"><?php echo '<br>'.$lang['tasker_description']; ?></div>
</div>
<?php require('footer.php'); ?>

</body>
</html>