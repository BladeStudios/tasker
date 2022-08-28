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

<?php

    if(isset($_POST['name'])) //add task
    {
        require_once('database/Task.class.php');
        $task = new Task();
        if($task->addTask($_SESSION['user']['id'], $_SESSION['user']['id'],0,$_POST['name'],$_POST['description'],$_POST['priority'],$_POST['visibility'],null))
            $_SESSION['info'] = $lang['task-added'];
    }

    require('header.php');
?>
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

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
    {
        echo '<div id="addtaskform">
        <form method="post">'.
        $lang['addtask_name'].'<br>
        <input type="text" name="name" size="60"/><br><br>'.
        $lang['addtask_description'].'<br>
        <textarea name="description" rows="5" cols="100"></textarea><br><br>'.
        $lang['addtask_priority'].'<br>
        <select name="priority">
            <option value="0">'.$lang['addtask_low'].'</option>
            <option value="1">'.$lang['addtask_medium'].'</option>
            <option value="2">'.$lang['addtask_high'].'</option>
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