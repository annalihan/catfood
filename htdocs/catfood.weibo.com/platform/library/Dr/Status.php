<?php
/**
 * 微博Dr
 * @todo 待处理转换格式
 * @not_check 先不检查内容
 */
class Dr_Status extends Dr_Abstract
{
    const MC_LIVETIME = 300;
    const MC_LIVETIME_HALF_MINUTE = 30;
    const MC_LIVETIME_HOUR = 3600;
    const MC_LIVETIME_RZ = 86400;
    const MC_MBLOG_INFO_LIVETIME = 300;
    const MC_GEO_INFO_LIVETIME = 1296000; //缓存15天
    const CACHE_POOL = 'MAIN';
    const MC_KEY_PREFIX = 'status';
    private static $_mcKeyFriendsTimeline = '%s_1_%s';
    private static $_mcKeyFriendsTimeline30 = '%s_1_30_%s';
    private static $_mcKeyFriendsTimeline3600 = '%s_3_3600_%s';
    private static $_mcKeyUserTimeline = '%s_2_%s';
    private static $_mcKeyUserTimelineHour = '%s_6_%s';
    const MC_KEY_GEO_INFO = '%s_3_1_%s';
    const MC_KEY_MBLOG_INFO = '%s_4_%s';
    const MC_KEY_MENTION_ME = '%s_5_%s';
    //容灾缓存
    const MC_KEY_RZ_FRIENDS_TIMELINE = '%s_7_%s';
    const MC_KEY_RZ_USER_TIMELINE = '%s_8_%s';
    //小缓存，存储五分钟内自己发的微博
    const MC_KEY_SELF_SEND_MBLOG = '%s_9_%s';
  
    const RUMOR_MBLOG_MLEVEL = 1; //谣言微博标识
    const REPORT_MBLOG_MLEVEL = 128; //举报微博标识
    const STATUS_TO_SELF = 1; //私密定向微博
    const STATUS_CLOSE_FRIEND = 4; //密友定向微博
    const STATUS_CLOSE_GROUP = 3; //密友定向微博

    //微博状态码定义
    const STATUS_SELF = 3; //已设私密 *除自己外的列表不显示*
    const STATUS_AUTO_SELF = 11;      //自动设私 *同3*

    const FEED_CACHE_LIMIT = 180; // feed缓存
    //过滤海外状态。 临时处理 ，应添加其他微博状态的过滤。haoran2.
    private static $abroadStates = array(16, 17, 18, 19);

    /**
     * 根据微博状态状态判断微博是否应该显示
     *
     * @param array $statusInfo
     * @return BOOL
     */
    public static function filterState($statusInfo)
    {
        $viewer = Comm_Context::get("viewer", FALSE);
        $viewerId = $viewer ? $viewer->id : null;
        return Tool_StatusFilter::checkSingleVisiable($statusInfo, $viewerId);
    }

    public static function mcKey($format)
    {
        $args = func_get_args();
        $args[0] = Comm_Config::get('cache_key.' . self::MC_KEY_PREFIX);
        return vsprintf($format, $args);
    }

    /**
     * 获取当前登录用户的feed列表
     *
     * @param int $count
     * @return Dr_Status
     */
    public static function friendsTimeline($count = 50, $page = 1, $feature = 0, $sinceId = NULL, $maxId = NULL, $baseApp = 0, $trimUser = 0) {
        //是否应用缓存（临时解决后台操作对前台数据的同步问题）2011-07-06 add by zhangbo
        $useCache = Comm_Config::get('control.use_cache');
        if ($useCache) {
            $cache = Comm_Cache::pool(self::CACHE_POOL);
            //缓存取数据
            if ($page == 1 && $feature == 0 && !$sinceId) {
                $key = self::mcKey(self::$_mcKeyFriendsTimeline30, Comm_Context::get('viewer')->id);
                $result = $cache->get($key, self::MC_LIVETIME_HALF_MINUTE);
                if ($result) {
                    return $result;
                } else {
                    try {
                        $unread = Dr_Remind::unread(Comm_Context::get('viewer'), 'feed');
                        if ($unread < 1) {
                            $key = self::mcKey(self::$_mcKeyFriendsTimeline, Comm_Context::get('viewer')->id);
                            $result = $cache->get($key, self::MC_LIVETIME);
                        }
                    } catch (Exception $e) {}
                }
                if ($result) {
                    return $result;
                }
            }
        }
        //接口取数据
        //ini_set('display_errors',1);
        try {
            $commApi = Comm_Weibo_Api_Statuses::friendsTimeline();
            $commApi->count = $count;
            $commApi->page = $page;
            $commApi->feature = $feature;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->base_app = $baseApp;
            $commApi->getHttpRequest()->connectTimeout = 2000;
            $commApi->getHttpRequest()->timeout = 2000;
            $commApi->trim_user = $trimUser;
            $rtnFeedList = $commApi->getResult();
        } catch (Comm_Weibo_Exception_Api $e) {
            //return 1hour cache
            if ($page == 1 && $feature == 0 && !$sinceId) {
                $key = self::mcKey(self::$_mcKeyFriendsTimeline3600, Comm_Context::get('viewer')->id);
                $result = $cache->get($key, self::MC_LIVETIME_HOUR);
                if (false !== $result) {
                    return $result;
                }
            }
            throw new Dr_Exception($e);
        }
        if ($rtnFeedList) {
            $feedTotalNum = $rtnFeedList['total_number'];
            $feedNextCursor = $rtnFeedList['next_cursor'];
            $feedPreviousCursor = $rtnFeedList['previous_cursor'];
            if (isset($rtnFeedList['statuses'])) {
                $rtnFeedList = $rtnFeedList['statuses'];
            }
        }
        if (count($rtnFeedList) == 0) {
            $result = array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        } else {
            $result = array('list' => $rtnFeedList, 'total' => $feedTotalNum, 'next_cursor' => $feedNextCursor);
        }
        if ($useCache) {
            //写入缓存
            if ($page == 1 && $feature == 0 && !$sinceId) {
                $key = self::mcKey(self::$_mcKeyFriendsTimeline3600, Comm_Context::get('viewer')->id);
                $cache->set($key, $result, self::MC_LIVETIME_HOUR);
                $key = self::mcKey(self::$_mcKeyFriendsTimeline30, Comm_Context::get('viewer')->id);
                $cache->set($key, $result, self::MC_LIVETIME_HALF_MINUTE);
                $key = self::mcKey(self::$_mcKeyFriendsTimeline, Comm_Context::get('viewer')->id);
                $cache->set($key, $result, self::MC_LIVETIME);
            }
            if ($page == 1 && $feature == 0 && $sinceId) {
                $key = self::mcKey(self::$_mcKeyFriendsTimeline30, Comm_Context::get('viewer')->id);
                $cache->del($key, $result, self::MC_LIVETIME_HALF_MINUTE);
                $key = self::mcKey(self::$_mcKeyFriendsTimeline, Comm_Context::get('viewer')->id);
                $cache->del($key, $result, self::MC_LIVETIME);
            }
        }
        return $result;
    }


