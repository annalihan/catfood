<?php

define("TIME_FORMAT_SECONDTE", _('%s秒前'));
define("TIME_FORMAT_MINITE", _('%s分钟前'));
define("TIME_FORMAT_HOUR", _('%s小时前'));
define("TIME_FORMAT_DAY", _('%s天前'));
define("TIME_FORMAT_WEEK", _('1周前'));
define("TIME_FORMAT_TODAY", _('今天 %s'));
define("TIME_FORMAT_HISTORY", _('%s-%s-%s'));

$currentLang = Comm_I18n::getCurrentLang();
if ($currentLang == 'en' || $currentLang == 'en-us')
{
    define("TIME_FORMAT_HISTORY_VISITOR", _('%s-%s'));
}
else
{
    define("TIME_FORMAT_HISTORY_VISITOR", _('%s月%s日'));
}

class Tool_Formatter_Time
{
    public static function timeFormat($time)
    {
        $now = time();
        if (!is_numeric($time))
        {
            $time = strtotime($time);
        }

        if (($dur = $now - $time) < 3600)
        {
            if ($dur < 50)
            {
                $second = ceil($dur / 10) * 10;

                if ($second <= 0)
                {
                    $second = 10;
                }

                $time = sprintf(TIME_FORMAT_SECONDTE, $second);
            }
            else
            {
                $minutes = ceil($dur / 60);
                
                if ($minutes <= 0)
                {
                    $minutes = 1;
                }

                $time = sprintf(TIME_FORMAT_MINITE, $minutes);
            }
        }
        elseif (date("Ymd", $now) == date("Ymd", $time))
        {
            $time = sprintf(TIME_FORMAT_TODAY, date("H:i", $time));
        }
        else
        {
            if (date("Y") == date("Y", $time))
            {
                $time = sprintf(TIME_FORMAT_HISTORY_VISITOR, date("n", $time), date("j", $time)) . " " . date("H:i", $time);
            }
            else
            {
                $time = sprintf(TIME_FORMAT_HISTORY, date("Y", $time), date("n", $time), date("j", $time)) . " " . date("H:i", $time);
            }
        }

        return $time;
    }

    public static function timeFormatForMyapp($time)
    {
        $now = time();
        if (!is_numeric($time))
        {
            $time = strtotime($time);
        }

        $dur = $now - $time;
        if ($dur < 3600)
        {
            if ($dur < 50)
            {
                $second = ceil($dur / 10) * 10;
                if ($second <= 0)
                {
                    $second = 10;
                }

                $time = sprintf(TIME_FORMAT_SECONDTE, $second);
            }
            else
            {
                $minutes = ceil($dur / 60);
                if ($minutes <= 0)
                {
                    $minutes = 1;
                }

                $time = sprintf(TIME_FORMAT_MINITE, $minutes);
            }
        }
        elseif (date("Ymd", $now) == date("Ymd", $time))
        {
            $hour = ceil($dur / 3600);
            if ($hour <= 0)
            {
                $hour = 1;
            }

            $time = sprintf(TIME_FORMAT_HOUR, $hour);
        }
        else
        {
            $day = ceil($dur / 86400);
            if ($day < 7)
            {
                if ($day <= 0)
                {
                    $day = 1;
                }

                $time = sprintf(TIME_FORMAT_DAY, $day);
            }
            else
            {
                $time = TIME_FORMAT_WEEK;
            }
        }
        
        return $time;
    }
}