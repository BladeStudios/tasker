<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require('src/header.php'); ?>
<div id="container"></div>
<?php require('src/footer.php'); ?>

</body>
</html>