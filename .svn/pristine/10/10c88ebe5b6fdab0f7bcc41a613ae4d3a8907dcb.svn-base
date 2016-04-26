<?php
/**
 * 微博Do对象
 *
 */
class Do_Status extends Do_Abstract
{
    protected $props = array(
        // ----共有的-----------
        'annotations' => '', // 元数据，主要是为了方便第三方应用记录一些适合于自己使用的信息。每条微博可以包含一个或者多个元数据。请以json字串的形式提交，字串长度不超过512个字符，具体内容可以自定。
                             
        // --------下行---------
        'idstr' => '', // 保留字段，请勿使用
        'id' => array(
            'int',
            'min,1;',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'created_at' => '',
        'text' => '',
        'source' => '',
        'favorited' => '',
        'truncated' => '',
        'geo' => '',
        'in_reply_to_status_id' => '',
        'in_reply_to_user_id' => '',
        'in_reply_to_screen_name' => '',
        'thumbnail_pic' => '',
        'bmiddle_pic' => '',
        'original_pic' => '',
        'user' => 'Do_User',
        'retweeted_status' => '',
        'repost_status_id' => '',
        'mid' => '', // 主站用的mid,
        'media' => '',
        'time' => '',
        'at_names' => '',
        'reposts_count' => '',
        'comments_count' => '',
        'media_list' => '',
        'sec_time' => '',
        
        // --------上行-------------
        'is_comment' => array(
            'enum',
            'enum,0,1,2,3',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT,
            1
        ),
        'pic_id' => '',
        'url' => array(
            'string',
            '',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'pic' => array(
            'string',
            '',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'show_geo' => '',
        'deleted' => '',
        'ssm_ext_log' => '',
        'mlevel' => array(
            'int',
            'min,0;',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'visible' => '',
        'mood' => '', // 發心情 同步发微博 记行为日志用
    );
    
    /**
     * 对个性数据进行单独处理
     * 
     * @param array $data            
     */
    public static function formatStatus($data, $keyWord = "")
    {
        // 对个性数据进行单独处理
        $rootMedia = false;
        $showGeo = false;

        // GMT时间转换成毫秒
        $createdAtTimestamp = strtotime($data['created_at']);
        $data['sec_time'] = $createdAtTimestamp;
        $data['time'] = $createdAtTimestamp . "000";
        $data['rawtext'] = $data['text'];
        $data['rawsource'] = $data['source'];
        $data['rawcreated_at'] = $data['created_at'];
        
        if (isset($data['deleted']) && $data['deleted'] == 1)
        {
            // 微博已经被删除
            return $data;
        }

        $ssmExtLog = array();
        $mid = $data['id'];

        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case 'text':
                    $uid = isset($data['uid']) ? $data['uid'] : $data['user']['id'];
                    $value = htmlspecialchars($value);
                    $hasRoot = (isset($data['retweeted_status']) || isset($data['retweeted_status_id'])) ? true : false;
                    $rtn = self::formatContent($uid, $value, 0, $hasRoot, $mid);
                    $value = $rtn['content'];
                    
                    if ($keyWord)
                    {
                        $value = Tool_Formatter_String::redTag($value, $keyWord);
                    }

                    $ssmExtLog = array_merge($ssmExtLog, $rtn['ssm_ext_log']);
                    $media = $rtn['media'];
                    $atNames = $rtn['at_names'];
                    break;

                case 'created_at':
                    $value = $createdAtTimestamp;
                    break;

                case 'retweeted_status':
                    if (isset($value['text']) && isset($value['user']))
                    {
                        $uid = isset($value['uid']) ? $value['uid'] : $value['user']['id'];
                        $value['user'] = Dr_User::formatUserInfo($value['user']);
                        $value['text'] = htmlspecialchars($value['text']);
                        $retMid = $data['retweeted_status']['id'];
                        $rtn = self::formatContent($uid, $value['text'], 1, 1, $retMid);
                        $value['text'] = $rtn['content'];
                        
                        if ($keyWord)
                        {
                            $value['text'] = Tool_Formatter_String::redTag($value['text'], $keyWord);
                        }

                        $value['media'] = $rtn['media'];
                        $value['at_names'] = $rtn['at_names'];
                        
                        if (count($value['media']))
                        {
                            $rootMedia = true;
                        }

                        $annotations = isset($value['annotations']) ? $value['annotations'] : null;
                        $value['source'] = Tool_Analyze_Source::formatSource($value['source'], $annotations);
                    }

                    break;

                case 'geo' :
                    if (empty($value))
                    {
                        break;
                    }

                    // 经纬度未做转换
                    if (!isset($value['name']))
                    {
                        if ($value['coordinates'][0] && $value['coordinates'][1])
                        {
                            $fullname = array();
                            try
                            {
                                $fullname = Dr_Status::getGeo($value['coordinates'][0], $value['coordinates'][1]);
                            }
                            catch (Dr_Exception $e)
                            {
                                break;
                            }

                            if (isset($fullname ['g0']) && is_array($fullname ['g0']))
                            {
                                $value['name'] = isset($fullname['g0']['address']) && !empty($fullname['g0']['address']) ? $fullname['g0']['address'] : '';
                            }

                            if (empty($value['name']))
                            {
                                $value['name'] = Comm_I18n::get('未知地址');
                            }
                        }
                    }
                    
                    $showGeo = (!empty($value['name']));

                    break;

                case 'source' :
                    $annotations = isset($data['annotations']) ? $data['annotations'] : null;
                    $value = Tool_Analyze_Source::formatSource($value, $annotations);
                    break;

                // 防止pic_id被xss注入攻击
                case 'thumbnail_pic' :
                case 'bmiddle_pic' :
                case 'original_pic' :
                    $value = htmlspecialchars($value, ENT_QUOTES);
                    break;

                case 'user' :
                    $value = Dr_User::formatUserInfo($value);
                    break;

                default :
                    break;
            }

            $data[$key] = $value;
        }
        
        $data['show_geo'] = $showGeo;

        if (!isset($data['visible']) || empty($data['visible']))
        {
            $data['visible'] = array('type' => 0);
        }
        
        if ($rootMedia == false)
        {
            $data['media'] = $media;
        }
        
        if ($atNames)
        {
            $data['at_names'] = $atNames;
        }

        $data['ssm_ext_log'] = $ssmExtLog;
        
        return $data;
    }
    
    /**
     *
     *
     * 对微博时间做转换
     * 
     * @param string $time            
     */
    protected function formatTime($time)
    {
        return Tool_Formatter_Time::timeFormat($time);
    }
    
    /**
     * 渲染微博数据
     * 
     * @param string $content            
     */
    public static function formatContent($uid, $content, $isForward = 0, $hasRoot = 0, $mid = 0)
    {
        $ssmExtLog = array();
        
        // Warning: 渲染顺序必须为 link=>@user=>tag=>emotion， 由于link、 user 、 tag 之类的解析
        // 会发生冲突，产品确定了以上顺序
        $content = self::formatTag($content);
        
        // link信息，将除了link以外的字符都认为是纯文本内容，做了
        // htmlspecialchars($content, ENT_NOQUOTES) 处理，以防止后续tag提取发生冲突。
        $rtn = Tool_Analyze_Link::parseLinkToHtml($content, $isForward, $hasRoot, true, $mid);
        $content = $rtn['content'];
        $media = $rtn['media'];
        
        // @信息
        $toolsAnalyzeAt = new Tool_Analyze_At();
        $atNames = $toolsAnalyzeAt->getAtUsername($content);
        $content = $toolsAnalyzeAt->replaceWeihaoToNick($content, $atNames);
        if (count($atNames))
        {
            $toolsAnalyzeAt->atToLink($content, $atNames);
        }

        // tag信息
        $content = Tool_Analyze_Tag::renderTag($content);
        
        $content = Tool_Analyze_Tag::userTagToLink($content);
        // 表情
        $content = Tool_Analyze_Icon::textToIcon($content);
        
        // TODO 用户标签转化为连接
        // 行为日志处理
        if (count($atNames))
        {
            $ssmExtLog['atusers'] = implode(',', $atNames);
        }

        if (preg_match("#a_topic#", $content))
        {
            $ssmExtLog['isTopic'] = 1;
        }

        if (preg_match("#face#", $content))
        {
            $ssmExtLog['face_normal'] = 1;
        }

        return array(
            'content' => $content,
            'media' => $media,
            'at_names' => $atNames,
            'ssm_ext_log' => $ssmExtLog
        );
    }

    public function hasGeo()
    {
        return !empty($this->geo);
    }
    
    /**
     * 判断用户上传的pid是否合法
     * 
     * @param unknown_type $picId            
     */
    public static function checkPicId($picId)
    {
        try
        {
            $resPic = Tool_Picid2url::getPicurl($picId);
            return isset($resPic[$picId]);
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    
    /**
     * 检测geo 是否正常
     * 
     * @param unknown_type $value            
     */
    public static function checkGeo($value)
    {
        return empty($value) || (is_string($value['type']) && is_array($value['coordinates']));
    }
    
    /**
     * 检测是否有pic
     */
    public function hasPic()
    {
        return empty($this->pic_id) ? false : true;
    }
    
    /**
     * 检测是否有pic
     * Enter description here .
     * ..
     */
    public function hasPicUrl()
    {
        return empty($this->url) ? false : true;
    }
    
    /**
     * 检测是否是评论转发
     */
    public function hasReply()
    {
        return empty($this->repost_status_id) ? false : true;
    }
    
    /**
     * v3推荐的标签目前的内容形式为<a href="/pub/tags/xxx">xxx</a>
     *
     * 需要将该形式格式化成[TAG]xxx[TAG]，这样同时解决v3和v4标签搜索地址不同的问题
     *
     * @param string $text            
     * @return mixed
     */
    public static function formatTag($text)
    {
        return preg_replace('#&lt;a(?:\s+)href=&quot;(?:http:\/\/weibo.com)?/pub/tags/(?:.+?)&quot;&gt;(.+?)&lt;/a&gt;#iu', '[TAG]\\1[TAG]', $text);
    }
}
