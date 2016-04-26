<?php
abstract class Dr_Abstract
{
    /**
     * 筛选出未命中的缓存对象
     * 
     * @param array $items 批量从缓存中获取的对象
     * @param array $keys 批量的缓存key
     * @param array $data 用于保存命中的对象
     * @return array
     */
    public static function filterCachedItems(Array $items, Array $keys, Array &$data)
    {
        $noCacheItem = array();

        foreach ($keys as $id => $key)
        {
            $item = isset($items[$key]) ? $items[$key] : false;
            
            if ($item !== false)
            {
                $data[$id] = $items[$key];
            }
            else
            {
                $noCacheItem[] = $id;
                continue;
            }
        }
        
        return $noCacheItem;
    }
}