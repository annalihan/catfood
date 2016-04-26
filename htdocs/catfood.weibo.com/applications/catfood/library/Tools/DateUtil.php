<?php
/* @title 时间转换函数 @Author quanjunw @Date 2014-4-14下午2:50:35 @Copyright copyright(2014) weibo.com all rights reserved */
class Tools_DateUtil
{

    const ONE_DAY = 86400;

    const ONE_HOUR = 3600;

    const ONE_MINUTE = 60;

    public static function testEcho()
    {
        return 'test!';
    }

    public static function formateTime ($time, $formate = 'Y-m-d H:i')
    {
        return date($formate, $time);
    }

    public static function timeFormatter($time)
    {
        $time_i = strtotime($time);
        $now = time();
        
        $span = $now - $time_i;
        
        $last_year = date('Y', $time_i);
        $this_year = date('Y', $now);
        
        // 往年
        if ($this_year > $last_year)
        {
            return date('Y-m-d H:i', $time_i);
        }
        
        // 本年度内
        if ($span > self::ONE_DAY)
        {
            $month = date('m', $time_i);
            $date = date('d', $time_i);
            
            return $month . '月' . $date . '日 ' . date('H:i', $time_i);
        }
        
        $last_date = date('d', $time_i);
        $this_date = date('d', $now);
        
        if ($last_date != $this_date)
        {
            $month = date('m', $time_i);
            $date = date('d', $time_i);
            
            return $month . '月' . $date . '日 ' . date('H:i', $time_i);
        }
        
        // 今天
        if ($span > self::ONE_HOUR)
        {
            return '今天 ' . date('H:i', $time_i);
        }
        
        // 1小时内
        if ($span > self::ONE_MINUTE)
        {
            return intval($span / 60) . ' 分钟前';
        }
        
        if ($span < self::ONE_MINUTE)
        {
            return '刚刚';
        }
    }

    public static function interVal($interval = "d", $date1, $date2)
    {
        $timedifference = strtotime($date2) - strtotime($date1);
        $days = bcdiv($timedifference, 86400);
        
        if ($interval == 'd')
        {
            return $days;
        }
        
        $temp1 = $timedifference % (86400);
        $hours = bcdiv($temp1, 3600);
        if ($interval == 'h')
        {
            return $hours;
        }
        
        $temp2 = $temp1 % (3600);
        $minutes = bcdiv($temp2, 60);
        if ($interval == 'n')
        {
            return $minutes;
        }
        
        $seconds = $temp2 % 60;
        if ($interval == 's')
        {
            return $seconds;
        }
    }
}
