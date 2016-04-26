<?php
class Dr_Comment extends Dr_Abstract
{    
    const COMMENT_CACHE_NUM = 40;

    /**
     * 获取评论信息
     *
     * 缓存策略:
     * 大评论缓存，小评论直接从该缓存中取，小评论缓存未命中时，向接口取20条，中大评论缓存，然后截取$count条返回
     *
     * @param int $id            
     * @param int $count            
     * @param int $page            
     * @return array Do_Comment
     */
    public static function getCommentListByMid($id, $count = 10, $page = 1, $sinceId = null, $maxId = null, $filterByAuthor = 0)
    {
        if ($page <= 2 && empty($filterByAuthor) && Comm_Config::get('control.use_comment_list_recovery'))
        {
            $cacheCmtIndexObj = new Cache_CommentIndex();
            $cacheCmtContentObj = new Cache_CommentContent();
            $cmtIndex = $cacheCmtIndexObj->getCommentIndex($id);
            $setCache = 1;
            $getNum = self::COMMENT_CACHE_NUM;
        }
        else
        {
            $cmtIndex = false;
            $setCache = 0;
            $getNum = $count;
        }

        if ($cmtIndex === false)
        {
            try
            {
                $commApi = Comm_Weibo_Api_Comments::show();
                $commApi->id = $id;
                $commApi->page = $page;
                $commApi->count = $getNum;
    
                if ($filterByAuthor)
                {
                    $commApi->filterByAuthor = $filterByAuthor;
                }
    
                $rtnCommentList = $commApi->getResult();
            }
            catch (Exception $e)
            {
                throw new Dr_Exception($e);
            }
            
            $mcResult = self::_makeMcDate($rtnCommentList);
            if (isset($mcResult['cids'][$id]) && is_array($mcResult['cids'][$id]) && !empty($mcResult['cids'][$id]))
            {
                $tmp = array_chunk($mcResult['list'], $count);
                $rtnCommentList['comments'] = $tmp[0];

                if (!empty($setCache))
                {
                    $mcShortLiveTime = ($mcResult['cids'][$id]['total_number'] < 10);

                    $cacheCmtIndexObj->setCommentIndexs(array($id => $mcResult['cids']), $mcShortLiveTime);
                    $cacheCmtContentObj->setCommentContents($mcResult['list']);
                }
            }
        }
        else
        {
            $cids = array_chunk($cmtIndex[$id], $count, true);
            $rtnCommentList['comments'] = $cacheCmtContentObj->getCommentContents($cids[$page - 1]);
            $getCid = array();

            foreach ($cids[$page - 1] as $cid => $value)
            {
                // 获得没有命中cache的评论内容的评论ID
                if (is_numeric($cid))
                {
                    $key = $cacheCmtContentObj->key("cmtcontent", $cid);
                    
                    if (!isset($rtnCommentList['comments'][$key]) || !is_array($rtnCommentList['comments'][$key]) || empty($rtnCommentList['comments'][$key]))
                    {
                        $getCid[$key] = $cid;
                    }
                }
            }

            if (!empty($getCid))
            {
                // 获取没有命中cache的评论内容并补种到缓存中
                if (count($getCid) > 50)
                {
                    $tmp = array_chunk($getCid, 50);
                    $getCid = $tmp[0];
                }

                try
                {
                    $commApi = Comm_Weibo_Api_Comments::showBatch();
                    $commApi->cids = implode(',', $getCid);
                    $commentList = self::_makeMcDate(array('comments' => $commApi->getResult()));
                }
                catch (Exception $e)
                {
                    throw new Dr_Exception($e);
                }
                
                if (!empty($commentList['list']))
                {
                    $cacheCmtContentObj->setCommentContents($commentList['list']);
                    foreach ($commentList['list'] as $cid => $value)
                    {
                        $key = $cacheCmtContentObj->key("cmtcontent", $cid);
                        $rtnCommentList['comments'][$key] = $value;
                    }
                }

                // 将微博内容为空的数据过滤掉
                foreach ($getCid as $key => $cid)
                {
                    if (!isset($rtnCommentList['comments'][$key]) || !is_array($rtnCommentList['comments'][$key]) || empty($rtnCommentList['comments'][$key]))
                    {
                        unset($rtnCommentList['comments'][$key]);
                    }
                }
            }

            krsort($rtnCommentList['comments']);
            $rtnCommentList['total_number'] = $cmtIndex[$id]['total_number'];
            $rtnCommentList['previous_cursor'] = 0;
            $rtnCommentList['next_cursor'] = 0;
        }
        
        return self::_mapping($rtnCommentList, $page);
    }

