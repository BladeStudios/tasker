<?php

class Logger
{
    public static function log($message)
    {
        $now = DateTime::createFromFormat('U.u', microtime(true));
        $current_time = $now->format("Y-m-d H:i:s.u");

        file_put_contents('logs/error_log.txt',$current_time." ".$message."\n");
    }
}

?>