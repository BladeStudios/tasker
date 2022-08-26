<?php

require_once('src/TaskFunctions.class.php');

$task = new TaskFunctions();

if(isset($_POST))
{
    switch($_POST['fun'])
    {
        case 'start': echo json_encode($task->startTask($_POST['id'])); break;
        case 'pause': echo json_encode($task->pauseTask($_POST['id'])); break;
        case 'finish': echo json_encode($task->finishTask($_POST['id'])); break;
        default: echo false; break;
    }
}

?>