    private static function _makeMcDate($result)
    {
        $cids = array();
        $list = array();

        if (is_array($result['comments']) && !empty($result['comments']))
        {
            $mid = 0;

            foreach ($result['comments'] as $content)
            {
                $mid = $content['status']['id'];
                $cids[$content['status']['id']][$content['id']] = 1;
                $list[$content['id']]['created_at'] = $content['created_at'];
                $list[$content['id']]['id'] = $content['id'];
                $list[$content['id']]['text'] = $content['text'];
                $list[$content['id']]['source'] = $content['source'];
                $list[$content['id']]['user'] = $content['user'];
                $list[$content['id']]['status_id'] = $content['status']['id']; // 微博ID
                $list[$content['id']]['mid'] = $content['mid']; // 老评论ID
                
                if (isset($content['reply_comment']) && is_array($content['reply_comment']))
                {
                    $list[$content['id']]['raw_id'] = $content['reply_comment']['id'];
                }
            }

            if (isset($result['total_number']))
            {
                $cids[$mid]['total_number'] = $result['total_number'];
            }
        }

        return array(
            'cids' => $cids,
            'list' => $list,
        );
    }

    private static function _mapping($rtnCommentList, $page = 1, $keyWord = "")
    {
        $commentList = array();
        $totalNumber = 0;

        if ($rtnCommentList && isset($rtnCommentList['comments']))
        {
            $totalNumber = $rtnCommentList['total_number'];
            $nextCursor = $rtnCommentList['next_cursor'];
            $previousCursor = $rtnCommentList['previous_cursor'];
            
            if (isset($rtnCommentList['comments']))
            {
                if ($rtnCommentList['comments'])
                {
                    foreach ($rtnCommentList['comments'] as $key => $comment)
                    {
                        try
                        {
                            /*
                             * if (!$comment['status']['mid']){ continue; }
                             */
                            $formatedComment = self::formatComment($comment, $keyWord);
                        }
                        catch (Exception $e)
                        {
                            continue;
                        }

                        $commentList[$key] = $formatedComment;
                    }
                }
            }
        }

        return array(
            'comment_list' => $commentList,
            'total_number' => $totalNumber,
            'page' => $page,
        );
    }

