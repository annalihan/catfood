<?php
class Dr_Favorite extends Dr_Abstract
{
    const PAGESIZE = 20;

    /**
     * 获取当前用户的收藏列表
     * @param int $page
     * @param int $count
     */
    public static function getList($page, $count)
    {
        $requestList = Comm_Weibo_Api_Favorites::favorites();
        $requestList->page = $page;
        $requestList->count = $count;

        try
        {
            $list = $requestList->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        $result = array('list' => array());

        if (!empty($list['favorites']))
        {
            foreach ($list['favorites'] as $fav)
            {
                $tmpFav['status'] = Do_Status::formatStatus($fav['status']);
                $tmpFav['tags'] = $fav['tags'];
                $tmpFav['favorited_time'] = $fav['favorited_time'];
                $result['list'][] = new Do_Favorite($tmpFav, Do_Abstract::MODE_OUTPUT);
            }
        }

        $result['total_number'] = isset($list['total_number']) ? $list['total_number'] : 0;

        $cacheObject = new Cache_Favorite();
        $user = Comm_Context::get("viewer");
        $uid = $user['id'];
        $cacheObject->setTotalNumber($uid, $list['total_number']);
        
        return $result;
    }
    
    /**
     * 获取当前用户收藏标签列表
     * @param int $page
     * @param int $count
     */
    public static function getTagList($page, $count)
    {
        $requestTags = Comm_Weibo_Api_Favorites::tags();
        $requestTags->page = $page;
        $requestTags->count = $count;

        try
        {
            $tags = $requestTags->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        $doTags = array();
        
        if (count($tags['tags']) > 0)
        {
            foreach ($tags['tags'] as $tag)
            {
                $doTags['list'][] = new Do_Favtag($tag);
            }

            usort($doTags['list'], array('Dr_Favorite', 'cmptag_by_count'));
            $doTags['total_number'] = $tags['total_number'];
        }
        else
        {
            $doTags['list'] = array();
            $doTags['total_number'] = 0;
        }

        return $doTags;
    }
    
    public static function searchStatusesFavorites($key, $cuid, $page, $count, $contact = 0, $uid = NULL, $onlyid = 0, $isred = 0, $istag = 0, $onlytotal = 0)
    {
        $result = array();
        $requestSearch = Comm_Weibo_Api_Search::statusesFavorites();
        $requestSearch->key = $key;
        $requestSearch->cuid = $cuid;
        $requestSearch->sid = "t_fav";
        $requestSearch->start = ($page - 1) * $count;
        $requestSearch->num = $count;
        $requestSearch->isred = $isred;
        $requestSearch->istag = $istag;
        $requestSearch->onlytotal = $onlytotal;
        $requestSearch->onlyid = $onlyid;
        $requestSearch->contact = $contact;
        $requestSearch->uid = $uid;

        try
        {
            $list = $requestSearch->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        if ($contact == 0)
        {
            $result['total_number'] = isset($list['m']) ? $list['m'] : 0;
            $result['list'] = isset($list['result']) ? $list['result'] : array();
            
            if (isset ($list['m']) ? $list['m'] : 0 > 0)
            {
                $result['list'] = self::formatSearchFav($list['result'], $key);
            
            }
        }
        elseif (!empty($list[0]))
        {
            $contactUid = $list[0];
            $result['contact'] = Dr_User::getUserInfo($contactUid);
        }

        return $result;
    }
    
    /**
     * 修改当前用户收藏数
     * @param int $total_number
     * @param string $status
     */
    public static function updateTotalNumber($totalNumber, $status)
    {
        $cacheObject = new Cache_Favorite();
        $user = Comm_Context::get("viewer");
        $uid = $user['id'];

        if (empty($totalNumber))
        {
            $totalNumber = $cacheObject->getTotalNumber($uid);
            
            if ($totalNumber === false)
            {
                self::getList(1, self::PAGESIZE);
                return true;
            }
        }

        if ($status === 'add')
        {
            $totalNumber = $totalNumber + 1;
        }
        else
        {
            $totalNumber = $totalNumber - 1;
        }

        $cacheObject->setTotalNumber($uid, $totalNumber);

        return true;
    }
    
    /**
     * 获取当前用户收藏数
     */
    public static function getTotalNumber()
    {
        $cacheObject = new Cache_Favorite();
        $user = Comm_Context::get("viewer");
        $uid = $user['id'];
        $totalNumber = $cacheObject->getTotalNumber($uid);
        
        if ($totalNumber === false)
        {
            $result = self::getList(1, self::PAGESIZE);
            $totalNumber = $result['total_number'];
        }

        return $totalNumber;
    }
    
    public static function formatSearchFav($favList, $key)
    {
        $result = array();
        foreach ($favList as $fav)
        {
            //$fav['time'] = intval($fav['time']/1000);
            $tmp['favorited_time'] = date("y-m-d H:i:s", $fav['time']);

            $favtags = array();
            if ((strlen(trim($fav['tag'])) > 0))
            {
                $tags = explode(',', $fav['tag']);
                foreach ($tags as $tag)
                {
                    if (false !== strpos($tag, ":"))
                    {
                        $tag = explode(":", $tag, 2);
                        $favtags[] = array('tag' => $tag[1], 'id' => $tag[0]);
                    }
                    else
                    {
                        $favtags[] = array('tag' => $tag);
                    }
                }
            }

            $tmp['tags'] = $favtags;
            $tmpFavList[$fav['id']] = $tmp;
            unset($tmp);
            $mids[] = $fav['id'];
        }

        $mids = array_map(create_function('$a', 'return $a["id"];'), $favList);
        $ids = Dr_Status::statusesQueryId($mids);
        $ids = array_values($ids);
        $mblogs = Dr_Status::getMblogs($ids, true);
        foreach ($mblogs as $mblog)
        {
            if (isset($tmpFavList[$mblog['mid']]))
            {
                $mblog['text'] = Tool_Formatter_String::redTag($mblog['text'], $key);
                $tmpFavList[$mblog['mid']]['status'] = $mblog;
                $doFav = new Do_Favorite($tmpFavList[$mblog['mid']], Do_Abstract::MODE_OUTPUT);
                $result[] = $doFav;
            }
        }

        return $result;
    }

    /**
     * 根据标签返回当前用户该标签下的所有收藏 
     * @param int $tagid
     * @param int $page
     * @param int $count
     */
    public static function getListByTag($tagid, $page = 1, $count = 10)
    {
        $request = Comm_Weibo_Api_Favorites::byTags();
        $request->tid = $tagid;
        $request->page = $page;
        $request->count = $count;

        try
        {
            $list = $request->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        $result = array('list' => array());

        if (!empty($list['favorites']))
        {
            foreach ($list['favorites'] as $fav)
            {
                $tmpFav['status'] = Do_Status::formatStatus($fav['status']);
                $tmpFav['tags'] = $fav['tags'];
                $tmpFav['favorited_time'] = $fav['favorited_time'];
                $result['list'][] = new Do_Favorite($tmpFav);
            }
        }

        $result['total_number'] = isset($list['total_number']) ? $list['total_number'] : 0;
        
        return $result;
    }
    
    
    /**
     * 获取推荐收藏标签列表
     */
    public static function getRecommendTags()
    {
        //TODO 接口未完成(三期)
        return array ('UCD产品设计', '明星图片', '不明飞行物', '笑话', '学习');
    }
    
    /**
     * 返回指定收藏的信息
     * @param int64 $favId
     */
    public static function getFavoriteById($favId)
    {
        try
        {
            $request = Comm_Weibo_Api_Favorites::show();
            $request->id = $favId;
            $result = $request->getResult();
            return new Do_Favorite($result);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取收藏最多的微博列表
     * @param int $max 每次获取最大的微博数
     */
    public static function getRecommendFavorites($uid, $max = 3)
    {
        $cacheObject = new Cache_Favorite();
        $statuses = $cacheObject->getRecommend();
        if (false === $statuses)
        {
            try
            {
                $api = Comm_Weibo_Api_Suggestions::favoritesHot();
                $statuses = $api->getResult();
            }
            catch (Exception $e)
            {
                throw new Dr_Exception($e);
            }

            $cacheObject->setRecommend($statuses);
        }

        if (count($statuses) > 0)
        {
            foreach ($statuses as $v)
            {
                $dr[] = new Do_Status($v);
            }

            shuffle($dr);

            for ($i = $max; $i > 0; --$i)
            {
                $temp = array_shift($dr);

                if ($uid == $temp['user']['id'])
                {
                    ++$i;
                    continue;
                } 
                else
                {
                    $favs[$temp['id']] = $temp;
                }
            }

            return $favs;
        }
    }
}
