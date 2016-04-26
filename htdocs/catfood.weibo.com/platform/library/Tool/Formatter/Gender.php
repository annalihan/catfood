<?php
class Tool_Formatter_Gender
{
    public static function formatFriendly($gender)
    {
        return $gender ? (strtolower($gender) == 'f' ? Comm_I18n::get('男') : Comm_I18n::get('女')) : '';
    }
    
    public static function formatToEnName($gender)
    {
        return strtolower($gender) == 'f' ? 'female' : 'male';
    }
    
    public static function formatToThird($gender)
    {
        return strtolower($gender) == 'f' ? Comm_I18n::get('她') : Comm_I18n::get('他');
    }
}