    public static function formatComment($data, $keyWord = '')
    {
        // 对个性数据进行单独处理
        $rawText = $data['text'];
        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case 'text':
                    $value = htmlspecialchars($value);
                    
                    // link信息
                    $rtn = Tool_Analyze_Link::parseLinkToHtml($value);
                    $content = $rtn ['content'];
                    $media = $rtn ['media'];
                    
                    // @信息
                    $toolsAnalyzeAt = new Tool_Analyze_At();
                    $atName = Tool_Analyze_At::getAtUsername($content);
                    $content = Tool_Analyze_At::replaceWeihaoToNick($content, $atName);
                    if (count($atName))
                    {
                        Tool_Analyze_At::atToLink($content, $atName);
                    }

                    // tag信息
                    $tollsAnalyzeTag = new Tool_Analyze_Tag();
                    $content = Tool_Analyze_Tag::renderTag($content);
                    
                    // 表情
                    $value = Tool_Analyze_Icon::textToIcon($content);
                    if ($keyWord)
                    {
                        $value = Tool_Formatter_String::redTag($value, $keyWord);
                    }

                    break;

                case 'created_at':
                    $value = str_replace('CST', '+0800', $value);
                    $value = Tool_Formatter_Time::timeFormat(strtotime($value));
                    break;

                case 'status':
                    $value ['text'] = htmlspecialchars($value ['text']);
                    // 原文截止
                    $value ['text'] = Tool_Formatter_String::contentTruncate($value ['text'], 30);
                    $atName = Tool_Analyze_At::getAtUsername($value ['text']);
                    $value ['text'] = Tool_Analyze_At::replaceWeihaoToNick($value ['text'], $atName);
                    $value ['text'] = Tool_Analyze_Icon::textToIcon($value ['text']);
                    $value ['user'] = Dr_User::formatUserInfo($value ['user']);
                    break;

                case 'reply_comment':
                    if ($value)
                    {
                        $value['text'] = htmlspecialchars($value['text']);
                        // 原文截止
                        $value['text'] = Tool_Formatter_String::contentTruncate($value['text'], 20);
                        $atName = Tool_Analyze_At::getAtUsername($value['text']);
                        $value['text'] = Tool_Analyze_At::replaceWeihaoToNick($value['text'], $atName);
                        $value['text'] = Tool_Analyze_Icon::textToIcon($value['text']);
                        $value['user'] = Dr_User::formatUserInfo($value['user']);
                    }

                    break;

                case 'source' :
                    $pattern = "/(wap|msg|iphone|android|s60|kjava|wm|blackberry|ipad)\.php/";
                    $cellphone = preg_match($pattern, $value, $matches);
                    if ($cellphone == 1)
                    {
                        $title = strip_tags($value) . Comm_I18n::get('发布');
                        $href = Comm_Config::get("domain.weibo") . "/mobile/{$matches[0]}";
                        $data['source_mobile'] = "<a href=\"{$href}\" target=\"__blank\" ><span class=\"feedico_mobile\" title=\"{$title}\"></span></a>";
                    }
                    else
                    {
                        $data['source_mobile'] = '';
                    }
                    
                    $annotations = isset($data['annotations']) ? $data['annotations'] : null;
                    $value = Tool_Analyze_Source::formatSource($value, $annotations);
                    break;

                case 'user' :
                    $value = Dr_User::formatUserInfo($value);
                    break;

                default :
                    break;
            }

