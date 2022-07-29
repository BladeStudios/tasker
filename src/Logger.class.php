<?php

class Logger
{
    public static function log($message)
    {
        $current_time = date("Y-m-d H:i:s");

        file_put_contents('logs/error_log.txt',$current_time." ".$message."\n",FILE_APPEND);
    }
}

?>