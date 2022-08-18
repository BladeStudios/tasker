<?php

class Calc
{
    public function getSecondsBetweenDates($dateFrom, $dateTo)
    {
        if($dateFrom > $dateTo) // if wrong order
        {
            $dateTemp = $dateFrom;
            $dateFrom = $dateTo;
            $dateTo = $dateTemp;
        }

        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);

        $result = $dateTo->getTimestamp() - $dateFrom->getTimestamp();
        return $result;
    }
}

?>