<?php

class Dr_Tags extends Dr_Abstract
{    
    /**
     * 获取指定用户的标签列表
     * 
     * @param int $userId 用户
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public static function getUserTags($userId, $page = 1, $pageSize = 20)
    {
        $cacheObject = new Cache_Tags();
        $tagsList = $cacheObject->getTags($userId);
        $isSelf = Tool_Misc::checkOwnerIsViewer();

        if (false === $tagsList || $isSelf)
        {
            $commWeiboApiTags = Comm_Weibo_Api_Tags::getTags();
            $commWeiboApiTags->uid = $userId;
            $commWeiboApiTags->page = $page;
            $commWeiboApiTags->count = $pageSize;
            $tagsList = $commWeiboApiTags->getResult();

            if (!$isSelf)
            {
                $cacheObject->setTags($uid, $tagsList);
            }
        }

        $lists = array();
        $weights = array();
        foreach ($tagsList as $tags)
        {
            $tag = array();
            list($tag['tag_id'], $tag['tag']) = each($tags);
            $temp = array_pop($tags);
            $tag['weight'] = $temp['weight'];
            $weights[$tag['tag_id']] = $temp['weight'];
            $lists[$tag['tag_id']] = new Do_Tags($tag, "output");
        }

        if (count($weights) <= 0)
        {
            return $lists;
        }
        
        $weights = array_map(create_function('$a', 'return $a + rand(0, 100) / 100;'), $weights);
        $weights2 = $weights;
        sort($weights);
        $length = count($weights);
        foreach ($weights2 as $i => $weight)
        {
            $key = array_search($weight, $weights);
            $per = $key / $length;
            if ($per <= 1 / 4)
            {
                $class = 'ft12';
            } 
            elseif ($per <= 2 / 4)
            {
                $class = 'ft14';
            }
            elseif ($per <= 3 / 4)
            {
                $class = 'ft16';
            }
            else
            {
                $class = 'ft18';
            }

            $class .= rand() % 2 ? ' ft_b' : '';
            $lists[$i]->class = $class;
        }

        return $lists;
    }
    
    /**
     * 获取当前登录用户感兴趣的推荐标签列表
     * 
     * @param int count  页码:缺省值为5（非必需），随机返回暂不支持此特性
     * @return array
     */
    public static function getSuggestionsTags($count = 10)
    {
        try
        {
            $commWeiboApiTags = Comm_Weibo_Api_Tags::suggestions();
            $commWeiboApiTags->count = $count;
            
            $tagsList = $commWeiboApiTags->getResult();            
            $lists = array();
            foreach ($tagsList as $tags)
            {
                $tag = array();
                $tag['tag_id'] = $tags['id'];
                $tag['tag'] = $tags['value'];
                
                $lists[] = new Do_Tags($tag, "output");
            }
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        return $lists;
    }
}
