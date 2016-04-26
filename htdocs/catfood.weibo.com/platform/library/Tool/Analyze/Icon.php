<?php
class Tool_Analyze_Icon
{
    const LANG_SC = 'cnname';
    const LANG_TC = 'twname';
    
    private static $_faceList = array();
    
    /**
     * 对内容中的表情文字做处理
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public static function textToIcon($content)
    {
        //加载表情
        self::_loadFace();

        //增加解析emoji表情
        $content = Tool_Emoji::filter($content);
        return preg_replace('/\[([\x{4e00}-\x{9fa5}a-zA-Z0-9]+)\]/use', "self::renderIcon('\\1')", $content);
    }
    
    private static function _loadFace()
    {
        if (self::$_faceList)
        {
            return true;
        }

        try
        {
            $faceList = array();
            $sc = Dr_Face::getFaceListLite('face', self::LANG_SC);

            if (is_array($sc))
            {
                $faceList = array_merge($faceList, $sc);
            }

            $tc = Dr_Face::getFaceListLite('face', self::LANG_TC);
            if (is_array($tc))
            {
                $faceList = array_merge($faceList, $tc);
            }
            
            self::$_faceList = $faceList;
            return true;
        }
        catch (Exception $e)
        {
        }
        
        return false;
    }

    /**
     * 将内容中表情文字替换为表情图标
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function renderIcon($name)
    {
        $nameFormat = "[{$name}]";
        if (mb_strwidth($name, "utf-8") > 20)
        {
            return $nameFormat;
        }
        
        $tmp = self::$_faceList;
        if (empty($tmp[$nameFormat]['url']) === false)
        {
            return "<img src=\"{$tmp[$nameFormat]['url']}\" title=\"[{$name}]\" alt=\"[{$name}]\" type=\"face\" />";
        }
        else
        {
            return $nameFormat;
        }
    }   
}

