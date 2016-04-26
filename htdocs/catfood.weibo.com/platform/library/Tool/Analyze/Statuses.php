<?php
class Tool_Analyze_Statuses
{
    /**
     * 获取微博列表中的微博的链接及作者备注信息
     * @param int $viewerUid 登录用户uid
     * @param array $list feed列表
     * @param boolean $statusRemark 是否显示微博作者的备注
     * @param boolean $isFav 是否为收藏feed
     */
    public static function getStatusesUserRemarks($viewerUid, $list, $statusRemark = false, $isFav = false)
    {
        $userRemarks = array();
        if (count($list) == 0)
        {
            return $userRemarks;
        }

        $ids = $followings = array();
        foreach ($list as $k => $oneList)
        {
            $tempList = $isFav === true ? $oneList['status'] : $oneList;

            if ($statusRemark)
            {
                self::appendStatusesUserIds($tempList, $followings, $viewerUid);
            }

            if (isset($tempList['retweeted_status']))
            {
                self::appendStatusesUserIds($tempList['retweeted_status'], $followings, $viewerUid);
            }

            unset($tempList);
        }

        if (count($followings) > 0)
        {
            $userRemarks = Dr_Relation::friendsRemarkBatch($followings);
        }

        return $userRemarks;
    }
    
    public static function appendStatusesUserIds($statuses, &$uids, $excludeUid)
    {
        if (!isset($statuses['user']))
        {
            return;
        }

        if (!empty($statuses['user']['id']) && $statuses['user']['id'] != $excludeUid && isset($uids[$statuses['user']['id']]) === false)
        {
            $uids[$statuses['user']['id']] = $statuses['user']['id'];
        }
    }

    /**
     * 获取转发按钮的action-data
     * 
     * 此函数目前只生成相关静态参数，不返回微博的评论状态，评论状态在转发弹层后单独请求获取
     */ 
    public static function getRetwitActionData($mblog, $viewerId)
    {
        $domain = Comm_Config::get("domain.weibo");
        $actionData = array();
        $ids = array();

        foreach ($mblog as $v)
        {
            $ids[] = $v['mid'];
            if (!empty($v['retweeted_status']))
            {
                $ids[] = $v['retweeted_status']['mid'];
            }
        }

        $ids = array_unique($ids);
        $mids = Comm_Weibo_MIDConverter::multiFrom10To62($ids);
        foreach ($mblog as $v)
        {
            if (!isset($v['user']))
            {
                continue;
            }

            $actionDataUrl = "allowForward=1";
            $rootDomain = $v['user']['domain'];
            if (!empty($v['retweeted_status']) && !isset($v['retweeted_status']['user']))
            {
                $actionDataUrl = "allowForward=0";
            }

            if (!empty($v['retweeted_status']) && isset($v['retweeted_status']['user']))
            {
                $actionDataUrl .= "&rootmid=" . $v['retweeted_status']['id'] . "&rootname=" . $v['retweeted_status']['user']['name'] . "&rootuid=" . $v['retweeted_status']['user']['id'];
                $actionDataUrl .= "&rooturl=" . $domain ."/". $v['retweeted_status']['user']['id'] . "/" . $mids[$v['retweeted_status']['mid']];
                $rootDomain = $v['retweeted_status']['user']['domain'];
            }

            $actionDataUrl .= "&url=" . $domain ."/". $v['user']['id'] . "/" . $mids[$v['mid']];
            $actionDataUrl .= "&mid=" . $v['id'] . "&name=" . $v['user']['name'] . "&uid=" . $v['user']['id']. "&domain=". $rootDomain;
            if (isset($v['thumbnail_pic']) && $v['thumbnail_pic'])
            {
                $actionDataUrl .= "&pid=" . array_shift(explode('.', basename($v['thumbnail_pic'])));
            }

            if (isset($v['retweeted_status']['thumbnail_pic']) && $v['retweeted_status']['thumbnail_pic'])
            {
                $actionDataUrl .= "&pid=" . array_shift(explode('.', basename($v['retweeted_status']['thumbnail_pic'])));
            }

            $actionData[$v['id']] = $actionDataUrl;
        }
        
        return $actionData;    
    }
}