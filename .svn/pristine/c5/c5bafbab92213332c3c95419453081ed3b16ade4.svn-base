<?php
class Tools_PageManage
{
    //隐藏无数据pl
    public static function isDisabled ($page, $str, $isShow = 1)
    {
        $str = strtoupper($str);
        $pl = new $str;
        $plData = $pl->prepareData();
        $page->children[strtolower($str)]->disabled = empty($plData[strtolower($str)]) || $isShow == 0 ? true : false;
        return $page;
    }
    
    public static function isShow($page, $pl, $isShow)
    {
        $page->children[strtolower($pl)]->disabled = $isShow == 0 ? true : false;
        return $page;
    }
    
    /*
     * 将整个页面的无数据pl隐藏
     * $page  object paeg对象
     * $slip  array  plname
     */
    public static function isDisabledAllPage($page, $slip = array())
    {
        //页面默认显示的面包屑导航，添加白名单内
        $slip[] = 'pl_common_tip';
        foreach ($page->children as $key => $val)
        {
            if (!in_array($key, $slip))
            {
                $page->children[$key]->disabled = self::disabled($page, $key);
            }
        }
        return $page;
    }
    
    //设置页面以藏
    public static function disabled ($page, $str)
    {
        $str = strtoupper($str);
        $pl = new $str;
        $plData = $pl->prepareData();
        return empty($plData[strtolower($str)]) ? true : false;
    }
}