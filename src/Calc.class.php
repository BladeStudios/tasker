<?php

class Calc
{
    public function getSecondsBetweenDates($dateFrom, $dateTo)
    {
        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);

        $result = $dateTo->getTimestamp() - $dateFrom->getTimestamp();
        if($result < 0) $result = -($result);
        return $result;
    }
}

?>