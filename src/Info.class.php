<?php

class Info
{
    public function getOS() { 

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform  = "unknown";

        $os_array     = array(
                            '/windows nt 10/i'      =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;

        return $os_platform;
    }

    public function getBrowser() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $browser        = "unknown";

        $browser_array = array(
                                '/msie/i'      => 'Internet Explorer',
                                '/firefox/i'   => 'Firefox',
                                '/safari/i'    => 'Safari',
                                '/chrome/i'    => 'Chrome',
                                '/edge/i'      => 'Edge',
                                '/opera/i'     => 'Opera',
                                '/netscape/i'  => 'Netscape',
                                '/maxthon/i'   => 'Maxthon',
                                '/konqueror/i' => 'Konqueror',
                                '/mobile/i'    => 'Handheld Browser'
                        );

        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $browser = $value;

        return $browser;
    }

    public function getTime()
    {
        return gmdate("Y-m-d H:i:s");
    }

    public function convertTimeForTimezone($time, $timezone)
    {
        $dateTime = new DateTime($time, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($timezone));
        return $dateTime->format("Y-m-d H:i:s");
    }

    public function getTimestamp()
    {
        $date = new DateTime();
        return $date->getTimestamp();
    }

    public function getIp()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getCountry()
    {
        if($ipInfo = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$_SERVER['REMOTE_ADDR'])))
            $country = $ipInfo->geoplugin_countryCode;
        else
            $country = "err";
        return $country;
    }

    public function getLanguage()
    {
        $languageArray = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        return $languageArray[0];
    }

    public function getPositions()
    {
        $positions = [
            0 => 'User',
            1 => 'Moderator',
            2 => 'Admin'
        ];

        return $positions;
    }

    public function getOnlineStatuses()
    {
        $statuses = [
            0 => 'Offline',
            1 => 'Online',
            2 => 'Away'
        ];

        return $statuses;
    }

    public function getFriendshipStatuses()
    {
        $statuses = [
            0 => 'PENDING',
            1 => 'ACCEPTED',
            2 => 'REJECTED',
            3 => 'BLOCKED'
        ];

        return $statuses;
    }

    public function getTaskTypes()
    {
        $task_types = [
            0 => 'None'
        ];

        return $task_types;
    }

    public function getTaskStatuses()
    {
        $statuses = [
            0 => 'TO DO',
            1 => 'IN PROGRESS',
            2 => 'PAUSED',
            3 => 'DONE',
            4 => 'REMOVED'
        ];

        return $statuses;
    }

    public function getTaskPriorities()
    {
        $priorities = [
            0 => 'Low',
            1 => 'Medium',
            2 => 'High'
        ];

        return $priorities;
    }

    public function getTaskVisibilities()
    {
        $visibilities = [
            0 => 'Only task creator and task executor can see it',
            1 => "Task creator, task executor and task executor's friends can see it",
            2 => "Task creator, task executor, task creator's friends and task executor's friends can see it",
            3 => 'Everyone can see it'
        ];

        return $visibilities;
    }

    public function getTimezoneDescriptions()
    {
        $descriptions = [
            'Etc/GMT+12' => '(UTC -12:00) International Date Line - West',
            'Pacific/Pago_Pago' => '(UTC -11:00) Pacific/Pago Pago',
            'America/Adak' => '(UTC -10:00) America/Adak',
            'Pacific/Honolulu' => '(UTC -10:00) Pacific/Honolulu',
            'Pacific/Marquesas' => '(UTC -09:30) Pacific/Marquesas',
            'America/Anchorage' => '(UTC -09:00) America/Anchorage',
            'Pacific/Gambier' => '(UTC -09:00) Pacific/Gambier',
            'Europe/London' => '(UTC +00:00) Europe/London',
            'Europe/Paris' => '(UTC +01:00) Europe/Paris',
            'Europe/Warsaw' => '(UTC +01:00) Europe/Warsaw'
        ];

        return $descriptions;
    }

    public function getExpPerMin()
    {
        $exp_per_min_levels = [ 3, 6, 12 ];
        return $exp_per_min_levels;
    }

    public function getMinimumExp()
    {
        $minimum_exp = [ 10, 20, 40 ];
        return $minimum_exp;
    }

    public function isTaskRunning($start_date, $stop_date)
    {
        if($start_date===null)
            return false;
        else if($stop_date===null)
            return true;
        else if($stop_date > $start_date)
            return false;
        else
            return true;
    }

    public function convertSecondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);

        $result = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        return $result;
    }

}

?>