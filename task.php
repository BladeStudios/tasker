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

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true && isset($_POST['fn']))
    {
        require_once('database/Task.class.php');
        $task = new Task();

        if($_POST['fn']=='add') //add task
        {
            if(empty($_POST['name']) || mb_strlen($_POST['name'])<3)
                $_SESSION['error'] = $lang['task-name-too-short'];
            else if($task->addTask($_SESSION['user']['id'], $_SESSION['user']['id'],0,$_POST['name'],$_POST['description'],$_POST['priority'],$_POST['visibility'],null))
                $_SESSION['info'] = $lang['task-added'];
        }
        else if($_POST['fn']=='edit' && isset($_POST['id'])) //edit task
        {
            if(empty($_POST['name']) || mb_strlen($_POST['name'])<3)
                $_SESSION['error'] = $lang['task-name-too-short'];
            else if($task->editTask($_POST['id'],$_POST['name'],$_POST['description'],$_POST['priority'],$_SESSION['user']['id']))
            {
                $_SESSION['info'] = $lang['task-edited'];
                exit(header('Location: index.php'));
            }
        }
        else if($_POST['fn']=='delete') //delete task
        {

        }
    }

    require('header.php');
?>
<div id="container">
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

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true && isset($_GET['fn']))
    {
        if($_GET['fn']=='add')
        {
            echo '<div id="title">'.$lang['title_addtask'].'</div><br>';
            echo '<div id="addtaskform">
            <form method="post">'.
            $lang['addtask_name'].'<br>
            <input type="text" name="name" size="60" maxlength="60"/><br><br>'.
            $lang['addtask_description'].'<br>
            <textarea name="description" rows="8" cols="50" maxlength="500"></textarea><br><br>'.
            $lang['addtask_priority'].'<br>
            <select name="priority">
                <option value="0">'.$lang['addtask_low'].'</option>
                <option value="1">'.$lang['addtask_medium'].'</option>
                <option value="2">'.$lang['addtask_high'].'</option>
            </select><br><br>
            <input type="hidden" value="3" name="visibility"/>
            <input type="hidden" value="add" name="fn"/>
            <input type="submit" class="btn btn-success center-in-div" value="'.$lang['add-task'].'"/>
            </form>';
        }
        else if($_GET['fn']=='edit' && isset($_GET['id']))
        {
            $taskId = intval($_GET['id']);
            require_once('database/Task.class.php');
            $task = new Task();
            $task_info = $task->getTask($taskId);
            $selected = array('','','');
            $selected[$task_info['priority_id']] = ' selected';

            echo '<div id="title">'.$lang['title_edittask'].'</div><br>';
            echo '<div id="edittaskform">
            <form method="post">'.
            $lang['addtask_name'].'<br>
            <input type="text" name="name" size="60" maxlength="60" value="'.$task_info['name'].'"/><br><br>'.
            $lang['addtask_description'].'<br>
            <textarea name="description" rows="8" cols="50" maxlength="500">'.$task_info['description'].'</textarea><br><br>'.
            $lang['addtask_priority'].'<br>
            <select name="priority">
                <option value="0"'.$selected[0].'>'.$lang['addtask_low'].'</option>
                <option value="1"'.$selected[1].'>'.$lang['addtask_medium'].'</option>
                <option value="2"'.$selected[2].'>'.$lang['addtask_high'].'</option>
            </select><br><br>
            <input type="hidden" value="3" name="visibility"/>
            <input type="hidden" value="edit" name="fn"/>
            <input type="hidden" value="'.$taskId.'" name="id"/>
            <input type="submit" class="btn btn-success center-in-div" value="'.$lang['edit-task'].'"/>
            </form>';
        }
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