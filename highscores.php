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
<div id="title"><?php echo $lang['highscores']; ?></div>
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
        require_once('database/User.class.php');
        $user = new User();
        $highscores = $user->getUsersHighscores();

        echo '<div id="highscores-table-div"><table id="highscores-table">';
        echo '<tr><th id="th-id">#</th><th id="th-nickname">'.$lang['nickname'].'</th><th id="th-level">'.$lang['level'].'</th><th id="th-experience">'.$lang['experience'].'</th></tr>';

        foreach($highscores as $key => $row)
        {
            echo '<tr><td>'.($key+1).'</td><td>'.$row['login'].'</td><td>'.$row['level'].'</td><td>'.$row['experience'].'</td></tr>';
        }
        echo '</table></div>';
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