    /**
     * 获取指定用户的Feed列表，按mid倒序排序
     *
     * @param integer $count    需要获取的feed数目
     * @param integer $page        页码
     * @param integer $feature    筛选值
     * @param string $sinceId    聚合列表最小值边界，即小于此值的feed都将舍弃
     * @param string $maxId    计算的起始mid
     * @param integer $baseApp
     * @param integer $trimUser
     * @return array
     */
    public function getFeedList($count=50, $page=1, $feature=0, $sinceId=NULL, $maxId=NULL, $baseApp=0, $trimUser=0, $lastId=0) {
        $result = array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        $useCache = Comm_Config::get('control.use_feedindex_cache') && empty($feature) ? true : false;
        $cuid = Comm_Context::get('viewer')->id;
        $setCache = true;
        $isLabel = false; //是否点小黄签
        $addCache = 0; //添加FEED缓存位置

        // 我的首页，点小黄签异步加载
        if ($sinceId && $page == 1 && $count == 45) {
            $feedList = self::friendsTimelineIds($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
            $isLabel = true;
            $addCache = 1;

        // 我的首页，刷新
        } elseif ($page == 1 && $count == 15) {
            $addCache = 1;
            if (empty($feature) && $useCache){
                $getNum = 50;
            }else{
                $getNum = $count;
            }
            if ($useCache){
                $cacheObj = new Cache_Feedindex();
                $feedCache = $cacheObj->getFeedList($cuid);
            }
            if (isset($feedCache['list']) && !empty($feedCache['list'])) {
                $timeOffset = time () - $feedCache['last_pull_time'];
                if ($timeOffset < 0 || $timeOffset > 3) {
                    $sinceId = reset($feedCache['list']);
                    try{
                        $feedList = self::friendsTimelineIds($getNum, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
                    }catch (Comm_Exception_Program $e){
                        $result['list'] = array_slice($feedCache['list'], 0, $count);
                        $result['total'] = $feedCache['total'];
                        $result['next_cursor'] = $feedCache['list'][$count];
                        return $result;
                    }
                }
                // 如果没有新feed，则直接返回结果
                if (empty($feedList['list'])) {
                    $result['list'] = array_slice($feedCache['list'], 0, $count);
                    $result['total'] = $feedCache['total'];
                    $result['next_cursor'] = $feedCache['list'][$count];
                    return $result;
                }
            }else{
                $feedList = self::friendsTimelineIds($getNum, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
            }

        // 我的首页，分屏加载（第二、三屏）
        } elseif ($page > 1 && $page < 4) {
            /*$addCache = 2;
            if ($useCache){
                $cacheObj = new Cache_Feedindex();
                $feedCache = $cacheObj->getFeedList($cuid);
            }
            if (isset($feedCache['list']) && !empty($feedCache['list'])) {
                $offset = array_search($lastId,$feedCache['list']);
                if ($offset === false){
                    $feedList = self::friendsTimelineIds($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
                }else{
                    $result['list'] = array_slice($feedCache['list'], $offset+1, $count);
                    $result['total'] = $feedCache['total'];
                    $nextOffset = $page*$count;
                    $result['next_cursor'] = $feedCache['list'][$nextOffset];
                    return $result;
                }
            } else {
                $feedList = self::friendsTimelineIds($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
            }*/
            //第二、三暂时不用缓存
            $feedList = self::friendsTimelineIds($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser, $lastId);
            $offset = array_search($lastId,$feedList['list']);
            if ($offset !== false){
                $feedList['list'] = array_slice($feedList['list'], $offset+1);
            }
            $setCache = false;

            // 我的首页，第二页之后
        } else {
            $feedList = self::friendsTimelineIds($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser);
            $setCache = false;
        }

        // 更新feed缓存
        if (!empty($feedList['list'])) {
            if (empty($feature) && $setCache && !isset($feedList['no_set_cache'])) {
                if (!isset($cacheObj)) {
                    $cacheObj = new Cache_Feedindex();
                    $feedCache = $cacheObj->getFeedList($cuid);
                }
                if ($isLabel && empty($feedCache)){ //缓存为空的话点小黄标签返回的数据不能满足一页的数量，因此不存缓存
                    $setCache = false;
                }
                if (isset($feedCache['list'])){
                    if ($addCache == 1){
                        $feedCache['list'] = Tool_Ds::arryMerage(array($feedList['list'],$feedCache['list']));
                    }else if ($addCache == 2){
                        $feedCache['list'] = Tool_Ds::arryMerage(array($feedCache['list'],$feedList['list']));
                    }
                }else{
                    $feedCache['list'] = $feedList['list'];
                }
                if (isset($feedCache['total'])){
                    if ($feedList['total']>$feedCache['total']){
                        $feedCache['total'] = $feedList['total'];
                    }
                }else{
                    $feedCache['total'] = $feedList['total'];
                }
                $feedCache['last_pull_time'] = time ();
                if (count($feedCache['list']) > self::FEED_CACHE_LIMIT){
                    $feedCache['list'] = array_slice($feedCache['list'], 0, self::FEED_CACHE_LIMIT);
                }
                if ($setCache){
                    $cacheObj->setFeedList($cuid, $feedCache);
                }
                $sliceOffset = ($page-1)*$count;
                if (!empty($sliceOffset)){
                    $sliceOffset += 1;
                }
                $result['list'] = ($isLabel) ? $feedList['list'] : array_slice($feedCache['list'],$sliceOffset,$count);
                $result['total'] = $feedCache['total'];
                if ($isLabel){
                    $result['next_cursor'] = $feedList['next_cursor'];
                }else{
                    $nextOffset = $page*$count;
                    $result['next_cursor'] = isset($feedCache['list'][$nextOffset]) ? $feedCache['list'][$nextOffset] : end($feedCache['list']);
                }
            }else{
                $result = $feedList;
            }
        }

        return $result;
    }

    /**
     * 获取当前登录用户的feed列表ids
     *
     * @param int $count
     * @return Dr_Status
     */
    public static function friendsTimelineIds($count = 50, $page = 1, $feature = 0, $sinceId = NULL, $maxId = NULL, $baseApp = 0, $trimUser = 0, $lastId = 0) {
        $noSetCache = false; //是否中FEEDINDEX的MC
        try {
            $commApi = Comm_Weibo_Api_Statuses::friendsTimelineIds();
            $commApi->count = $count;
            $commApi->page = $page;
            $commApi->feature = $feature;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->base_app = $baseApp;
            $commApi->getHttpRequest()->connectTimeout = 3000;
            $commApi->getHttpRequest()->timeout = 3000;
            $commApi->trim_user = $trimUser;
            $rtnFeedList = $commApi->getResult();
        } catch (Comm_Weibo_Exception_Api $e) {
            throw new Dr_Exception ( $e );
        }
        if ($rtnFeedList) {
            $feedTotalNum = $rtnFeedList['total_number'];
            $feedNextCursor = $rtnFeedList['next_cursor'];
            $feedPreviousCursor = $rtnFeedList['previous_cursor'];
            if (isset($rtnFeedList['statuses'])) {
                $rtnFeedList = $rtnFeedList['statuses'];
            }
        }
        if (count($rtnFeedList) == 0) {
            $result = array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        } else {
            $result = array('list' => $rtnFeedList, 'total' => $feedTotalNum, 'next_cursor' => $feedNextCursor);
            if ($noSetCache){
                $result['no_set_cache'] = 1;
            }
        }
        return $result;
    }

    /**
     * 获取当前登录用户的feed列表
     */
    public static function getFriendsTimeline($count = 50, $page = 1, $feature = 0, $sinceId = NULL, $maxId = NULL, $baseApp = 0, $trimUser = 0, $lastId = 0) {
        $cache = Comm_Cache::pool(self::CACHE_POOL);
        $key = self::mcKey(self::MC_KEY_RZ_FRIENDS_TIMELINE, Comm_Context::get('viewer')->id);
        try {
            //$friendsTimeline = self::getFeedList($count, $page, $feature, $sinceId, $maxId, $baseApp, $trimUser, $lastId);
            $dataFeed = new Data_Feed();
            $friendsTimeline = $dataFeed->getFeedMids($count, $page, $feature, $sinceId, $maxId, $lastId);
            $mids = $friendsTimeline['list'];
            $friendsTimeline['list'] = self::getMblogs($mids);
        } catch (Comm_Exception_Program $e) {
            if ($page == 1 && $feature == 0 && $count == 15 && !$sinceId && !$maxId) {

                $friendsTimeline = $cache->get($key, self::MC_LIVETIME_RZ);
                if ($friendsTimeline === false) {
                    throw $e;
                }
                $friendsTimeline['list'] = self::mergeSelfMblog($friendsTimeline['list'], 15);
                //记录使用容灾 缓存的次数
                $msg = __METHOD__ . $e->getMessage();
                // Tool_Log_Commlog::writeLog('RZ', $msg);
                return $friendsTimeline;
            }
            throw $e;
        }
        if ($page == 1 && $feature == 0 && $count == 15 && !$sinceId && !$maxId) {
            //增加种缓存概率
            $friendsTimeline['list'] = self::mergeSelfMblog($friendsTimeline['list'], 15);
            if (1 == rand(1, 3)) {
                $cache->set($key, $friendsTimeline, self::MC_LIVETIME_RZ);
            }
        }
        $friendsTimeline['list'] = self::filterVisible($friendsTimeline['list']);
        return $friendsTimeline;
    }
    
    public static function filterVisible($timelineList=array()){
        if ( isset($timelineList) && is_array($timelineList) ) {
            $viewer = Comm_Context::get('viewer',false);
            $checkUids = array();
            foreach ($timelineList as $k =>$v ){
                if (empty($viewer) || ($viewer->id!=$v['uid'] && isset($v['visible']) && isset($v['visible']['type']))){
                    if ($v['visible']['type']==Dr_Status::STATUS_TO_SELF) {
                        unset($timelineList[$k]);
                    } elseif ($v['visible']['type']==Dr_Status::STATUS_CLOSE_FRIEND) {
                        $checkUids[] = $v['uid'];
                    }
                }
            }
            //过滤密友状态的数据
            if (is_array($checkUids) && count($checkUids)>0){
                $interfaceError = false;
                try{
                    $checkUids = array_unique($checkUids);
                    $checkRelation = Dr_Relation::friendshipsCloseFriendsExists($checkUids);
                } catch (Comm_Weibo_Exception_Api $e){
                    $interfaceError = true;
                } catch (Comm_Exception_Program $e) {
                    $interfaceError = true;
                }
                foreach ($timelineList as $k =>$v ){
                    if ($v['visible']['type']==Dr_Status::STATUS_CLOSE_FRIEND && (empty($viewer) || $viewer->id!=$v['uid'])){
                        if ($interfaceError === false && isset($checkRelation[$v['uid']]) && $checkRelation[$v['uid']] == 1){
                            continue;
                        } else {
                            unset($timelineList[$k]);
                        }
                    }
                }
            }
        }
        return $timelineList;
    }
    /**
     * 获取当前登录用户的feed列表
     */
    public static function mergeSelfMblog($mblogList = array(), $count) {
        $cache = Comm_Cache::pool(self::CACHE_POOL);
        $key = self::mcKey(self::MC_KEY_SELF_SEND_MBLOG, Comm_Context::get('viewer')->id);
        $selfMblog = $cache->get($key, 300);
        if ($selfMblog) {
            foreach ($selfMblog as $key => $v){
                if (!isset($mblogList[$key])){
                    $mblogList[$key] = $v;
                }
            }
            krsort($mblogList);
            $mblogList = array_slice($mblogList, 0, $count);
        }
        return $mblogList;
    }
    /**
     * 获取用户相互关注feed列表
     *
     * @param int $count
     * @return Dr_Status
     */
    public static function bilateralTimeline($count = 50, $page = 1, $feature = 0, $sinceId = 0, $maxId = 0, $baseApp = 0) {
        //接口取数据
        try {
            $commApi = Comm_Weibo_Api_Statuses::bilateralTimeline();
            $commApi->count = $count;
            $commApi->page = $page;
            $commApi->feature = $feature;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->base_app = $baseApp;
            $rtnFeedList = $commApi->getResult();
        } catch (Comm_Weibo_Exception_Api $e) {
            throw new Dr_Exception($e);
        }
        if ($rtnFeedList) {
            $feedTotalNum = $rtnFeedList['total_number'];
            $feedNextCursor = $rtnFeedList['next_cursor'];
            $feedPreviousCursor = $rtnFeedList['previous_cursor'];
            if (isset($rtnFeedList['statuses'])) {
                $rtnFeedList = $rtnFeedList['statuses'];
            }
        }
        if (count($rtnFeedList) == 0) {
            $result = array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        } else {
            $result = array('list' => $rtnFeedList, 'total' => $feedTotalNum, 'next_cursor' => $feedNextCursor);
        }
        return $result;
    }

    /**
     *feed列表按兴趣度排序
     *
     * @deprecated
     * @param int $count
     * @return Dr_Status
     */
    public static function statusesReorder($count = 50, $page = 1, $section = 0, $sinceId = 0, $maxId = 0) {
        //接口取数据
        try {
            $commApi = Comm_Weibo_Api_Suggestions_Statuses::statusesReorder();
            $commApi->count = $count;
            $commApi->page = $page;
            $commApi->section = $section;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $rtnFeedList = $commApi->getResult();
        } catch (Comm_Weibo_Exception_Api $e) {
            throw new Dr_Exception($e);
        }
        if ($rtnFeedList) {
            $feedTotalNum = $rtnFeedList['total_number'];
            $feedNextCursor = isset($rtnFeedList['next_cursor']) ? $rtnFeedList['next_cursor'] : 0;
            $feedPreviousCursor = isset($rtnFeedList['previous_cursor']) ? $rtnFeedList['previous_cursor'] : 0;
            if (isset($rtnFeedList['statuses'])) {
                $rtnFeedList = $rtnFeedList['statuses'];
            }
        }
        if (count($rtnFeedList) == 0) {
            $result = array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        } else {
            $result = array('list' => $rtnFeedList, 'total' => $feedTotalNum, 'next_cursor' => $feedNextCursor);
        }
        return $result;
    }

    /**
     * feed列表按兴趣度排序，仅返回ids列表
     *
     * @param int $count
     * @param int $page
     * @param int $section
     */
    public static function statusesReorderIds($count = 50, $page = 1, $section = 0) {
        $commApi = Comm_Weibo_Api_Suggestions_Statuses::statusesReorderIds();
        $commApi->count = $count;
        $commApi->page = $page;
        $commApi->section = $section;
        return $commApi->getResult();
    }

    public static function getStatusesReorder($count = 50, $page = 1, $section = 0) {
        $cacheStatus = new Cache_Status();
        $uid = Comm_Context::get('viewer')->id;
        $reorderIds = $cacheStatus->getTotalReorderIds($uid, $section);
        if (FALSE === $reorderIds) {
            try {
                $reorderIds = self::statusesReorderIds(500, 1, $section);
            } catch (Comm_Exception_Program $e) {
                //容易超时，重试一次
                $reorderIds = self::statusesReorderIds(500, 1, $section);
            }

            if (FALSE !== $reorderIds) {
                $cacheStatus->createTotalReorderIds($uid, $section, $reorderIds);
            }
        }

        $totalMids = isset($reorderIds['statuses']) ? $reorderIds['statuses'] : array();
        $feedList= array();
        $feedList['total'] = $reorderIds['total_number'];
        $feedList['next_cursor'] = 0;
        if (empty($totalMids)) {
            $feedList['list'] = array();
            return $feedList;
        }

        $offset = ($page - 1) * $count;
        $mids = array_slice($totalMids, $offset, $count);
        $feedList['list'] = self::getMblogs($mids);
        return $feedList;
    }

    /**
     *
     * 批量渲染微博数据
     * @param array $list
     */
    public static function mappingStatus($list, $keyWord = "") {
//TODO
        //cache_keys是微博id向微博的缓存id的映射
        $cacheKeys = array();
        foreach ($list as $key => $data) {
            if (!empty($data['user']['id']) && !empty($data['id'])) {
                $cacheKeys[$data['id']] = Comm_Cache::key('status', $data['user']['id'], $data['id']);
            }
        }
        if ($cacheKeys) {
            $cachedStatuses = Comm_Cache::pool(Do_Status::$cachePool)->mget(array_values($cacheKeys), Do_Status::$cacheExpires);
        } else {
            $cachedStatuses = array();
        }

        $feedList = $list;
        foreach ($feedList as $key => $data) {
            //命中缓存的，不搜集短链信息
            if (!empty($cachedStatuses[$cacheKeys[$data['id']]])) {
                continue;
            }
            //搜集微博短链信息
            if (isset($data['text']) && !empty($data['text'])) {
                Tool_Analyze_Link::prepareParseLink($data['text']);
            }

            //搜集转发原微博短链信息
            if (isset($data['retweeted_status']['text']) && !empty($data['retweeted_status']['text'])) {
                Tool_Analyze_Link::prepareParseLink($data['retweeted_status']['text']);
            }
        }

        //批量获取上面程序搜集的短链信息
        Tool_Analyze_Link::genShortUrlInfo();

        foreach ($feedList as $key => $value) {
            //过滤删除的微博
            if (!$value['id'] || !isset($value['user'])) {
                unset($feedList[$key]);
                continue;
            }
            $feedList[$key] = Do_Status::formatStatus($value, $keyWord);
        }
        return $feedList;
    }

    /**
     *
     * 根据微博id获取该微博的转发列表
     * @param int $mid 微博ID。
     * @param int $count 返回结果条数数量，默认50默认为50
     * @param int $page 页码.返回的结果的页码。默认为1
     * @param int $filterByAuthor 转发列表筛选，0.全部 1. 关注人 2. 陌生人。默认0
     * @param int $sinceId 若指定此参数，则只返回ID比sinceId大的微博消息（即比sinceId发表时间晚的微博消息）。默认为0
     * @param int $maxId 若指定此参数，则返回ID小于或等于maxId的微博消息。默认为0
     */
    public static function repostTimeline($mid, $count = 50, $page = 1, $filterByAuthor = 0, $sinceId = 0, $maxId = 0) {
        try {
            $apiStatuses = Comm_Weibo_Api_Statuses::repostTimeline();
            $apiStatuses->id = $mid;
            $apiStatuses->page = $page;
            $apiStatuses->count = $count;
            $apiStatuses->filter_by_author = $filterByAuthor;
            //$apiStatuses->since_id = $sinceId;
            //$apiStatuses->max_id = $maxId;
            $repostRst = $apiStatuses->getResult();
        } catch (Comm_Weibo_Exception_Api $e) {
            return array('list' => array(), 'total' => 0, 'next_cursor' => 0);

     //throw new Dr_Exception($e);
        }
        if ($repostRst) {
            $repostTotalNum = $repostRst['total_number'];
            $repostNextCursor = $repostRst['next_cursor'];
            $repostPreviousCursor = $repostRst['previous_cursor'];
            if (isset($repostRst['reposts'])) {
                $repostFeedList = array();
                foreach ($repostRst['reposts'] as $report) {
                    //过滤已经删除的微博
                    if (!isset($report['user'])) {
                        continue;
                    }
                    $repostFeedList[] = $report;
                }
                if ($repostFeedList) {
                    $repostFeedList = Dr_Status::mappingStatus($repostFeedList);
                }
            }
        }
        if (count($repostFeedList) == 0) {
            return array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        }
        return array('list' => $repostFeedList, 'total' => $repostTotalNum, 'next_cursor' => $repostNextCursor, 'previous_review' => $repostPreviousCursor);
    }

    /**
     *
     * 通过id获取mid
     * @param array $id
     * @param int $type id的类型：1为微博，2为评论；3为私信
     */
    public static function statusesQueryMid($id, $type = 1) {
        if (!$id) {
            return array();
        }
        $commApi = Comm_Weibo_Api_Statuses::querymid();
        $commApi->type = $type;
        $commApi->is_batch = 1;
        $rtnIdList = array();
        $i = 0;
        $count = 10;
        $haveMore = TRUE;
        while ($haveMore) {
            $tenId = array_slice($id, $i, $count);
            $i += $count;
            if ($tenId) {
                $tenId = implode(',', $tenId);
                $commApi->id = $tenId;
                try {
                    $tmpIdList = $commApi->getResult();
                } catch (Comm_Weibo_Exception_Api $e) {
                    continue;
                }
                $rtnIdList = @array_merge($rtnIdList, $tmpIdList);
            } else {
                $haveMore = FALSE;
            }
        }

        $rst = array();
        if ($rtnIdList) {
            foreach ($rtnIdList as $value) {
                foreach ($value as $key => $id) {
                    if ($id != -1) {
                        $rst[$key] = $id;
                    }
                }
            }
        }

        return $rst;
    }

    /**
     *
     * 通过mid获取id
     * @param $mid array
     * @param $is_base62 mid是否是base62编码。默认是0，即没有进行base62编码。注：3z4efAo4lk类型的ID即为经过base62转换的MID。
     * @param $type id的类型：1为微博，2为评论；3为私信
     * @param $is_batch 可选参数，是否使用批量模式，值为1或者0，为1时使用批量模式，mid参数可以提交由半角逗号分隔的多个值。默认是0，非批量
     * @param $inbox 仅对私信有效，当MID类型为私信时用此参数，1为收件箱，其他为发件箱。默认为其他值
     */
    public static function statusesQueryId($mid, $isBase62 = false, $type = 1, $isBatch = 1, $inbox = 0) {
        if (!$mid) {
            return array();
        }
        $commApi = Comm_Weibo_Api_Statuses::queryid();
        $rtnMidList = array();
        $i = 0;
        $count = 10;
        $haveMore = TRUE;
        while ($haveMore) {
            $tenMid = array_slice($mid, $i, $count);
            $i += $count;
            if ($tenMid) {
                $tenMid = implode(',', $tenMid);
                $commApi->mid = $tenMid;
                $commApi->type = $type;
                $commApi->is_batch = $isBatch;
                if ($inbox) {
                    $commApi->inbox = $inbox;
                }
                $commApi->isBase62 = $isBase62 ? "1" : "0";
                $tmpMidList = $commApi->getResult();
                $rtnMidList = @array_merge($rtnMidList, $tmpMidList);
            } else {
                $haveMore = FALSE;
            }
        }

        $rst = array();
        if ($rtnMidList) {
            foreach ($rtnMidList as $value) {
                foreach ($value as $key => $id) {
                    if ($id != -1) {
                        $rst[$key] = $id;
                    }
                }
            }
        }

        return $rst;
    }
    /**
     *
     * 分组搜索
     * @param unknownType $listId
     * @param unknownType $count
     * @param unknownType $page
     * @param unknownType $feature
     * @param unknownType $sinceId
     * @param unknownType $maxId
     * @throws Dr_Exception
     */
    public static function listsMembersTimeline($uid, $listId, $sinceId = 0, $maxId = 0, $page = 1, $count = 50, $baseApp = 0, $feature = 0) {
        $commApi = Comm_Weibo_Api_Lists::membersTimeline();
        $commApi->uid = $uid;
        $commApi->list_id = $listId;
        if ($sinceId > 0) {
            $commApi->since_id = $sinceId;
        }
        if ($maxId > 0) {
            $commApi->max_id = $maxId;
        }
        $commApi->page = $page;
        $commApi->count = $count;
        $commApi->base_app = $baseApp;
        $commApi->feature = $feature;

        $rtnFeedList = $commApi->getResult();

        if ($rtnFeedList) {
            $feedTotalNum = $rtnFeedList['total_number'];
            $feedNextCursor = $rtnFeedList['next_cursor'];
            $feedPreviousCursor = $rtnFeedList['previous_cursor'];
            if (isset($rtnFeedList['statuses'])) {
                $rtnFeedList = $rtnFeedList['statuses'];
            }
        }

        if (count($rtnFeedList) == 0) {
            return array('list' => array(), 'total' => 0, 'next_cursor' => 0);
        }
        return array('list' => $rtnFeedList, 'total' => $feedTotalNum, 'next_cursor' => $feedNextCursor);
    }

    /**
     * 获取微博的评论数和转发数 （数据实时读取）
     *
     * @param array $mids
     * @return $rcNum
     */
    public static function getRcNum(array $mids) {
        $rcNum = array();
        try {
            $commApi = Comm_Weibo_Api_Statuses::statusesCount();
            $commApi->ids = implode(',', $mids);
            $commApi->is_read = 1;
            $rst = $commApi->getResult();
            foreach ($rst as $status) {
                if (!isset($status['id'])) {
                    continue;
                }
                $rcNum[$status['id']] = array('comments' => $status['comments'], 'rt' => $status['reposts'], 'reads' => $status['reads']);
            }
        } catch (Comm_Exception_Program $e) {}
        return $rcNum;
    }

    /**
     * 取得单条 微博
     *
     * @param $mid
     * @param bool     $hasRemark    是否需要用户备注信息
     * @param bool  $hasRcNum 是否需要实时的微博转发数，评论数
     */
    public static function getMblog($id, $hasRemark = true, $hasRcNum = true) {
        $mblogInfo = self::getMblogs(array($id), '', $hasRemark, $hasRcNum);
        return isset($mblogInfo[$id]) ? $mblogInfo[$id] : NULL;
    }
    /**
     * 格式化原始微博内容
     *
     * @param array $statusInfo
     * @param bool $trimUser 是否按照去掉user全信息的类型进行格式化
     */
    public static function formatRawStatus($statusInfo, $trimUser = TRUE) {
        //精简转发微博内容，只保存id和uid
        if (isset($statusInfo['retweeted_status'])) {
            $retweetedStatus = array();
            $retweetedStatus['id'] = $statusInfo['retweeted_status']['id'];
            if ($trimUser && isset($statusInfo['retweeted_status']['uid'])) {
                $retweetedStatus['uid'] = $statusInfo['retweeted_status']['uid'];
            }
            if (!$trimUser && isset($statusInfo['retweeted_status']['user']['id'])) {
                $retweetedStatus['uid'] = $statusInfo['retweeted_status']['user']['id'];
            }
            $statusInfo['retweeted_status'] = $retweetedStatus;
        }
        if (!$trimUser && isset($statusInfo['user']['id'])) {
            $statusInfo['uid'] = $statusInfo['user']['id'];
            unset($statusInfo['user']);
        }
        return $statusInfo;
    }
    
    public function formatDeleteStatus($statusInfo) {
        return array(
            'created_at' => $statusInfo['created_at'],
            'id' => $statusInfo['id'],
            'mid' => $statusInfo['mid'],
            'idstr' => $statusInfo['idstr'],
            'state' => isset($statusInfo['state']) ? $statusInfo['state'] : NULL,
            'text' => '此微博已被删除。',
            'deleted' => 1
        );
    }

    /**
     * 批量获取原始微博内容，先从l1缓存取，没有再从接口取
     *
     * @param array $mids
     * @return array
     */
    public static function getMblogsFromApi(array $mids, $isTrimUser = TRUE,&$noCacheMids = array()) {
        try{
            $statusInfos = array();
            $mids = array_unique($mids);
            if (empty($mids))return array();//param check ;return array when $mids is empty; add by maoyu 2013/01/09 
            //留言板不走cache
            if ($isTrimUser) {
                //从缓存取
                   $cacheRawStatus = new Cache_Status('STATUS');
                $keys = array();
                $cacheRawStatus->clearRawMblogInfos($mids);
                $cacheRawStatus->clearMblogInfos($mids);
                $result = $cacheRawStatus->getRawMblogInfos($mids, $keys);
                $qMids = self::filterCachedItems($result, $keys, $statusInfos);
                if (empty($qMids)) {
                    return $statusInfos;
                }
                
                $noCacheMids = array_merge($noCacheMids, $qMids);
    
                //未命中的从接口取
                $chunkMids = array_chunk($qMids, 50);
            
            } else {
                $chunkMids = array_chunk($mids, 50);
            }
            $api = Comm_Weibo_Api_Statuses::showBatch();
            $apiStatusInfos = array();
                $stateInfos = array();
            foreach ($chunkMids as $mids) {
                $info = array();
                if (!empty($mids)){
                    $api->ids = implode(",", $mids);
                    $api->trim_user = $isTrimUser;
                    $info = $api->getResult();
                }                
                if (!isset($info['statuses']) || !is_array($info['statuses'])) {
                    continue;
                }
                $apiStatusInfos = array_merge($apiStatusInfos, $info['statuses']);
                foreach ($info['states'] as $st) {
                    $stateInfos[$st['id']] = $st['state'];
                }
            }

            if (empty($apiStatusInfos)) {
                return $statusInfos;
            }

            $geoInfos = array();//格式化原始微博内容，包括解析经纬度地址
            $needCacheItems = array();//保存需要回中的缓存内容
            foreach ($apiStatusInfos as $k => $status){
                $statusInfo = self::formatRawStatus($status);
                //保存微博经纬度信息
                if (isset($statusInfo['geo']['coordinates'][0]) && isset($statusInfo['geo']['coordinates'][1]) &&
                    !empty($statusInfo['geo']['coordinates'][0]) && !empty($statusInfo['geo']['coordinates'][1])) {
                    $geoInfos[$statusInfo['id']] = $statusInfo['geo']['coordinates'];
                }
                $statusInfo['state'] = isset($stateInfos[$statusInfo['id']]) ? $stateInfos[$statusInfo['id']] : 0;
                $statusInfos[$statusInfo['id']] = $statusInfo;
                //禁评功能
                $mlevel = $statusInfos[$statusInfo['id']]['mlevel'];
                $convertMlevel = base_convert($mlevel,10,2);
                $isDisplay = Tool_eBitCal::getSpecialNum($convertMlevel, 9 , $base = 2);
                if ($isDisplay){
                    //第九位为1，则该条微博设置为禁评，不显示评论的相关内容。
                    $isDisplayComment = 'none';
                }else{
                    $isDisplayComment = 'block';
                }
                    
                $statusInfos[$statusInfo['id']]['is_display_comment'] = $isDisplayComment;
                
                //已删除的微博不中缓存
                if (!isset($status['uid'])) {
                    continue;
                }
                $needCachedItems[$keys[$statusInfo['id']]] = $statusInfo;
            }

            if (!empty($geoInfos))
            {
                $geoInfos = Dr_Geo::getGeoInfos($geoInfos);
                $langUnknownAddr = Comm_I18n::get('未知地址');
                foreach ($geoInfos as $mid => $geoInfo)
                {
                    $statusInfos[$mid]['geo']['name'] = isset($geoInfo['address']) ? $geoInfo['address'] : $langUnknownAddr;
                    $needCachedItems[$keys[$mid]]['geo']['name'] = $statusInfos[$mid]['geo']['name'];
                }
            }

            //回中未命中缓存
            if (!empty($needCachedItems) && $isTrimUser ) {
                $cacheRawStatus->createRawMblogInfos($needCachedItems);
            }
            return $statusInfos;
        }catch (Comm_Weibo_Exception_Api $e){
            return array();
        }
    }

    /**
     * 批量获取用户信息
     *
     * @param array $mids 微博mid列表
     * @param string $keyword 需要漂红的关键字
     * @param bool     $hasRemark    是否需要用户备注信息
     * @param bool  $hasRcNum 是否需要实时的微博转发数，评论数
     * @return array
     */
    public static function getMblogs($mids = array(), $keyword = '', $hasRemark = true, $hasRcNum = true) {
        if (!is_array($mids) || empty($mids)) {
            return array();
        }
        $cacheStatus = new Cache_Status('STATUS');
        $keys = array();
        //l0关闭时，不从mc取数据，直接返回指定的返回值
        $mcRtn = array();
        $result = $cacheStatus->getMblogInfos($mids, $keys, $mcRtn);
        $uids = $qMids = $mblogInfos = $forwardMids = array();
        //从缓存中取原微博,并筛选出uid和转发微博mid
        foreach ($keys as $mid => $key) {
            if (!isset($result[$key]) || $result[$key] === FALSE) {
                $qMids[] = $mid;
                continue;
            }
            //检测微博状态是否可显示
            if (!self::filterState($result[$key])) {
                continue;
            }
            //过滤已删除的微博
            if (!isset($result[$key]['uid']) || empty($result[$key]['uid'])) {
                continue;
            }
            //筛选微博作者id
            $uids[] = $result[$key]['uid'];
            //筛选转发微博微mid
            if (isset($result[$key]['retweeted_status_id'])) {
                $forwardMids[] = $result[$key]['retweeted_status_id'];
                //已删除的微博，没有用户信息
                if (isset($result[$key]['retweeted_status_uid'])) {
                    $uids[] = $result[$key]['retweeted_status_uid'];
                }
            }
            $mblogInfos[$mid] = $result[$key];
        }
        $noCacheMids = array();
        $needCacheStatus = array();
        //未命中的原创微博列表，从接口中取,并筛选出uid和转发微博mid
        if (!empty($qMids)) {
            $statuses = self::getMblogsFromApi($qMids, true, $noCacheMids);
            foreach ($statuses as $status) {
                //已删除的微博直接过滤掉
                if (!isset($status['uid'])) {
                    continue;
                }
                //检测微博状态是否可显示
                if (!self::filterState($status)) {
                    continue;
                }
                $item = $status;
                //有转发微博，筛选转微博的mid和uid
                if (isset($status['retweeted_status'])) {
                    $item['retweeted_status_id'] = $status['retweeted_status']['id'];
                    //已删除的微博没有uid信息
                    if (isset($status['retweetedStatus']['uid'])) {
                        $item['retweeted_status_uid'] = $status['retweeted_status']['uid'];
                        $uids[] = $status['retweeted_status']['uid'];
                    }
                    $forwardMids[] = $status['retweeted_status']['id'];
                    unset($item['retweeted_status']);
                }
                $uids[] = $status['uid'];
                $needCacheStatus[$keys[$item['id']]] = $item;
            }
        }
        //批量获取转发微博信息
        if (!empty($forwardMids)) {
            $forwardKeys = array();
            $forwardResult = $cacheStatus->getMblogInfos($forwardMids, $forwardKeys, $mcRtn);
            $qForwardMids = array();
            foreach ($forwardKeys as $mid => $key) {
                if (!isset($forwardResult[$key]) || $forwardResult[$key] === FALSE) {
                    $qForwardMids[] = $mid;
                    continue;
                }
                //过滤已删除的微博
                if (!isset($forwardResult[$key]['uid']) || empty($forwardResult[$key]['uid'])) {
                    continue;
                }
                $uids[] = $forwardResult[$key]['uid'];
                $mblogInfos[$mid] = $forwardResult[$key];
            }
            //未命中的转发微博，从接口取
            if (!empty($qForwardMids)) {
                $statuses = self::getMblogsFromApi($qForwardMids, true, $noCacheMids);
                foreach ($statuses as $status) {
                    $needCacheStatus[$forwardKeys[$status['id']]] = $status;
                    if (isset($status['uid'])) {
                        $uids[] = $status['uid'];
                    }
                }
            }
        }
        //渲染未命中的微博，并种缓存
        if (!empty($needCacheStatus)) {
            foreach ($needCacheStatus as $status) {
                //搜集微博短链信息
                if (isset($status['text']) && !empty($status['text'])) {
                    Tool_Analyze_Link::prepareParseLink($status['text']);
                }
            }
            //批量获取上面程序搜集的短链信息
            Tool_Analyze_Link::genShortUrlInfo();
            foreach ($needCacheStatus as $key => $status) {
                //过滤删除的微博
                if (!$status['id'] || !isset($status['uid'])) {
                    unset($needCacheStatus[$key]);
                    continue;
                }
                $mblogInfos[$status['id']] = Do_Status::formatStatus($status);
                //ssm_ext_log为@hongjun1为添加行为日志时，临时添加的一个字段，没用，直接unset掉
                if (isset($mblogInfos[$status['id']]['ssm_ext_log'])) {
                    unset($mblogInfos[$status['id']]['ssm_ext_log']);
                }
                $needCacheStatus[$key] = $mblogInfos[$status['id']];
            }          
        }
        //批量获取用户信息
        $userInfos = array();
        $remarkInfos = array();
        if (!empty($uids)) {
            $userInfos = Dr_User::getLiteUserInfos($uids);
            //批量获取备注信息
            if ($hasRemark) {
                try{
                    $remarkInfos = Dr_Relation::friendsRemarkBatch($uids);
                }catch (Comm_Exception_Program $e){
                    //不影响feed显示
                }
            }
        }
        //拼装微博列表
        $mblogList = array();
        $allMids = array_merge($mids, $forwardMids);
        //$all_mids = array_diff($all_mids, $no_cache_mids);  //目前只有get_rc_num接口返回微博阅读数，因此全部feed 的数据均从该接口取
        //获取实时的转发数据和评论数
        if ($hasRcNum && $allMids) {
            $rcNums = self::getRcNum($allMids);
        } else {
            $rcNums = array();
        }
        foreach ($mids as $mid) {
            //过滤掉获取微博列表失败的mid
            if (!isset($mblogInfos[$mid]) || empty($mblogInfos[$mid])) {
                continue;
            }
            $mblogInfo = $mblogInfos[$mid];
            if (!isset($userInfos[$mblogInfo['uid']]) || empty($userInfos[$mblogInfo['uid']])){
                continue;
            }
            if (isset($rcNums[$mid])) {
                $mblogInfo['reposts_count'] = $rcNums[$mid]['rt'];
                $mblogInfo['comments_count'] = $rcNums[$mid]['comments'];
                $mblogInfo['reads_count'] = $rcNums[$mid]['reads'];
            }

            //微博被删除，没有uid
            if (isset($mblogInfo['uid'])) {
                $mblogInfo['user'] = $userInfos[$mblogInfo['uid']];
                $mblogInfo['user']['remark'] = isset($remarkInfos[$mblogInfo['uid']]) ? $remarkInfos[$mblogInfo['uid']] : '';
            }

            //组合转发微博
            if (isset($mblogInfo['retweeted_status_id'])) {
                if (isset($mblogInfos[$mblogInfo['retweeted_status_id']])) {
                    $mblogInfo['retweeted_status'] = $mblogInfos[$mblogInfo['retweeted_status_id']];
                    if (isset($mblogInfo['retweeted_status_uid']) && $mblogInfo['retweeted_status_uid'] == 0 && self::filterState($mblogInfo['retweeted_status'])) {
                        if (isset($mblogInfo['retweeted_status']['uid']) && !empty($mblogInfo['retweeted_status']['uid'])) {
                            $mblogInfo['retweeted_status_uid'] = $mblogInfo['retweeted_status']['uid'];
                        }
                    }
                    if (isset($mblogInfo['retweeted_status_uid']) && isset($userInfos[$mblogInfo['retweeted_status_uid']]) && self::filterState($mblogInfo['retweeted_status'])) {
                        $mblogInfo['retweeted_status']['user'] = $userInfos[$mblogInfo['retweeted_status_uid']];
                        $mblogInfo['retweeted_status']['user']['remark'] = isset($remarkInfos[$mblogInfo['retweeted_status_uid']]) ? $remarkInfos[$mblogInfo['retweeted_status_uid']] : '';
                    } else {
                        //未取到根微博用户信息。视为被删除
                        $mblogInfo['retweeted_status'] = array(
                            'created_at' => '',
                            'id' => $mblogInfo['retweeted_status_id'],
                            'mid' => $mblogInfo['retweeted_status_id'],
                            'idstr' => $mblogInfo['retweeted_status_id'],
                            'text' => '此微博已被删除。',
                            'deleted' => 1
                        );
                    }
                } else {
                    //@todo 如果有用户投诉转发微博出现已删除，但实际上没有删除时，可考虑直接continue，将此微博过滤掉
                    //转发微博已删除
                    $mblogInfo['retweeted_status'] = array(
                        'created_at' => '',
                        'id' => $mblogInfo['retweeted_status_id'],
                        'mid' => $mblogInfo['retweeted_status_id'],
                        'idstr' => $mblogInfo['retweeted_status_id'],
                        'text' => '此微博已被原作者删除。',
                        'deleted' => 1
                    );
                }
                if (isset($rcNums[$mblogInfo['retweeted_status_id']])) {
                    $mblogInfo['retweeted_status']['reposts_count'] = $rcNums[$mblogInfo['retweeted_status_id']]['rt'];
                    $mblogInfo['retweeted_status']['comments_count'] = $rcNums[$mblogInfo['retweeted_status_id']]['comments'];
                    $mblogInfo['retweeted_status']['reads_count'] = $rcNums[$mblogInfo['retweeted_status_id']]['reads'];
                }
                unset($mblogInfo['retweeted_status_id'], $mblogInfo['retweeted_status_uid']);
            }
            $mblogList[$mid] = $mblogInfo;
        }
        /*
         //拼装微博的标签信息到微博内容中
        $tagInfos = self::tagsShowBatch($mids);
            foreach ($mblogList as $k => $v) {
                if (isset($tagInfos[$k])) {
                    $mblogList[$k]['tags'] = $tagInfos[$k];
                    if (isset($mblogList[$k]['tags']) && count($mblogList[$k]['tags']) > 0) {
                        $mblogList[$k]['tags_str'] = implode(' ', $mblogList[$k]['tags']);
                    } else {
                        $mblogList[$k]['tags_str'] = '';
                    }
                } else {
                    $mblogList[$k]['tags'] = array();
                    $mblogList[$k]['tags_str'] = '';
                }                
            }
            */
        return empty($keyword) ? $mblogList : self::redKeyWord($mblogList, $keyword);
    }

    /**
     * 微博表关键字漂红　
     *
     * @param array $mblogs
     * @param string $keyword
     * @return array
     */
    public static function redKeyWord(array $mblogs, $keyword) {
        if (empty($keyword) || empty($mblogs)) {
            return $mblogs;
        }
        foreach ($mblogs as $key => $mblog) {
            if (isset($mblog['text']) && !empty($mblog['text'])) {
                $mblogs[$key]['text'] = Tool_Formatter_String::redTag($mblog['text'], $keyword);
            }

            if (isset($mblog['retweeted_status']['text']) && !empty($mblog['retweeted_status']['text'])) {
                $mblogs[$key]['retweeted_status']['text'] = Tool_Formatter_String::redTag($mblog['retweeted_status']['text'], $keyword);
            }
        }
        return $mblogs;
    }

    /*
     * 获取某人发表的微博列表
     */
    public static function userTimeline($userId, $screenName = NULL, $sinceId = NULL, $maxId = NULL, $count = 20, $page = 1, $baseApp = 0, $feature = 0) {
        //缓存取数据
        $mblogList = false;
        //是否应用缓存（临时解决后台操作对前台数据的同步问题）2011-07-06 add by zhangbo
        $useCache = Comm_Config::get('control.use_cache');
        $cache = Comm_Cache::pool(self::CACHE_POOL);
        //用户发表的微博不使用缓存
        $useCache = false;
        if ($useCache) {
            if ($page == 1 && $feature == 0 && !$sinceId) {
                $key = self::mcKey(self::$_mcKeyUserTimeline, $userId);
                $mblogList = $cache->get($key, self::MC_LIVETIME);
            }
        }
        if (!$mblogList) {
            try {
                $commApi = Comm_Weibo_Api_Statuses::userTimeline();
                if ($userId) {
                    $commApi->uid = $userId;
                } else {
                    $commApi->screen_name = $screenName;
                }
                $commApi->since_id = $sinceId;
                $commApi->max_id = $maxId;
                $commApi->count = $count;
                $commApi->page = $page;
                $commApi->base_app = $baseApp;
                $commApi->feature = $feature;
                $commApi->getHttpRequest()->connectTimeout = 2000;
                $commApi->getHttpRequest()->timeout = 2000;
                $mblogList = $commApi->getResult();
                $useCache = true;
                if ($useCache) {
                    //写入缓存
                    if ($page == 1 && $feature == 0 && !$sinceId && !self::dealEmptyStatuses($mblogList)) {
                        $key = self::mcKey(self::$_mcKeyUserTimeline, $userId);
                        $cache->set($key, $mblogList, self::MC_LIVETIME);
                        $key = self::mcKey(self::$_mcKeyUserTimelineHour, $userId);
                        $cache->set($key, $mblogList, self::MC_LIVETIME_HOUR);
                    }
                }
            } catch (Comm_Exception_Program $e) {
                $cacheMblogList = false;
                if ($page == 1 && $feature == 0 && !$sinceId) {
                    $key = self::mcKey(self::$_mcKeyUserTimelineHour, $userId);
                    $cacheMblogList = $cache->get($key, self::MC_LIVETIME_HOUR);
                }
                if (!$cacheMblogList) {
                    throw $e;
                }
                $mblogList = $cacheMblogList;
            }
        }
        $doList = array('list' => array());
        if (!empty($mblogList['statuses'])) {
            if (is_array($mblogList['statuses']) && !empty($mblogList['statuses'])) {
                $doList['list'] = self::mappingStatus($mblogList['statuses']);
            }
        }
        $doList['total_number'] = $mblogList['total_number'];
        $doList['previous_cursor'] = $mblogList['previous_cursor'];
        $doList['next_cursor'] = $mblogList['next_cursor'];
        return $doList;
    }
    /*
     * 获取某人发表的微博列表ids
     */
    public static function userTimelineIds($userId, $screenName = NULL, $sinceId = NULL, $maxId = NULL, $count = 20, $page = 1, $baseApp = 0, $feature = 0,$isMobile=null) {
        try {
            $commApi = Comm_Weibo_Api_Statuses::userTimelineIds();
            if ($userId) {
                $commApi->uid = $userId;
            } else {
                $commApi->screen_name = $screenName;
            }
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->count = $count;
            $commApi->page = $page;
            $commApi->base_app = $baseApp;
            $commApi->feature = $feature;
            $commApi->getHttpRequest()->connectTimeout = 2000;
            $commApi->getHttpRequest()->timeout = 2000;
            $mblogList = $commApi->getResult();
        } catch (Comm_Exception_Program $e) {
            throw $e;
        }
        $doList = array('list' => array());
        if (!empty($mblogList['statuses']) && is_array($mblogList['statuses'])) {                     
            if ($page == 1 && $screenName == NULL && $sinceId == NULL && $maxId == NULL && $isMobile == null){
                $extinfo = Comm_Context::get('owner_ext_info', FALSE); 
                    if (isset($extinfo['topfeed']) && $extinfo['topfeed']){
                        $topfeed = trim($extinfo['topfeed'], '"');
                        if ( in_array($topfeed, $mblogList['statuses']) ) {
                            unset($mblogList['statuses'][array_search($topfeed, $mblogList['statuses'])]);
                        }
                        array_unshift($mblogList['statuses'],$topfeed);
                    }
            }                               
            $doList['list'] = $mblogList['statuses'];
        }
        $doList['marks'] = $mblogList['marks'];
        $doList['total_number'] = $mblogList['total_number'];
        $doList['previous_cursor'] = $mblogList['previous_cursor'];
        $doList['next_cursor'] = $mblogList['next_cursor'];
        return $doList;
    }

    /**
     * 获取user_timeline第一屏数据
     * @param unknown_type $uid
     */
    public static function getMyfeedFirst($uid) {
        $cacheMyfeed = new Cache_MyFeed();
        $userTimeline = $cacheMyfeed->getMyfeedFirst($uid);
        $viewer = Comm_Context::get("viewer",FALSE);
        if (empty($userTimeline) || $viewer->id == $uid) {
            $userTimeline = self::userTimelineIds($uid, NULL, NULL, NULL, 15, 1);
            if (isset($userTimeline['list']) && !empty($userTimeline['list']) && Tool_Misc::checkOwnerIsViewer()) {
                $cacheMyfeed->createMyfeedFirst($uid, $userTimeline);
            }
        }
        return $userTimeline;
    }

    public static function getUserTimeline($userId, $screenName = NULL, $sinceId = NULL, $maxId = NULL, $count = 20, $page = 1, $baseApp = 0, $feature = 0) {
        $cache = Comm_Cache::pool(self::CACHE_POOL);
        $key = self::mcKey(self::MC_KEY_RZ_USER_TIMELINE, $userId);
        try {
            //第一页前1屏mid列表从myfeed缓存中取
            if ($page == 1 && $feature == 0 && $count == 15 && !$sinceId && !$maxId) {
                $userTimeline = self::getMyfeedFirst($userId);
            } else {
                $userTimeline = self::userTimelineIds($userId, $screenName, $sinceId, $maxId, $count, $page, $baseApp, $feature);
            }
            $mids = $userTimeline['list'];
            $userTimeline['list'] = self::getMblogs($mids);
        }catch (Comm_Exception_Program $e){
            if ($page == 1 && $feature == 0 && $count == 15 && !$sinceId && !$maxId) {
                $userTimeline = $cache->get($key, self::MC_LIVETIME_RZ);
                if ($userTimeline === false) {
                    throw $e;
                }
                //记录使用容灾 缓存的次数
                $msg = "{$userId}\t" . __METHOD__ . $e->getMessage();
                // Tool_Log_Commlog::writeLog('RZ', $msg);
                if (Comm_Context::get('viewer')->id == $userId){
                    $userTimeline['list'] = self::mergeSelfMblog($userTimeline['list'], 15);
                }
                return $userTimeline;
            }
            throw $e;
        }
        if ($page == 1 && $feature == 0 && $count == 15 && !$sinceId && !$maxId) {
            $viewer = Comm_Context::get('viewer', FALSE);
            if ($viewer && $viewer->id == $userId){
                    $userTimeline['list'] = self::mergeSelfMblog($userTimeline['list'], 15);
            }
            if (1 == rand(1, 3) && $userTimeline['total_number'] >= count($userTimeline['list'])) {
                //增加种缓存概率 (TODO：因为是profile页，种缓存几率比较高，这个几率有待增加)
                $cache->set($key, $userTimeline, self::MC_LIVETIME_RZ);
            }
        }
        if ($feature != 12) {
            $tagInfos = self::tagsShowBatch($mids);
            foreach ($userTimeline['list'] as $k => $v) {
                if (isset($tagInfos[$k])) {
                    $userTimeline['list'][$k]['tags'] = $tagInfos[$k];
                    if (isset($userTimeline['list'][$k]['tags']) && count($userTimeline['list'][$k]['tags']) > 0) {
                        $userTimeline['list'][$k]['tags_str'] = implode(' ', $userTimeline['list'][$k]['tags']);
                    } else {
                        $userTimeline['list'][$k]['tags_str'] = '';
                    }
                } else {
                    $userTimeline['list'][$k]['tags'] = array();
                    $userTimeline['list'][$k]['tags_str'] = '';
                }                
            }
        }
        return $userTimeline;
    }

    /**
     * 处理statuses内容体为空的情况（兼容Api返回值statuses为空，但是total_number等大于0的情况）
     * @param unknown_type $statuses
     * @return boolean
     */
    private static function dealEmptyStatuses(&$statuses) {
        if (isset($statuses['statuses']) && is_array($statuses['statuses']) && !empty($statuses['statuses'])) {
            return false;
        } else {
            //TODO::返回值statuses为空，但是total_number等大于0的情况记录日志
            //if ($statuses ['total_number'] > 0) {
            //    //记录错误日志
            //}
            $statuses['total_number'] = 0;
            $statuses['previous_cursor'] = 0;
            $statuses['next_cursor'] = 0;
            $statuses['statuses'] = array();
            return true;
        }
    }

    /**
     * 在朋友的timeline中搜索
     *
     * @param unknown_type $uid
     * @param unknown_type $keyWord
     * @param unknown_type $start
     * @param unknown_type $count
     * @param unknown_type $startTime
     * @param unknown_type $endTime
     * @param unknown_type $isOri
     * @param unknown_type $isForward
     * @param unknown_type $isText
     * @param unknown_type $isPic
     * @param unknown_type $isVideo
     * @param unknown_type $isMusic
     * @param unknown_type $gid
     * @param unknownType $includeSelf
     * @param unknown_type $includeFriends
     * @throws Comm_Weibo_Exception_Api
     */
    public static function searchStatusesFriendsTimeline($uid, $keyWord, $start, $count, $startTime, $endTime, $isOri, $isForward, $isText, $isPic, $isVideo, $isMusic, $gid, $includeSelf = true, $includeFriends = true) {
        $commApi = Comm_Weibo_Api_Search::statusesFriendsTimeline();
        try {
            $commApi->cuid = $uid;
            $commApi->sid = ($includeSelf && $includeFriends) ? 't_atten' : ($includeSelf ? 't_mymblog' : 't_profile');
            $commApi->gid = $gid;

            $commApi->start = $start;
            $commApi->num = $count;
            $commApi->starttime = $startTime;
            $commApi->endtime = $endTime;

            $commApi->hasori = $isOri;
            $commApi->hasret = $isForward;

            if ($isText) {
                $commApi->hastext = 1;
            }
            if ($isMusic) {
                $commApi->hasmusic = 1;
            }
            if ($isPic) {
                $commApi->haspic = 1;
            }
            if ($isVideo) {
                $commApi->hasvideo = 1;
            }

            //            $comm_api->haslink = 0;
            //            $comm_api->hasat = 1;


            $commApi->key = $keyWord;

            $commApi->nofilter = 1;
            $rst = $commApi->getResult();
            //TODO http://doc.api.weibo.com/index.php/1/search/statuses/friends_timeline 接口返回规则比较复杂
            return $rst;
        } catch (Comm_Weibo_Exception_Api $e) {
            throw $e;
        }
    }

    /**
     *
     *@param    sinceId     false     int64     若指定此参数，则只返回ID比sinceId大的微博消息（即比sinceId发表时间晚的微博消息）。默认为0
     *@param maxId     false     int64     若指定此参数，则返回ID小于或等于maxId的微博消息。默认为0
     *@param count     false     integer     返回结果条数数量，默认50默认为50
     *@param page     false     integer     页码.返回的结果的页码。默认为1
     *@param filterByAuthor     false     integer     过滤类型ID （0：所有用户、1：关原创的微博）默认为0。默认为0
     *@param filterByType     false interger
     **/
    const MENTIONS_ME_CACHE_NUM = 50;
    public static function mentionsMe($sinceId = 0, $count = 20, $page = 1, $byAuth = 0, $byType = 0, $bySource = 0, $maxId = 0, $trim = 0) {
        //TODO 确认接口格式以后修改这里的做法
        $needCache = $page == 1 && $count <= self::MENTIONS_ME_CACHE_NUM && $sinceId === 0 && $byAuth === 0 && $byType === 0 && $bySource === 0 ? TRUE : FALSE;
        $unread = 0;
        if ($needCache) {
            $rtnFeedList = self::getMentionsMeByMc();
            $unread = self::getUnreadMentionMe();
        }

        if (!isset($rtnFeedList) || false == $rtnFeedList || $unread > 0) {
            $commApi = Comm_Weibo_Api_Statuses::mentions();
            if ($needCache) {
                $commApi->count = self::MENTIONS_ME_CACHE_NUM;
            } else {
                $commApi->count = $count;
            }

            $commApi->count = $count;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->page = $page;
            $commApi->filter_by_author = $byAuth;
            $commApi->filter_by_source = $bySource;
            $commApi->filter_by_type = $byType;
            $commApi->trim_user = $trim;
            $commApi->getHttpRequest()->connectTimeout = 2000;
            $commApi->getHttpRequest()->timeout = 2000;
            $commApi->getHttpRequest()->connectTimeout = 2000;
            $commApi->getHttpRequest()->timeout = 2000;
            $rtnFeedList = $commApi->getResult();
            if ($needCache) {
                self::setMentionsMeMc($rtnFeedList);
            }
        }
        $rtnMentions = array();

        $rtnMentions['total_number'] = $rtnFeedList['total_number'];

        $rtnMentions['previous_cursor'] = $rtnFeedList['previous_cursor'];
        $rtnMentions['next_cursor'] = $rtnFeedList['next_cursor'];
        $rtnMentions['mentions'] = array();

        if ($rtnMentions['total_number'] == 0) {
            return $rtnMentions;
        }

        if (!isset($rtnFeedList['statuses'])) {
            return $rtnMentions;
        }

        foreach ($rtnFeedList['statuses'] as $v) {
            if (isset($v['user'])) {
                $rtnMentions['mentions'][] = $v;
            }
        }

        //小于20条时，从50截取$count条，返回
        if (isset($rtnMentions['mentions']) && count($rtnMentions['mentions']) > 0 && $needCache) {
            $rtnMentions['mentions'] = array_slice($rtnMentions['mentions'], 0, $count);
        }

        return $rtnMentions;
    }

    /**
     * 返回at我的微博ids列表
     *
     * @param int $since_id 则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。默认为0
     * @param int $count    返回结果条数数量，默认50
     * @param int $page     指定返回结果的页码
     * @param int $by_auth  过滤类型ID （0：所有用户、1：关注的人）默认为0。
     * @param int $by_type  过滤类型ID （0：所有微博、1：原创的微博）默认为0
     * @param int $by_source 过滤类型ID （0：所有来源、1：来自微博、2：来自微群）默认为0
     * @param int $max_id   若指定此参数，则返回ID小于或等于max_id的微博消息。默认为0
     * @return 接口无异常时的正常返回值
     */
    public static function mentionsMeIds($sinceId = 0, $count = 20, $page = 1, $byAuth = 0, $byType = 0, $bySource = 0, $maxId = 0, $trim = 0) {        
        $viewer = Comm_Context::get('viewer', FALSE);
        try {
               //读取第一页的第一屏时，以一定的概率中容灾缓存，容灾缓存
            $createRzCache = FALSE;
            $newCount = 0;
            if (rand(1, 3) == 1 && $sinceId == 0 && $count == 15 && $page == 1 && $byAuth == 0 && $byType == 0 && $bySource == 0 && $maxId == 0 && $trim == 0) {
                $createRzCache = TRUE;
                //灾缓缓存一页的数据
                $newCount = 45;
            }
            $commApi = Comm_Weibo_Api_Statuses::mentionsIds();
            $commApi->count = $newCount == 0 ? $count : $newCount;
            $commApi->since_id = $sinceId;
            $commApi->max_id = $maxId;
            $commApi->page = $page;
            $commApi->filter_by_author = $byAuth;
            $commApi->filter_by_source = $bySource;
            $commApi->filter_by_type = $byType;
            $commApi->trim_user = $trim;
            $commApi->getHttpRequest()->connectTimeout = 2000;
            $commApi->getHttpRequest()->timeout = 2000;
            $rtnFeedList = $commApi->getResult();
            $rtnFeedList['statuses'] = isset($rtnFeedList['statuses']) ? $rtnFeedList['statuses'] : array();
            if ($createRzCache && $viewer !== FALSE) {
                $cacheMyat = new Cache_Myat();    
                $cacheMyat->createAtmeMblog($viewer->id, $rtnFeedList);
                //需要中缓存时，count数被设置成了45条，返回时只需返回15条
                $rtnFeedList['statuses'] = array_slice($rtnFeedList['statuses'], 0, $count);
            }
        } catch (Comm_Exception_Program $e) {
            //如果是第一页，从容灾缓存中取
            if ($viewer !== FALSE && $sinceId == 0 && $count == 15 && ($page == 1 || $page == 2 || $page === 3) && $byAuth == 0 && $byType == 0 && $bySource == 0 && $maxId == 0 && $trim == 0) {
                $cacheMyat = new Cache_Myat();    
                $rtnFeedList = $cacheMyat->getAtmeMblog($viewer->id);
                if (FALSE === $rtnFeedList) {
                    throw $e;
                }
                $offset = ($page - 1) * $count;
                $rtnFeedList['statuses'] = array_slice($rtnFeedList['statuses'], $offset, $count);
                // Tool_Log_Commlog::writeLog('RZ', __METHOD__ . "," . $e->getMessage());
            } else {
                throw $e;
            }
        }
        $rtnMentions = array();
        $rtnMentions['total_number'] = isset($rtnFeedList['total_number']) ? $rtnFeedList['total_number'] : 0;
        $rtnMentions['previous_cursor'] = isset($rtnFeedList['previous_cursor']) ? $rtnFeedList['previous_cursor'] : 0;
        $rtnMentions['next_cursor'] = isset($rtnFeedList['next_cursor']) ? $rtnFeedList['next_cursor'] : 0;
        $rtnMentions['mentions'] = isset($rtnFeedList['statuses']) ? $rtnFeedList['statuses'] : array();
        return $rtnMentions;
    }

    public static function getMentionsMe($sinceId = 0, $count = 20, $page = 1, $byAuth = 0, $byType = 0, $bySource = 0, $maxId = 0, $trim = 0) {
        //获取at我的微博ids列表
        try {
            $mentionsMe = self::mentionsMeIds ( $sinceId, $count, $page, $byAuth, $byType, $bySource = 0, $maxId = 0, $trim = 0 );
            if (empty ( $mentionsMe ['mentions'] )) {
                return $mentionsMe;
            }
            $ids = $mentionsMe ['mentions'];
            $mentionsMe ['mentions'] = self::getMblogs ( $ids );
            if (rand(1,3) == 1 && $sinceId == 0 && $count == 15 && $page == 1 && $byAuth == 0 && $byType == 0 && $bySource == 0 && $maxId == 0 && $trim == 0) {
                $mcRz = new Cache_Status ();
                $cuser = Comm_Context::get ( "viewer" );
                $mcRz->createAtmeMblog( $cuser->id, $mentionsMe );
            }
            return $mentionsMe;
        } catch ( Comm_Weibo_Exception_Api $e ) {
            $mcRz = new Cache_Status ();
            $cuser = Comm_Context::get ( "viewer" );
            $mentionsMe = $mcRz->getAtmeMblog( $cuser->id );
            // Tool_Log_Commlog::writeLog('RZ', __METHOD__ . "," . $e->getMessage());
            if (false !== $mentionsMe){
                return $mentionsMe;
            }
            throw $e;
        }
    }

    public static function getGeo($long, $lnt) {
        $cache = Comm_Cache::pool(self::CACHE_POOL);
        $key = self::mcKey(self::MC_KEY_GEO_INFO, $long, $lnt);
        $geoInfo = $cache->get($key);
        if (FALSE !== $geoInfo) {
            return $geoInfo;
        }
        try {
            $api = Comm_Weibo_Api_Location::getAddress();
            $api->coordinates = "{$lnt},{$long},g0";
            $geoInfo = $api->getResult();
            if (isset($geoInfo['g0']) && is_array($geoInfo['g0'])) {
                if (isset($geoInfo['g0']['address']) && !empty($geoInfo['g0']['address'])){
                    //地理位置没获取到不打缓存
                    $cache->set($key, $geoInfo, self::MC_GEO_INFO_LIVETIME);
                }
            }
            return $geoInfo;
        } catch (Comm_Exception_Program $e) {
            throw new Dr_Exception($e);
        }
    }

    public static function mentionsMeSearch($uid, $fuid, $searchKey, $page, $count) {

        $api = Comm_Weibo_Api_Search::statusesMentions();
        $api->cuid = $uid;
        $api->key = $searchKey;
        $api->start = ($page - 1) * $count;
        $api->num = $count;
        $api->atme = "1";
        $api->sid = "t_atme";
        $api->onlymid = "1";
        $api->nofilter = 1;
        $info = $api->getResult(false);

        $rtn['total_number'] = isset($info['m']) ? $info['m'] : 0;
        if ($rtn['total_number'] > 0) {
            foreach ($info['result'] as $v) {
                $mids[] = $v;

            }

            //批量取得取得微博id
            $midList = self::statusesQueryId($mids);
            if (is_array($midList) && count($midList) > 0) {
                $midTmpList = array_values($midList);
                //批量取得微博
                $statusInfo = self::getMblogs($midTmpList, $searchKey);
                $rtn['mentions'] = $statusInfo;
            }
        } else {
            $rtn['mentions'] = array();
        }
        return $rtn;
    }

    /*
     * 搜索及筛选某人发表的微博
     */
    public static function searchStatusesUserTimeline($searchCondition, $cuid, $uid, $page = 1, $num = 10) {
        $commApi = Comm_Weibo_Api_Search::statusesUserTimeline();
        if (isset($searchCondition['start_time']) && NULL != $searchCondition['start_time']) {
            $searchCondition['start_time'] = strtotime($searchCondition['start_time']);
            $commApi->starttime = $searchCondition['start_time'];
        }
        if (isset($searchCondition['end_time']) && NULL != $searchCondition['end_time']) {
            $numEndTime = strtotime($searchCondition['end_time']);
            $searchCondition['end_time'] = strtotime("+1 day", $numEndTime);
            $commApi->endtime = $searchCondition['end_time'];
        }
        if (isset($searchCondition['is_pic'])) {
            $commApi->haspic = $searchCondition['is_pic'];
        }
        if (isset($searchCondition['is_ori'])) {
            $commApi->hasori = $searchCondition['is_ori'];
        }
        if (isset($searchCondition['is_forward'])) {
            $commApi->hasret = $searchCondition['is_forward'];
        }
        if (isset($searchCondition['is_video'])) {
            $commApi->hasvideo = $searchCondition['is_video'];
        }
        if (isset($searchCondition['is_music'])) {
            $commApi->hasmusic = $searchCondition['is_music'];
        }
        if (isset($searchCondition['is_text'])) {
            $commApi->hastext = $searchCondition['is_text'];
        }
        if (isset($searchCondition['key_word'])) {
            $commApi->key = $searchCondition['key_word'];
        }
        if ($uid) {
            $commApi->uid = $uid;
        }
        if (isset($searchCondition['ids']) && $searchCondition['ids']) {
            $commApi->ids = $searchCondition['ids'];
        }
        $commApi->cuid = $cuid;
        $commApi->start = ($page - 1) * $num;
        $commApi->num = $num;
        $result = array('list' => array());
        if ($cuid == $uid) {
            $commApi->sid = "t_mymblog";
            $commApi->nofilter = 1;
        } else {
            $commApi->sid = "t_profile";
        }
        
        try {
            $mblogList = $commApi->getResult();
            //uid为空则为
            if (!$uid && isset($searchCondition['ids']) && $searchCondition['ids']) {
                return $mblogList;
            }
            $result['total_number'] = $mblogList['m2'];
            if (!empty($mblogList['result'])) {
                $result['list'] = self::formatSearchUserTimeline($mblogList['result']);
            }
            return $result;

        } catch (Comm_Exception_Program $e) {
            throw $e;
        }
    }
    /**
    *    新搜索微博接口 profile页，home首页，@我 页面使用同一个
    *    profile需要锁定uid，home页atten=1限定，不限定uid，@我atme=1限定
    */
    public function searchWB($searchCondition, $cuid,$uid,$count = 10, $page = 1) {
        try{
            $commApi = Comm_Weibo_Api_Search::statusesSearch ();
            $commApi->q = $searchCondition['key_word'];
            $commApi->cuid = $cuid;
            if ($uid)
            $commApi->uid = $uid;
            $commApi->sid = 'e_weibo';
            $commApi->count = $count;
            $commApi->starttime = strtotime($searchCondition['start_time']);
            $commApi->endtime = strtotime($searchCondition['end_time']." 23:59:59");
            $commApi->atten = $searchCondition['atten'];
            $commApi->atme = $searchCondition['atme'];
            $commApi->page = $page;
            $commApi->hasori = $searchCondition['is_ori'];
            $commApi->hasret = $searchCondition['is_forward'];
            $commApi->hastext = $searchCondition['is_text'];
            $commApi->haspic = $searchCondition['is_pic'];
            $commApi->hasvideo = $searchCondition['is_video'];
            $commApi->hasmusic = $searchCondition['is_music'];
            $commApi->hasv = $searchCondition['hasv'];//是否是V用户
            $commApi->ids = $searchCondition['ids'];//指定一批用户的
            $rst = $commApi->getResult();
            return $rst;
        }catch (Exception $e){
            return array();
        }
    }
    public static function formatSearchUserTimeline($searchUserTimeline) {
        $mids = array_map(create_function('$a', 'return $a["mid"];'), $searchUserTimeline);
        $ids = self::statusesQueryId($mids);
        $ids = array_values($ids);
        if (empty($ids) && count($mids) > 0) {
            throw new Dr_Exception("query ids error");
        }
        $mblog = self::getMblogs($ids);
        return $mblog;
    }

    /**
     * 获取指定微博的评论转发状态
     *
     * @param array $mblog_info
     * @return array
     */
    public static function getAllowCommentAndForward(array $mblogInfo) {
        $allowInfo = array();
        //是否可以被转发
        $allowInfo['allowForward'] = 1;
        $allowInfo['allowComment'] = 1;
        if (isset($mblogInfo['retweeted_status']) && !isset($mblogInfo['retweeted_status']['user'])) {
            $allowInfo['allowForward'] = 0;
        }
        if (!isset($mblogInfo['user']['id'])) {
            return $allowInfo;
        }

        //是否可评论
        $uids = array();
        $uid = $mblogInfo['user']['id'];
        $uids[] = $uid;
        if (isset($mblogInfo['retweeted_status']['user']['id'])) {
           $rootUid = $mblogInfo['retweeted_status']['user']['id'];
           $uids[] = $rootUid;
        }
        $privacy = Dr_Privacy::getPrivacyBatch($uids);
        $viewerId = Comm_Context::get('viewer')->id;
        if (isset($privacy[$uid]['comment']) && $privacy[$uid]['comment'] == 1) {
            $relation = Dr_Relation::checkRelation($viewerId, $uid);
            if ($relation != Dr_Relation::RELATION_BILATERAL && $relation != Dr_Relation::RELATION_FOLLOWED) {
                $allowInfo['allowComment'] = 0;
            }
        }

        //根微博是否可评论
        if (isset($rootUid)) {
            $allowInfo['allowRootComment'] = 1;
            if (isset($privacy[$rootUid]['comment']) && $privacy[$rootUid]['comment']) {
                $relation = Dr_Relation::checkRelation($viewerId, $rootUid);
                if ($relation != Dr_Relation::RELATION_BILATERAL && $relation != Dr_Relation::RELATION_FOLLOWED) {
                    $allowInfo['allowRootComment'] = 0;
                }
            }
        }
        return $allowInfo;
    }
    /**
     * 通过谣言微博的mid获取辟谣的说明信息
     * @param Array $mids 谣言微博mid
     * @return Array
     *
     */
    public static function getRumorInfoByMid($mids) {
        try{
            $cacheStatus = new Cache_Status();
            $keys = array();
            $result = $cacheStatus->getRumorMblogInfos($mids, $keys);

            //最终返回结果
            $data = $qMids = array();
            foreach ($mids as $mid) {
                if (isset($result[$keys[$mid]]) && $result[$keys[$mid]] !== false) {
                    if (isset($result[$keys[$mid]]['ext'])) {
                        $data[$mid] = $result[$keys[$mid]];
                    }
                } else {
                    $qMids[] = $mid;
                }
            }

            //判断是否需要查询接口
            if (empty($qMids)) {
                return $data;
            }

            //请求接口数据
            $commApi = Comm_Weibo_Api_Admin::getRumorInfoByMid();
            $commApi->mids = implode(",", $qMids);
            if (Tool_Misc::isUseUnloginAuth()) {
                $commApi->addUserpsw(); //指定用户名、密码方式访问
            }
            $result = $commApi->getResult();
            $setMcData = array();
            if (!isset($result['error_code'])) {
                foreach ($qMids as $mid) {
                    if (isset($result[$mid]) && !empty($result[$mid]['ext'])) {
                        $current = $result[$mid];
                        $toolsAnalyzeAt = new Tool_Analyze_At();
                        //解析微博提示@
                        if (!empty($current['ext']['showcontent'])){
                            $atName = $toolsAnalyzeAt->getAtUsername($current['ext']['showcontent']);
                            if (count($atName)>0){
                                  $toolsAnalyzeAt->atToLink($current['ext']['showcontent'], $atName);
                            }
                        }
                        if (!empty($current['ext']['url'])){
                            $current['ext']['showcontent'] .=  " <a target='_blank' href='".$current['ext']['url']."'>" . Comm_I18n::get('rumor_ext') . "</a>";
                        }
                        //評論提示  @解析
                        if (!empty($current['ext']['cmt_desc'])){
                            $atName = $toolsAnalyzeAt->getAtUsername($current['ext']['cmt_desc']);
                            if (count($atName)>0){
                                  $toolsAnalyzeAt->atToLink($current['ext']['cmt_desc'], $atName);
                            }
                            $current['ext']['rumor_comment'] = $current['ext']['cmt_desc'];
                        } else {
                            $current['ext']['rumor_comment'] = Comm_I18n::get('rumor_comment');
                        }
                        if (!empty($current['ext']['fwd_desc'])){
                            $atName = $toolsAnalyzeAt->getAtUsername($current['ext']['fwd_desc']);
                            if (count($atName)>0){
                                  $toolsAnalyzeAt->atToLink($current['ext']['fwd_desc'], $atName);
                            }
                        } else {
                            $current['ext']['fwd_desc'] = Comm_I18n::get('rumor_comment');
                        }
                        
                        $data[$mid] = $current;
                        $setMcData[$keys[$mid]] = $current;
                    } else {
                        $setMcData[$keys[$mid]] = '';
                    }
                }

                //存缓存数据
                $cacheStatus->createRumorMblogInfos($setMcData);
            }

            return $data;
        }catch (Comm_Weibo_Exception_Api $e) {
            return array();
        }
    }

    /**
     * 从feed列表中查找出谣言微博
     * @param unknown_type $feed_list
     * @param $list_type 分析对象的类型，目前仅支持feed，作为扩展字段,暂未使用
     */
    public static function getRumourListFromStatus($list, $listType = 'feed') {
        try{
            $RumorList = array();
            foreach ($list as $feed) {
                if ('fav' == $listType) {
                    $feed = $feed['status'];
                }
                if (isset($feed['retweeted_status']['mid'])){
                    $RumorList[] = $feed['retweeted_status']['mid'];
                }
                if (isset($feed['mlevel']) &&  (($feed['mlevel'] & self::RUMOR_MBLOG_MLEVEL) || ($feed['mlevel'] & self::REPORT_MBLOG_MLEVEL)) )  {
                    $RumorList[] = $feed['mid'];
                }
            }
            $RumorList = array_unique($RumorList);
            if (empty($RumorList)) return array();
            //谣言微博的辟谣信息
            $RumorData = self::getRumorInfoByMid($RumorList);
            return $RumorData;
        }catch (Comm_Exception_Program $e) {
            return array();
        }
    }

    private function getMentionsMeKey() {
        $user = Comm_Context::get("viewer");
        $uid = $user->id;
        $key = sprintf(self::MC_KEY_MENTION_ME, Comm_Config::get("cache_key.status"), $uid);
        return $key;
    }
    private function getMentionsMeByMc() {
        $key = self::getMentionsMeKey();
        $list = Comm_Cache::get(self::CACHE_POOL)->get($key);
        return $list;
    }

    private function setMentionsMeMc($list) {
        $key = self::getMentionsMeKey();
        $list = Comm_Cache::get(self::CACHE_POOL)->set($key, $list, self::MC_LIVETIME);
        return true;
    }

    private function getUnreadMentionMe() {
        return Dr_Remind::unread(Comm_Context::get("viewer"), Dr_Remind::ATME);
    }
    
    /**
     * 获取指定用户的微博标签列表
     * 
     */
    public static function tags($uid, $count, $page) {
        try {
            $commApi = Comm_Weibo_Api_Statuses::tags();
            $commApi->uid = $uid;
            $commApi->count = $count;
            $commApi->page = $page;
            $re = $commApi->getResult();
            return $re;
        } catch (Comm_Weibo_Exception_Api $e) {
            $re = array();
        }
    }
    
    /**
     * 获取当前用户某个标签的微博ID列表 
     * 
     */
    public static function tagTimelineIds($tag, $uid, $sinceId, $maxId, $count = 15, $page = 1) {
        try {
                $commApi = Comm_Weibo_Api_Statuses::tagTimelineIds();
                $commApi->tag = $tag;
                $commApi->uid = $uid;
                $commApi->since_id = $sinceId;
                $commApi->max_id = $maxId;
            $commApi->count = $count;
            $commApi->page = $page;
            $re = $commApi->getResult();
            return $re;
        } catch (Comm_Weibo_Exception_Api $e) {
            $re = array();
        }
    }
  
    /**
     * 根据提供的ID批量获取微博标签的信息
     * 
     */
    public static function tagsShowBatch($mids) {
        try {
            if (empty($mids)){
                return array();
            }
            $commApi = Comm_Weibo_Api_Statuses::tagsShowBatch();
            $commApi->ids = implode(',', $mids);
            $re = $commApi->getResult();
            if (isset($re['result'])) {
                foreach ($re['result'] as $v) {
                    $tags[$v['status_id']] = $v['tags'];                      
                }               
            }
            return $tags;
        } catch (Comm_Weibo_Exception_Api $e) {
            $tags = array();
        }
    }
    /**
     * 取筛选feed信息
     * 
     */
    public static function getUserTimelineTag($tag, $userId, $sinceId = 0, $maxId = 0, $count = 15, $page = 1) {
        try {
            $userTimeline = self::tagTimelineIds($tag, $userId, $sinceId, $maxId, $count, $page);
            $mids = $userTimeline['statuses'];
            $userTimeline['list'] = self::getMblogs($mids);
        } catch (Comm_Weibo_Exception_Api $e) {
            $userTimeline = array();
        }
        $tagInfos = self::tagsShowBatch($mids);    
        foreach ($userTimeline['list'] as $k => $v) {
            if (isset($tagInfos[$k])) {
                $userTimeline['list'][$k]['tags'] = $tagInfos[$k];
                if (isset($userTimeline['list'][$k]['tags']) && count($userTimeline['list'][$k]['tags']) > 0) {
                    $userTimeline['list'][$k]['tags_str'] = implode(' ', $userTimeline['list'][$k]['tags']);
                } else {
                    $userTimeline['list'][$k]['tags_str'] = '';
                }
            } else {
                $userTimeline['list'][$k]['tags'] = array();
                $userTimeline['list'][$k]['tags_str'] = '';
            }                
        }
        return $userTimeline;
    }
    
}
