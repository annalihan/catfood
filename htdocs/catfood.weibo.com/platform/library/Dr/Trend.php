<?php

class Dr_Trend extends Dr_Abstract
{
    /**
     * 首页右侧热点话题
     * @param int $pid 省份编码
     * @param int $cid 市级编码
     * @param int $num 返回记录条数
     */
    public static function pubRecommendTopTopics($uid, $pid = 100, $cid = 1000)
    {
        $uid = Comm_ArgChecker::int($uid, 'min,1', Comm_ArgChecker::NEED);
        
        if (is_null($uid))
        {
            return array();
        }

        $cacheTrend = new Cache_Trend();
        $topics = $cacheTrend->getTopTopics($uid);
        if ($topics)
        {
            return $topics;
        }

        $pid = ($pid == 0 ? 100 : intval($pid));
        $cid = ($cid == 0 ? 1000 : intval($cid));

        try
        {
            $api = Comm_Weibo_Api_Topic::hotSp();
            $api->uid = $uid;
            $api->pid = $pid;
            $api->cid = $cid;
            $rtn = $api->getResult();
        }
        catch (Comm_Exception_Program $e)
        {
            return array();
        }

        foreach ($rtn as $p => $preTopic)
        {
            if (count($preTopic) == 0)
            {
                continue;
            }

            foreach ($preTopic as $k => $topic)
            {
                $topics[$p][$k]['is_local'] = ($topic['pid'] == $pid);
                $topics[$p][$k]['url'] = $topic['url'];
                $topics[$p][$k]['intro'] = $topic['intro'];
                $topics[$p][$k]['keyword'] = $topic['keyword'];
                $topics[$p][$k]['keynum'] = $topic['keynum'];
                $topics[$p][$k]['topic'] = $topic['topic'];
            }
        }

        unset($rtn);

        if (count($topics))
        {
            $cacheTrend->setTopTopics($uid, $topics);
        }

        return $topics;
    }
    
    /**
     * 首页发布器右上角话题
     * @return array
     */
    public static function pubRecommendIssueTopic()
    {
        $cacheTrend = new Cache_Trend();
        $issueTopic = $cacheTrend->getIssueTopic();

        if (false === $issueTopic)
        {
            try
            {
                $api = Comm_Weibo_Api_Pub::recommendIssueTopic();
                $issueTopic = $api->getResult();
                $cacheTrend->createIssueTopic($issueTopic);
            }
            catch (Comm_Weibo_Exception_Api $e)
            {
                return array(
                    'result' => array(),
                    'url' => array()
                );
            }
        }

        $result = isset($issueTopic['result']) ? $issueTopic['result'] : '';
        $url = isset($issueTopic['url']) ? $issueTopic['url'] : '';
        
        return array(
            'result' => $result,
            'url' => $url
        );
    }
    
    /**
     * 1小时热门话题榜
     * @return [type] [description]
     */
    public static function getHourlyTopic()
    {
        $cacheHourlyTopic = new Cache_HourlyTopic();
        $data = $cacheHourlyTopic->getHourlyTopic();
        if ($data)
        {
            return $data;
        }

        try
        {
            $api = Comm_Weibo_Api_Topic::hourly();
            $data = $api->getResult();

            if (isset($data['trends']) && is_array($data['trends']))
            {
                $hourlyTopic = reset($data['trends']); // 取第一个元素是今天的
                
                if (is_array($hourlyTopic) && !empty($hourlyTopic))
                {
                    $hourlyTopic = array_slice($hourlyTopic, 0, 20);
                    $cacheHourlyTopic->createHourlyTopic($hourlyTopic);
                    return $hourlyTopic;
                }
            }

            return array();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }
    
    /**
     * 微博发布框推荐话题
     * @return [type] [description]
     */
    public static function getPublishTopic()
    {
        $cacheTopic = new Cache_Topic();
        return $cacheTopic->getPublishTopic();
    }
    
    /**
     * 推荐位管理-首页tips
     *
     * 产品规则：
     * 1、 tips返回数据为2维数组（ tips数量<=5）
     * 2、第一帧为最多5个元素的数组，用户每次刷新时按顺序轮播
     * 3、对于第一帧的位置存用户缓存
     */
    public static function getHomeTips()
    {
        $cacheTips = new Cache_Tips();
        $data = $cacheTips->getHomeTips(Comm_Context::get('viewer')->id);
        
        if (false === $data)
        {
            try
            {
                $commApi = Comm_Weibo_Api_Suggestions::homeTips();
                $data = $commApi->getResult();
                $cacheTips->createHomeTips(Comm_Context::get('viewer')->id, $data);
            } 
            catch (Comm_Weibo_Exception_Api $e)
            {
                $data = array();
                return $data;
            }
        }
        
        $tips = array();
        if (isset($data['tips']) && is_array($data['tips']))
        {
            $tips['close'] = $data['close'];
            $tips['interval'] = $data['interval'];

            // 种植tips缓存位置
            $tipsLocation = $cache_tips->get_home_tips_location(Comm_Context::get('viewer')->id);
            
            if (false === $tipsLocation)
            {
                $flag = 0; // $flag 表示是否缓存了tips位置，如果为1，表示有缓存，从缓存中取数据计算每帧应该显示第几张图片，反之为0表示每帧显示第一张图片
                $cacheTips->createHomeTipsLocation(Comm_Context::get('viewer')->id, 1);
            }
            else
            {
                $flag = 1;
                $cacheTips->createHomeTipsLocation(Comm_Context::get('viewer')->id, $tipsLocation + 1);
            }
        }

        // 获取向前端推送的数据
        $tipsCount = count($data['tips']);
        for ($i = 0; $i < $tipsCount; $i++)
        {
            if ($flag == 1)
            {
                $index = $tipsLocation % count($data['tips'][$i]);
                $tips['tips'][$i]['tip_img'] = $data['tips'][$i][$index]['tip_img'];
                $tips['tips'][$i]['tip_link'] = $data['tips'][$i][$index]['tip_link'];
            }
            else
            {
                $tips['tips'][$i]['tip_img'] = $data['tips'][$i][0]['tip_img'];
                $tips['tips'][$i]['tip_link'] = $data['tips'][$i][0]['tip_link'];
            }
        }
        
        return $tips;
    }
}
