<?php
class Tool_Analyze_Source
{
    /**
     * 渲染扩展APP的来源
     * @param Do_Status $status
     */
    public static function formatSource($source, $annotations = null)
    {
        $mblogAppWithExtinfo = Comm_Config::get("source");
        $formatSource = $source;
        $sourceContent = strip_tags($source);
        if (is_array($annotations) && count($annotations))
        {
            foreach ($annotations as $item)
            {
                if (isset($item['source']))
                {
                    $item = $item['source'];
                }

                if (isset($item['appid']) && in_array($item['appid'], $mblogAppWithExtinfo))
                {
                    $title = $sourceContent . '-' . $item['name'];
                    $formatSource = '<a title="' . $title . '" target="_blank" href="' . $item['url']. '" >';
                    $showSource = Tool_Formatter_Content::substrCn($title, 16);
                    $formatSource .=  $showSource . '</a>';
                    break;
                }
                else
                {
                    $formatSource = str_replace('<a', '<a target="_blank"', $formatSource);
                }
            }
        }
        else
        {
            $formatSource = str_replace('<a', '<a target="_blank"', $formatSource);
        }

        //当source = 未通过审核应用时  过滤链接
        if (strpos($formatSource, '未通过审核应用'))
        {
            $formatSource = preg_replace('|<a(.*?)href=".*?"(.*?)>|', '', $formatSource);
        }

        return $formatSource;
    }
    
}