            $data[$key] = $value;
        }

        $data['raw_text'] = $rawText; // 评论原文、未渲染。

        return $data;
    }

    public static function initComment($user, $text)
    {
        return array(
            'text' => $text,
            'without_mention' => 0, // 1：回复中不自动加入“回复@用户名”，0：回复中自动加入“回复@用户名”.默认为0.
            'comment_ori' => 0, // 当回复一条转发微博的评论时，是否评论给原微博。0:不评论给原微博。1：评论给原微博。默认0.
            'source_mobile' => 0,
            'user' => $user,
        );
    }
    
    /**
     *
     * 我所发出的评论列表
     * 
     * @param int $count            
     * @param int $page            
     * @param int64 $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。默认为0
     * @param int64 $max_id 若指定此参数，则返回ID小于或等于max_id的微博消息。默认为0
     * @param int $filter_by_source 返回结果过滤。默认为0：返回全部。1：返回来自微博的评论；2：返回来自微群的评论。
     *            
     */
    public static function commentsByMe($count = 20, $page = 1, $sinceId = null, $maxId = null, $filterBySource = 0)
    {
        $commApi = Comm_Weibo_Api_Comments::byMe();
        $commApi->page = $page;
        $commApi->count = $count;

        if (!is_null($sinceId))
        {
            $commApi->since_id = $sinceId;
        }

        if (!is_null($maxId))
        {
            $commApi->max_id = $maxId;
        }

        if ($filterBySource)
        {
            $commApi->filter_by_source = $filterBySource;
        }
        
        try
        {    
            $rtnCommentList = $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        if ($rtnCommentList && $rtnCommentList['total_number'] != 0 && empty($rtnCommentList['comments']) && $page > 1)
        {
            $page = $page - 1;
            $commApi->page = $page;
            $rtnCommentList = $commApi->getResult();

            if ($rtnCommentList && $rtnCommentList['total_number'] != 0 && empty($rtnCommentList['comments']) && $page > 1)
            {
                $page = 1;
                $commApi->page = $page;
                $rtnCommentList = $commApi->getResult();
            }
        }

        return self::_mapping($rtnCommentList, $page);
    }

    /**
     * 用户接收到的评论列表
     * 
     * @param int $count            
     * @param int $page            
     * @param int64 $sinceId 若指定此参数，则只返回ID比sinceId大的微博消息（即比since_id发表时间晚的微博消息）。默认为0
     * @param int64 $maxId 若指定此参数，则返回ID小于或等于max_id的微博消息。默认为0
     * @param int $filterByAuthor 筛选类型ID（0：全部，1：我关注的人，2：陌生人）默认为0
     * @param int $filterBySource 返回结果过滤。默认为0：返回全部。1：返回来自微博的评论；2：返回来自微群的评论。
     * @return <type>
     */
    public static function commentsToMe($count = 20, $page = 1, $sinceId = null, $maxId = null, $filterByAuthor = 0, $filterBySource = 0)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::toMe();
            $commApi->page = $page;
            $commApi->count = $count;

            if (!is_null($sinceId))
            {
                $commApi->since_id = $sinceId;
            }

            if (!is_null($maxId))
            {
                $commApi->max_id = $maxId;
            }

            if ($filterByAuthor)
            {
                $commApi->filter_by_author = $filterByAuthor;
            }

            if ($filterBySource)
            {
                $commApi->filter_by_source = $filterBySource;
            }

            $rtnCommentList = $commApi->getResult();
            if (1 == rand(1, 3) && $count == 20 && $page == 1 && !$sinceId && !$maxId && $filterByAuthor == 0 && $filterBySource == 0)
            {
                $mcObj = new Cache_Comment();
                $cuser = Comm_Context::get("viewer");
                $mcObj->createCommentInbox($cuser->id, $rtnCommentList);
            }
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            $mcObj = new Cache_Comment();
            $cuser = Comm_Context::get("viewer");
            $rtnCommentList = $mcObj->getCommentInbox($cuser->id);
            
            if (false === $rtnCommentList)
            {
                throw new Dr_Exception($e);
            }
        }

        if ($rtnCommentList && $rtnCommentList['total_number'] != 0 && empty($rtnCommentList['comments']) && $page > 1)
        {
            $page = $page - 1;
            $commApi->page = $page;
            $rtnCommentList = $commApi->getResult();
            if ($rtnCommentList && $rtnCommentList['total_number'] != 0 && empty($rtnCommentList['comments']) && $page > 1)
            {
                $page = 1;
                $commApi->page = $page;
                $rtnCommentList = $commApi->getResult();
            }
        }

        return self::_mapping($rtnCommentList, $page);
    }

    /**
     *
     * 热门评论
     * 
     * @param $count
     * @param $type
     * @param $baseApp
     */
    public static function commentHot($count, $type = 'weekly', $baseApp = 0)
    {
        if ($count == 20)
        {
            $mcObj = new Cache_Comment();
            $commentHot = $mcObj->getHotComment($count, $type);
            
            if ($commentHot)
            {
                return $commentHot;
            }
        }

        try
        {
            $commApi = Comm_Weibo_Api_Statuses::hotComments($type);
            $commApi->count = $count;
            
            if ($baseApp)
            {
                $commApi->base_app = $baseApp;
            }
    
            $commentHot = $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        if ($count == 20)
        {
            $re = $mcObj->createHotComment($count, $type, $commentHot);
            
            if (!$re)
            {
                $mcObj->createHotComment($count, $type, $commentHot);
            }
        }

        return $commentHot;
    }

    /**
     *
     * 批量取评论数据
     * 
     * @param array $cids            
     */
    public static function showBatch($cids = array(), $keyWord = "")
    {
        $commApi = Comm_Weibo_Api_Comments::showBatch();
        $cidList = implode(",", $cids);
        $commApi->cids = $cidList;

        try
        {
            $rtnCommentList = $commApi->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw new Dr_Exception($e);
        }

        $commentList = array();

        foreach ($rtnCommentList as $key => $comment)
        {
            try
            {
                if (!$comment['status']['mid'])
                {
                    continue;
                }

                $formatedComment = self::formatComment($comment, $keyWord);
            }
            catch (Exception $e)
            {
                continue;
            }

            $commentList[$key] = $formatedComment;
        }

        return $commentList;
        
    }
    
    /**
     *
     * @到我的评论
     * 
     * @param int $page            
     * @param int $count            
     * @param int $filterByAuthor
     *            0：所有用户、1：关注的人
     * @param int $filterBySource
     *            0：所有来源、1：来自微博的评论、2：来自微群的评论
     */
    public static function mentions($page = 1, $count = 20, $filterByAuthor = 0, $filterBySource = 0)
    {
        $commApi = Comm_Weibo_Api_Comments::mentions();
        $commApi->page = $page;
        $commApi->count = $count;

        if ($filterByAuthor)
        {
            $commApi->filter_by_author = $filterByAuthor;
        }

        if ($filterBySource)
        {
            $commApi->filter_by_source = $filterBySource;
        }

        try
        {
            $rtnCommentList = $commApi->getResult();
            return self::_mapping($rtnCommentList, $page);
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     *
     * at评论搜索
     * 
     * @param int $cuid            
     * @param int $uid @我的评论的某个用户
     * @param string $key            
     * @param int $contact 是否搜联系人，取值0或1，1 代表搜联系人，0 搜评论内容
     * @param int $type 搜索范围， 取值为0,1,2。0-我的全部评论，1-发给我的评论，2-我发给别人的评论
     * @param int $atme 是否是@我的评论，默认0-不是，1-是。
     * @param int $page 页码, 默认为1
     * @param int $num 获取数量, 默认为10，最大60
     * @param float $starttime 搜索范围起始时间，取值为时间戳
     * @param float $endtime 搜索范围结束时间，取值为时间戳
     * @param int $isred 是否标红，1:标红，0:不标红，默认：1
     * @throws Dr_Exception
     */
    public static function searchCommentsMentions($cuid, $key, $uid = 0, $contact = 0, $type = 0, $atme = 1, $page = 1, $num = 10, $startime = 0, $endtime = 0, $isred = 1)
    {
        $start = ($page - 1) * $num;
        $commApi = Comm_Weibo_Api_Search::commentsMentions();
        $key = self::_utf8ToGbk($key);
        $commApi->key = $key;
        $commApi->cuid = $cuid;

        if ($uid)
        {
            $commApi->uid = $uid;
        }

        $commApi->atme = $atme;
        $commApi->sid = 't_atme';
        $commApi->start = $start;
        $commApi->num = $num;
        $commApi->isred = $isred;

        if ($startime)
        {
            $commApi->startime = $startime;
        }

        if ($endtime)
        {
            $commApi->endtime = $endtime;
        }

        $commApi->type = $type;
        $commApi->contact = $contact;

        try
        {
            return $commApi->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     *
     * 评论搜索
     * 
     * @param int $cuid            
     * @param int $uid @我的评论的某个用户
     * @param string $key            
     * @param int $contact 是否搜联系人，取值0或1，1 代表搜联系人，0 搜评论内容
     * @param int $type 搜索范围， 取值为0,1,2。0-我的全部评论，1-发给我的评论，2-我发给别人的评论
     * @param int $atme 是否是@我的评论，默认0-不是，1-是。
     * @param int $page 页码, 默认为1
     * @param int $num 获取数量, 默认为10，最大60
     * @param float $starttime 搜索范围起始时间，取值为时间戳
     * @param float $endtime 搜索范围结束时间，取值为时间戳
     * @param int $isred 是否标红，1:标红，0:不标红，默认：1
     * @throws Dr_Exception
     */
    public static function searchComments($cuid, $key, $uid = 0, $contact = 0, $type = 0, $atme = 0, $page = 1, $num = 10, $startime = 0, $endtime = 0, $isred = 1)
    {
        $start = ($page - 1) * $num;
        $commApi = Comm_Weibo_Api_Search::commentsMentions();
        $key = self::_utf8ToGbk($key);
        $commApi->key = $key;
        $commApi->cuid = $cuid;

        if ($uid)
        {
            $commApi->uid = $uid;
        }

        $commApi->atme = $atme;
        $commApi->sid = 't_atme';
        $commApi->start = $start;
        $commApi->num = $num;
        $commApi->isred = $isred;

        if ($startime)
        {
            $commApi->startime = $startime;
        }

        if ($endtime)
        {
            $commApi->endtime = $endtime;
        }

        $commApi->type = $type;
        $commApi->contact = $contact;

        try
        {
            return $commApi->getResult();
        }
        catch (Comm_Exception_Program $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 字符串编码转换：UTF8->GBK
     * 
     * @param string $utf8str
     *            UTF-8编码的字符串
     * @return string GBK编码的字符串，转换出错时返回空字符串
     */
    private static function _utf8ToGbk($utf8Str)
    {
        return iconv('UTF-8', 'GBK//IGNORE', $utf8Str);
    }
    
    /**
     * 通过评论id获取微博id
     * 注：返回OpenApi ID
     *
     * @param string $cid
     *            评论id
     * @return int 评论不存在时，返回0
     */
    public static function getMidByCid($cid)
    {
        $cmts = Dr_Comment::showBatch(array($cid));
        return isset($cmts[0]['status']['id']) ? $cmts[0]['status']['id'] : 0;
    }
    
    /**
     * 获取评论用户隐私关系
     * 
     * @param int $uid 微博作者id
     * @return
     *
     */
    public static function getPrivacy($uid)
    {
        $cuser = Comm_Context::get('viewer');
        $isExtent = true;
        $context = 0;
        
        // 判断是否黑名单用户
        $isBlocked = Dr_Relation::blocksExists($uid, 1);
        if (intval($isBlocked) == 1)
        {
            $isExtent = false;
            $context = 1;
            return array(
                'is_extent' => $isExtent,
                'context' => $context,
            );
        }

        $ownerPrivacy = Dr_Account::getPrivacyBatch(array($uid));
        if ($ownerPrivacy[$uid]['comment'])
        {
            if ($ownerPrivacy[$uid]['comment'] == 1)
            {
                // 我关注的人
                $relation = Dr_Relation::checkRelation($cuser->id, $uid);
                
                if (!($relation == 1 || $relation == 2))
                { 
                    // 允许我的粉丝（关注了我）评论我的微博
                    $isExtent = false;
                    $context = 1;
                }
            }
            elseif ($ownerPrivacy[$uid]['comment'] == 2)
            {
                // 可信用户
                $isCan = false;
                $relation = Dr_Relation::checkRelation($cuser->id, $uid);
                
                if ($relation == 1 || $relation == 2)
                {
                    $isCan = true;
                }

                if ((boolean)$cuser->verified === true || $cuser->verified_type == 220)
                {
                    // 是v用户、达人
                    $isCan = true;
                }

                $re = Dr_Account::getMobile();
                if (false != $re && $re['number'] != "")
                {
                    // 是手机绑定用户
                    $isCan = true;
                }
                else
                {
                    $context = 2;
                }

                $position = Dr_Sass::retrieveVerify($cuser->id);
                if ($position == 1)
                {
                    // 0 未通过 , 1 通过, 2不需验证用户
                    $isCan = true;
                }

                if (!$isCan)
                {
                    // 所有条件都不满足时抛异常
                    $isExtent = false;
                }
            }
        }
        
        return array(
            'is_extent' => $isExtent,
            'context' => $context,
        );
    }
}
