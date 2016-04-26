<?php
class Tool_Formatter_OnlineStatus
{
    private static $_statusData = null;

    /**
     * 根据状态值获取在线状态对应的css及显示文案
     * @param int $status
     */
    public static function getOnlineStatusData($status)
    {
        if (self::$_statusData == null)
        {
            self::$_statusData = array(
                array(
                    'css' => 'IM_offline',
                    'value' => Comm_I18n::get('离线')
                ),
                array(
                    'css' => 'IM_online',
                    'value' => Comm_I18n::get('在线')
                ),
                array(
                    'css' => 'IM_away',
                    'value' => Comm_I18n::get('离开')
                ),
                array(
                    'css' => 'IM_busy',
                    'value' => Comm_I18n::get('忙碌')
                ),
            );
        }

        return isset(self::$_statusData[$status]) ? self::$_statusData[$status] : self::$_statusData[0];
    }
}