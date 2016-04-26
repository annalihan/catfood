<?php

/**
 * 实现线上多个版本（js）同时运行的辅助工具类
 */
class Tool_Multirelease
{
    
    private static $_jsMappingConfig = array();
    
    public static function jsVersionReplace($jsFile = '')
    {
        if (empty($jsFile))
        {
            return $jsFile;
        }

        if (preg_match("/(home|taobao)\/js\/(.*?).js/i", $jsFile, $match))
        {
            $type = $match[1];
            $jsKey = $match[2];
            $mappingJs = self::_loadMappingConfig($type);
            
            if (!empty($mappingJs[$jsKey]['js']))
            {
                return $type . '/js/' . $mappingJs[$jsKey]['js'];
            }
        }

        return $jsFile;
    }
    
    /**
     * 加载mapping_js配置文件
     */
    private static function _loadMappingConfig($type)
    {
        if (isset(self::$_jsMappingConfig[$type]))
        {
            return self::$_jsMappingConfig[$type];
        }

        try
        {
            self::$_jsMappingConfig[$type] = Tool_Conf::get('map_js_' . $type);
        }
        catch (Exception $e)
        {
            self::$_jsMappingConfig[$type] = array();
        }
        
        return self::$_jsMappingConfig[$type];
    }
}