<?php

class Dr_Message extends Dr_Abstract
{
    private static $_shortUrlDomains = array("t.cn" => 1, "sinaurl.cn" => 1);

    /**
     * 
     * 获取某个用户最新的私信列表
     */
    public static function getNewList($count = 20, $page = 1, $sinceId = null, $maxId = null)
    {
        $apiObj = Comm_Weibo_Api_Messages::getNewList();
        $apiObj->since_id = $sinceId;
        $apiObj->max_id = $maxId;
        $apiObj->count = $count;
        $apiObj->page = $page;
        $messageLists = $apiObj->getResult();
        
        $reArr = array();
        foreach ($messageLists['direct_messages'] as $k => $v)
        {
            $reArr[$k]['sender'] = new Do_User($v['sender'], Do_Abstract::MODE_OUTPUT);
            $reArr[$k]['recipient'] = new Do_User($v['recipient'], Do_Abstract::MODE_OUTPUT);
            unset($v['sender'], $v['recipient']);
            $reArr[$k]['message'] = new Do_Message($v, Do_Abstract::MODE_OUTPUT);
            $reArr[$k]['message'] = $v;
        }

        unset($messageLists);
        return $reArr;
    }

    /**
     *  获取某个用户最新的私信列表  增加按照时间进行筛选的策略
     * @param number $count
     * @param number $page
     * @param string $since_id
     * @param string $max_id
     * @param long from
     * @param long from
     * @return Ambigous <multitype:, boolean, number, multitype:unknown , multitype:Ambigous <multitype:null Ambigous <string, unknown> , multitype:, multitype:number string null unknown multitype:string multitype:null   multitype:multitype:null   mixed Ambigous <number, unknown> > , string, multitype:multitype:number unknown string multitype:unknown multitype:unknown   boolean Ambigous <number, unknown> Ambigous <string, mixed, unknown, multitype:Ambigous <mixed, unknown, string> >  >
     */
    public static function getNewList2($count = 20, $page = 1, $sinceId = null, $maxId = null, $from = null, $end = null)
    {
        try 
        {
            $apiObj = Comm_Weibo_Api_Messages::getNewList2();
            $apiObj->since_id = $sinceId;
            $apiObj->max_id = $maxId;
            $apiObj->from = $from;
            $apiObj->end = $end;
            $apiObj->count = $count;
            $apiObj->page = $page;

            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取当前用户收到的所有私信，包括单发私信、单发留言、群发私信
     * @param int $begin [可选] 开始时间戳，若指定此参数， 则返回该时间点及时间点之后的私信
     * @param int $end [可选] 结束时间戳，若指定此参数， 则返回该时间点及时间点之前的私信
     * @param int $cursor [可选] 上一页的最后一条私信ID，默认值为0（表示取第一页数据）
     * @param int $count [可选] 返回结果的条数数量，最大值200，默认值20
     * @param int $encoded [可选] 返回结果是否转义，0：不转义，1：转义，默认值为1（需要转义的符号及转义规则见相关约束）
     * @return array
     * @throws Dr_Exception
     */
    public static function getReceiveList($begin, $end, $cursor = 0, $count = 20, $encoded = 1)
    {
        try 
        {
            $apiObj = Comm_Weibo_Api_Messages::getReceiveList();
            $apiObj->setValue('since', $begin);
            $apiObj->setValue('end', $end);
            $apiObj->setValue('cursor', $cursor);
            $apiObj->setValue('count', $count);
            $apiObj->setValue('is_encoded', $encoded);
            
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取当前用户与某用户的所有会话，包括单发私信、单发留言、群发私信
     * @param int $guestUid [必选] 指定用户的uid
     * @param int $count [可选] 返回结果的条数数量，最大值200，默认值20
     * @param int $cursor [可选] 上一页的最后一条私信ID，默认值为0（表示取第一页数据）
     * @param int $encoded [可选] 返回结果是否转义，0：不转义，1：转义，默认值为1（需要转义的符号及转义规则见相关约束）
     * @return array
     * @throws Dr_Exception
     */
    public static function getConversationList($guestUid, $count = 20, $cursor = 0, $encoded = 1)
    {
        try 
        {
            $apiObj = Comm_Weibo_Api_Messages::getConversationList();
            $apiObj->setValue('uid', $guestUid);
            $apiObj->setValue('cursor', $cursor);
            $apiObj->setValue('count', $count);
            $apiObj->setValue('is_encoded', $encoded);
            
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取当前用户的私信搜索结果
     * @param string $key [必选] 查询字符串，必须采用UTF-8编码，并做URLEncode
     * @param int64 $uid [必选] 当前登录者uid
     * @param string $sid [必选] 搜索sid标识,由搜索部分配
     * @param int $cursor [可选] 起始位置, 默认值：0
     * @param int $count [可选] 获取数量, 默认值：10，最大值：60
     * @param int $isred [可选] 关键词是否标红，1:标红，0:不标红，默认值：1
     * @param int64 $startTime [可选] 搜索范围起始时间戳
     * @param int64 $endTime [可选] 搜索范围结束时间戳
     * @param int $type [可选] 搜索类型 取值：0,1,2，0：我的全部私信，1：发给我的私信，2：我发给别人的私信 默认值：0
     * @param int $contact [可选] 是否搜联系人 取值0,1，0：搜索信内容，1：搜索联系人 默认值：0
     * @return array
     * @throws Dr_Exception
     */
    public static function getSearchedMsgList($key = '', $uid = 0, $sid = 't_messages', $cursor = 0, $count = 20, $isred = 1, $startTime = 0, $endTime = 0, $type = 0, $contact = 0)
    {
        try 
        {
            $apiObj = Comm_Weibo_Api_Messages::getSearchedMsgList();
            $apiObj->setValue('key', $key);
            $apiObj->setValue('cuid', $uid);
            $apiObj->setValue('sid', $sid);
            $apiObj->setValue('start', $cursor);
            $apiObj->setValue('num', $count);
            $apiObj->setValue('isred', $isred);
            $apiObj->setValue('startime', $startTime);
            $apiObj->setValue('endtime', $endTime);
            $apiObj->setValue('type', $type);
            $apiObj->setValue('contact', $contact);
            
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取当前用户发送的最新私信列表
     */
    public static function sendList($count = 20, $page = 1, $sinceId = null, $maxId = null)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::sendList();
            $apiObj->since_id = $sinceId;
            $apiObj->max_id = $maxId;
            $apiObj->count = $count;
            $apiObj->page = $page;
            $messageLists = $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        $reArr = array();
        foreach ($messageLists['direct_messages'] as $k => $v)
        {
            $reArr[$k]['sender'] = new Do_User($v['sender'], Do_Abstract::MODE_OUTPUT);
            $reArr[$k]['recipient'] = new Do_User($v['recipient'], Do_Abstract::MODE_OUTPUT);
            unset($v['sender'], $v['recipient']);
            $reArr[$k]['message'] = new Do_Message($v, Do_Abstract::MODE_OUTPUT);
        }

        unset($messageLists);
        return $reArr;
    }
    
    /**
     * 获取与当前登录用户有私信往来的用户列表
     */
    public static function userList($count = 20, $cursor = 0)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::userList();
            $apiObj->count = $count;
            $apiObj->cursor = $cursor;
            $returnLists = $apiObj->getResult();

            if (1 == rand(1, 3) && $count == 20 && $cursor == 0)
            {
                $mcRz = new Cache_Message();
                $cuser = Comm_Context::get("viewer");
                $mcRz->createUserlist($cuser->id, $returnLists);
            }

            return $returnLists;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            $mcRz = new Cache_Message();
            $cuser = Comm_Context::get("viewer");
            $returnLists = $mcRz->getUserlist($cuser->id);
            
            if (false !== $returnLists)
            {
                return $returnLists;
            }

            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 
     * 获取与指定用户的往来私信列表
     */
    public static function historyList($uid, $count = 20, $page = 1, $sinceId = null, $maxId = null)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::conversation();
            $apiObj->uid = $uid;
            $apiObj->count = $count;
            $apiObj->page = $page;

            if (!is_null($sinceId))
            {
                $apiObj->since_id = $sinceId;
            }

            if (!is_null($maxId))
            {
                $apiObj->max_id = $maxId;
            }

            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    public static function formatOnlineStatus($onelineStatus)
    {
        //todo 目前状态未定
        $statusFromat = array(
            0 => "offline",
            1 => "online",
            2 => "busy",
            3 => "away",            
        );

        return isset($statusFromat[$onelineStatus]) ? $statusFromat[$onelineStatus] : 'offline';
    }
    
    /**
     * messageSearch
     * @param string $key_word
     * @param int64 $cuid
     * @param int $contact 0 搜内容 1搜人
     * @param int $start
     * @param int $num
     * @param int $isred
     * @param int $type
     */
    public static function messageSearch($keyWord, $cuid = 0, $contact = 0, $start = 0, $num = 20, $isred = 1, $type = 0)
    {
        $rtn = array();
        try
        {
            $apiObj = Comm_Weibo_Api_Search::directMessages();            
            $apiObj->key = $keyWord;
            $apiObj->cuid = $cuid;
            $apiObj->sid = 't_messages';
            $apiObj->contact = $contact;
            $apiObj->start = $start;
            $apiObj->num = $num;
            $apiObj->isred = $isred;
            $apiObj->type = $type;

            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 
     * 这个函数用于专门的搜人
     * 
     * @param string $key_word
     * @param int64 $cuid
     * @param int $contact 0 搜内容  1搜人
     * @param int $start
     * @param int $num
     * @param int $isred
     * @param int $type
     */
    public static function contactSearch($keyWord, $cuid = 0, $contact = 1, $start = 0, $num = 1, $isred = 0, $type = 0)
    {
        $reArr = array();
        try
        {
            $apiObj = Comm_Weibo_Api_Search::directMessages();            
            $apiObj->key = $keyWord;
            $apiObj->cuid = $cuid;
            $apiObj->sid = 't_messages';
            $apiObj->contact = $contact;
            $apiObj->start = $start;
            $apiObj->num = $num;
            $apiObj->isred = $isred;
            $apiObj->type = $type;            
            $rtn = $apiObj->getResult(true);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        //返回值的样式 array(0=>id,1=>name)
        if ($rtn[0] == 0)
        {
            $reArr["total_number"] = 0;
        }
        else
        {
            $reArr["total_number"] = 1;
            $reArr["uid"] = $rtn[0];
            $reArr["screen_name"] = $rtn[1];
        }

        return $reArr;        
    }
    
    /**
     * 批量获取私信内容
     * Enter description here ...
     * @param unknown_type $dmids
     * @throws Comm_Weibo_Exception_Api
     */
    public static function showBatch($dmids)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::showBatch();
            $apiObj->dmids = $dmids;

            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 
     *合并私信对话 
     */
    private static function _mergeMessage($array)
    {
        if (empty($array['direct_messages']))
        {
            return array(
                'list' => array(), 
                'total_number' => 0, 
                'next_cursor' => 0, 
                'previous_cursor' => 0,
                'remark' => '', 
                'online_status' => '', 
                'report_id' => '',
            );
        }

        $messageLists = $array['direct_messages'];

        //找出fid,并获取备注信息
        $doUser = Comm_Context::get('viewer');
        $uid = $doUser->id;
        if ($uid == $messageLists[0]['sender_id'])
        {
            $fid = $messageLists[0]['recipient_id'];
            $onlineStatus = self::formatOnlineStatus($messageLists[0]['recipient']['online_status']);
        }
        else
        {
            $fid = $messageLists[0]['sender_id'];
            $onlineStatus = self::formatOnlineStatus($messageLists[0]['sender']['online_status']);
        }

        $friendsRemark = Dr_Relation::friendsRemarkBatch(array($fid));
        $remark = isset($friendsRemark[$fid]) && !empty($friendsRemark[$fid]) ? '('.$friendsRemark[$fid].')' : '';
        $data = array();
        $times = array();
        $flag = 0;
        $reportId = 0;
      
        foreach ($messageLists as $k => $v)
        {
            $messageLists[$k]['sender'] = new Do_User($v['sender'], Do_Abstract::MODE_OUTPUT);
            $messageLists[$k]['created_at'] = Tool_Formatter_Time::timeFormat(strtotime($v['created_at']));
            $messageLists[$k]['created_at_m'] = strtotime($v['created_at']);
            
            //如有推荐,把<a>标签替换了
            $text = self::_replaceRecommend($v["text"]);
            $v["text"] = htmlspecialchars($text['content'], ENT_QUOTES);
            $content = Do_Status::formatContent($v["sender_id"], $v["text"], 0, 0);
            
            //把之前替换的<a>标签内容替换回来
            if (isset($text['matches']))
            {
                $messageLists[$k]['text'] = self::_returnRecommend($content['content'], $text['matches']);
            }
            else
            {
                $messageLists[$k]['text'] = $content['content'];
            }

            if ($flag != 1 && $uid != $v['sender_id'])
            {
                $reportId = $v['id'];
                $flag = 1;
            }
            
            if (isset($v['status_id']) && $v['status_id'] > 0)
            {
                $mblog = self::formatContent($v['status_id']);
                
                if (count($mblog) > 0)
                {
                    $messageLists[$k]['status_text'] = $mblog['content'];
                    $messageLists[$k]['status_url'] = $mblog['url'];
                }
            }
            
            $is_send = ($uid == $v['sender_id']) ? 1 : 0;
            
            if (isset($v['att_ids']) && !empty($v['att_ids']))
            {
                $finfo = self::_formatFile($v['att_ids'], $isSend);

                if (!empty($finfo))
                {
                    $messageLists[$k]['file_info'] = $finfo;
                }
            }
            
            if (empty($data))
            {
                $data[$k][0] = $messageLists[$k];
                $key = $k;
            }
            elseif ($v['sender_id'] == $messageLists[$k - 1]['sender_id'])
            {
                $start = count($times) == 0 ? $messageLists[$k - 1]['created_at_m'] : $times[0];

                if ($start - strtotime($v['created_at']) < 1860)
                {
                    array_push($data[$key], $messageLists[$k]);
                    array_push($times,$messageLists[$k - 1]['created_at_m']);
                }
                else
                {
                    $key = $key + 1;
                    $data[$key][0] = $messageLists[$k];
                    $times = array();
                }
            }
            else
            {
                $key = $key + 1;
                $data[$key][0] = $messageLists[$k];
                $times = array();
            }
        }

        $data = self::addLine($data);

        return array(
            'list' => $data, 
            'total_number' => $array['total_number'], 
            'next_cursor' => $array['next_cursor'], 
            'previous_cursor' => $array['previous_cursor'], 
            'remark' => $remark, 
            'online_status' => $onlineStatus,
            'report_id' => $reportId,
        );
    }
     
    /**
     * 
     * 对转发微博数据进行处理 
     */
    public static function formatContent($statusId)
    {
        try
        {   
            $commApi = Comm_Weibo_Api_Statuses::show();
            $commApi->id = $statusId;
            $info = $commApi->getResult();

            if (isset($info['text']) === false || $info['text'] == '')
            {
                return false;
            }

            $content = $info['text'];
            $grep = "!http\:\/\/[a-zA-Z0-9\$\-\_\.\+\!\*\'\,\{\}\\\^\~\]\`\>\%\>\/\?\:\@\&\=(\&amp\;)\#\|]+!is";    
            preg_match_all($grep, $content, $out);
            if (count($out[0]) > 0)
            {
                $urlShort = array();

                foreach ($out[0] as $k => $v)
                {
                    $urlArray = parse_url($v);
                    if (isset(self::$_shortUrlDomains[strtolower(trim($urlArray['host']))]))
                    {
                        $strinShortUrl = trim($urlArray['path'], "/");
                        
                        if (empty($strinShortUrl))
                        {
                            continue;
                        }

                        $urlShort[] = $strinShortUrl;
                    }
                }

                if (count($urlShort) > 0)
                {
                    try
                    {
                        $api = Comm_Weibo_Api_Shorturl::batchInfo();
                        $api->url_short = $urlShort[0];
                        $urlLong = $api->getResult();
                        if ($urlLong)
                        {
                            $img = self::_getUrlImg($urlLong);
                        }
                    }
                    catch (Comm_Weibo_Exception_Api $e)
                    {
                        throw new Dr_Exception($e);
                    }
                }
            }

            $content = self::_cutContent($content);
            $content = htmlspecialchars($content, ENT_QUOTES);
            $content = Do_Status::formatContent($info['user']['id'], $content, 0, 0);
            $content = preg_replace('/<span class=\"feedico_(vedio|music)\"><\/span>/', '', $content['content']);
            
            if (isset($img))
            {
                $content = $content.$img;
            }
            elseif (isset($info['thumbnail_pic']))
            {
                $content = $content . '<span class="feedico_img"></span>';
            }

            $statusMid = Comm_Weibo_MIDConverter::from10to62($info['mid']);
            $statusUrl = $info['user']['id'] . '/' . $statusMid;
            $res = array();
            $res['content'] = $content;
            $res['url'] = $statusUrl;
            
            return $res; 
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }  
    }

    /**
     * 
     * 返回转发微博短链图标($urlLong)
     * 只返回第一次出现的短链图标
     */
    private static function _getUrlImg($urlLong)
    {
        //TODO
        foreach ($urlLong as $k => $item)
        {
            switch ($item['type'])
            {
                case Tool_Analyze_Link::SHORTURL_TYPE_VIDEO:
                    $img = '<span class="feedico_vedio"></span>';
                    $flag = true;
                    break;

                case Tool_Analyze_Link::SHORTURL_TYPE_MP3:
                    $img = '<span class="feedico_music"></span>';
                    $flag = true;
                    break;

                case Tool_Analyze_Link::SHORTURL_TYPE_MAGIC:
                    $img = '<span class="feedico_magic"></span>';
                    $flag = true;
                    break;

                case Tool_Analyze_Link::SHORTURL_TYPE_VOTE:
                    $img = '<span class="feedico_vote"></span>';
                    $flag = true;
                    break;

                default:
                    $img = '';
                    $flag = false;
            }

            if ($flag == true)
            {
                break;
            }
        }

        return $img; 
    }

    /**
     * 
     * 截取转发微博字数, 只显示前15个汉字 
     */
    private static function _cutContent($content)
    {
        $grepStr = "/[\x80-\xff]./";
        $grepUrl = "!http\:\/\/[a-zA-Z0-9\$\-\_\.\+\!\*\'\,\{\}\\\^\~\]\`\>\%\>\/\?\:\@\&\=(\&amp\;)\#\|]+!is";        
        if (preg_match($grepUrl, $content) && preg_match($grepStr, $content))
        {
            $text = preg_replace($grepUrl, '', $content);
            $text = Tool_Formatter_String::substrCn2($text, 33);
        }
        elseif (preg_match($grepStr, $content) && !preg_match($grepUrl, $content))
        {
            $text = $content;
            $text = Tool_Formatter_String::substrCn2($text, 33);
        }
        else
        {
            $text = $content;
        }

        return $text;
    }

    /**
     * 
     * 根据类型决定取数组哪半部分
     * @param array $array
     * @param int $type
     */
    private static function _getHalfArray($array, $type = 1)
    {
        $count = count($array);
        if ($count < 1)
        {
            return $array;
        }

        if ($type == 1)
        {
            return array_slice($array, 0, floor($count / 2));
        }
        else 
        {
            return array_slice($array, -floor($count / 2));
        }
    }
    
    /**
     * 
     * 根据附件id获取附件信息 
     */
    private static function _formatFile($fids, $isSend = 1)
    {        
        $arrayFid = self::_getHalfArray($fids, $isSend);
        $fInfo = array();
        $arrayIcon = array(
            "csv"  => "csv",
            "doc"  => "word",
            "docx" => "word",
            "xls"  => "excel",
            "xlsx" => "excel",
            "pdf"  => "pdf",        
            "rar"  => "rar",
            "txt"  => "txt",
            "zip"  => "rar",
            "ppt"  => "ppt",
            "pptx" => "ppt",
            "mp3"  => "music",
            "avi"  => "video",
            "flv"  => "video",
            "mkv"  => "video",
            "mp4"  => "video",
            "mpeg" => "video",
            "mpg2" => "video",
            "rmvb" => "video",
        );

        foreach ($arrayFid as $fid)
        {
            try
            {
                $info = Dr_File::attachmentInfo($fid);
            }
            catch (Comm_Weibo_Exception_Api $e)
            {
                continue;
            }

            if (!empty($info))
            {
                $fileName = $info["name"];
                if (trim($fileName) == "")
                {
                    continue;
                }

                $arrayFilename = explode(".", $fileName);
                $fileExt = $arrayFilename[count($arrayFilename) - 1];
                $fileExt = strtolower($fileExt);
                $fileIcon = isset($arrayIcon[$fileExt]) ? $arrayIcon[$fileExt] : 'default';

                $info["file_icon"] = $fileIcon;
                $info["size"] = ceil($info["size"] / 1024);            
                $fInfo[] = $info;
            }            
        }

        return $fInfo;
    }

    private static function _replaceRecommend($str)
    {
        $grepS = '/<a href="((http\:\/\/weibo\.com\/f\/recommend\/received\?recid=\d+&touid=\d+)|(http\:\/\/weibo\.com\/recommend\/myrecommend\.php\?recid=\d+&touid=\d+)|(http\:\/\/t\.sina\.com\.cn\/recommend\/myrecommend\.php\?recid=\d+&touid=\d+))">/';
        $grepE = "/\<\/a>/";
        $res = array();
        
        if (preg_match($grepS, $str, $out))
        {
            $str = preg_replace($grepS, 'L%', $str);
            $str = preg_replace($grepE, 'R%', $str);
            $res['matches'] = $out[0];
        }

        $res['content'] = $str;

        return $res;
    }
    
    public static function replaceRecommend($str)
    {
        return self::_replaceRecommend($str);
    }
    
    private static function _returnRecommend($str, $matches)
    {
        if (strpos($str, 'L%'))
        {
            $str = str_replace('L%', $matches, $str);
        }

        if (strpos($str, 'R%'))
        {
            $str = str_replace('R%', '</a>', $str);
        }
        
        return $str;
    }
    
    /**
     * 
     * 对话泡泡相差半小时以上，需要加横线，所以重新遍历打标签 
     */
    protected function addLine($data)
    {
        for ($i = 0; $i < count($data) - 1; $i++)
        {
            $keys = count($data[$i]);
            if ($data[$i][$keys - 1]['created_at_m'] - $data[$i + 1][0]['created_at_m'] > 1860)
            {
                $data[$i]['is_line'] = 1;
            }
        }

        return $data;
    }

    /**
     * 判断是否给uid的人发私信
     * 
     * @param int64 $uid
     * @throws Comm_Weibo_Exception_Api
     */
    public static function messageIsCapable($uid, $type = false)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::isCapable();
            
            if ($type == 'keyword')
            {
                $user = 'weibokeywordmonitor@gmail.com';
                $pwd = '1q2w3e4r5t';
                $apiObj->addUserpsw($user, $pwd);
            }                

            $apiObj->uid = $uid;
            $return = $apiObj->getResult();

            return (boolean)$return["result"];          
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 批量获取对话私信总数和未读数。
     * @param string $uids 指定用户的ID。一次最多50个，逗号分隔
     * @param string $type 获取计数类型。1获取会话总数，2获取会话未读数，3同时获取会话总数和未读数。 
     * 
     * @return array $countList
     */
    public static function countBatch($uids, $type = 3)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::countBatch();
            $apiObj->uids = $uids;
            $apiObj->type = $type;
            
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}
