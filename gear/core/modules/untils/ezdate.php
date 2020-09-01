<?php
class EzDate{
    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    const FORMAT_DATE = 'Y-m-d';
    private static $formatList = [
        self::FORMAT_DATETIME,
        self::FORMAT_DATE
    ];

    public static function analyseDateTimeByDay($s, $e){
        return self::analyseDateTime($s, $e, '+1 day');
    }
    public static function analyseDateTimeByYear($s, $e){
        return self::analyseDateTime($s, $e, '+1 year');
    }
    public static function analyseDateTimeByMonth($s, $e){
        return self::analyseDateTime($s, $e, '+1 month');
    }
    public static function analyseDateTimeByHour($s, $e){
        return self::analyseDateTime($s, $e, '+1 hour');
    }

    private static function analyseDateTime($s, $e, $t){
        $date1 = self::formatDateTime($s);
        $e = self::formatDateTime($e);
        do{
            $date2 = date(self::FORMAT_DATETIME, strtotime($t, strtotime($date1)));
            $res[] = $date1;
            $date1 = $date2;
        }while($date1 <= $e);
        return $res;
    }

    public static function formatDateTime($dateTime, $format = self::FORMAT_DATETIME){
        if(!in_array($format,self::$formatList)){
            DBC::throwEx("[EzDate] UnSupport Format Type");
        }
        return date($format, self::getTime($dateTime));
    }

    public static function getTime($dateTime = ""){
        if(empty($dateTime)){
            return time();
        }
        $res = strtotime($dateTime);
        if(!$res){
           DBC::throwEx("[EzDate] UnKnow DateTime $dateTime");
        }
        return $res;
    }
}