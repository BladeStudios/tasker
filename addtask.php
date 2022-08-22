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
<div id="title"><?php echo $lang['title_addtask']; ?></div>
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

    if(isset($_POST['name'])) //add task
    {
        require_once('database/Task.class.php');
        $task = new Task();
        $task->addTask($_SESSION['id'], $_SESSION['id'],0,$_POST['name'],$_POST['description'],$_POST['difficulty'],$_POST['visibility'],null);
    }

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
    {
        echo '<div id="addtaskform">
        <form method="post">'.
        $lang['label1'].'<br>
        <input type="text" name="name"/><br><br>'.
        $lang['label1'].'<br>
        <textarea name="description" rows="5" cols="100"></textarea><br><br>'.
        $lang['label1'].'<br>
        <select name="difficulty">
            <option value="0">Easy</option>
            <option value="1">Medium</option>
            <option value="2">Hard</option>
        </select><br><br>
        <input type="hidden" value="3" name="visibility"/><br><br>
        <input type="submit" class="btn btn-success center-in-div" value="'.$lang['add-task'].'"/>
        </form>';
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