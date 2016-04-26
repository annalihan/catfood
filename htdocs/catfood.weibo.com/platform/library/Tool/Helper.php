<?php
/**
 * 視圖渲染時的數據轉換
 */
class Tool_Helper
{
    protected static $jsVersion = '';
    protected static $jsDomain = '';
    protected static $cssVersion = '';
    protected static $cssDomainPool = '';
    protected static $cssDomainPoolV5 = '';
    protected static $imgVersion = '';
    protected static $imgDomain = '';

    CONST ICON_TYPE_A = 4; // 加载身份icon,增值icon，合作icon和运营icon
    CONST ICON_TYPE_B = 3; // 加载身份icon，增值icon,和合作icon
    CONST ICON_TYPE_C = 2; // 加载身份icon,增值icon

    public static function trimPicId($picUrl)
    {
        $filename = basename($picUrl);

        return array_shift(explode('.', $filename));
    }

    public static function shortString($string, $len, $etc = '', $isDecode = '')
    {
        if (mb_strwidth($string) > $len)
        {
            if ($isDecode)
            {
                $string = htmlspecialchars_decode($string);
            }

            $string = mb_strimwidth($string, 0, $len, $etc, "utf8");
            $string = $isDecode ? htmlspecialchars($string) : $string;
        }

        return $string;
    }

    public static function reportUrl($params)
    {
        $wvr = Dr_User::getUserVersion();
        $queryStr = '';

        if (isset($params['rid']))
        {
            $queryStr .= "rid={$params['rid']}&";
        }

        if (isset($params['from']))
        {
            $queryStr .= "from={$params['from']}&";
        }

        if (isset($params['type']))
        {
            $queryStr .= "type={$params['type']}";
        }

        if (isset($params['location']) && $params['location'] != '')
        {
            $location = Comm_Config::get('reportspam.location');
            if (isset($location[$params['location']]))
            {
                $queryStr .= "&from={$location[$params['location']]}";
            }
        }

        $reportUrl = urlencode(str_replace("http://new.weibo.com", "http://weibo.com", $_SERVER['SCRIPT_URI']));
        $newUrl = array(Comm_Config::get('reportspam.report_mblog'), Comm_Config::get('reportspam.report_user'), Comm_Config::get('reportspam.report_vip'), Comm_Config::get('reportspam.report_comment'));
        $scrollbars = 'no';
        if (in_array($params['type'], $newUrl))
        {
            $url = Comm_Config::get('reportspam.url_new') . "?" . $queryStr . "&url={$reportUrl}&bottomnav=1&wvr=$wvr";
            $scrollbars = 'yes';
        }
        else
        {
            $url = Comm_Config::get('reportspam.url') . "?" . $queryStr . "&url={$reportUrl}&bottomnav=1&wvr=$wvr";
        }

        $re = "javascript:window.open('{$url}', 'newwindow', 'height=700, width=550, toolbar =yes, menubar=no, scrollbars={$scrollbars}, resizable=yes, location=no, status=no');";
        return $re;
    }

    public static function vcss($href, $params = array())
    {
        $params['href'] = $href;
        if (!self::$cssVersion)
        {
            self::$cssVersion = Tool_Misc::homesiteCssVersion();
        }

        self::_getDomain(self::$cssDomainPool, 'env.css_domain_pool');
        
        $cssDomain = self::_getRandomDomain(self::$cssDomainPool);
        
        self::_formatUrl($params, 'href', self::$cssVersion, $cssDomain);
        
        $params = self::_mergeDefaultValue($params, array("type" => "text/css", "rel" => "stylesheet", "charset" => 'utf-8'));
        $props = self::_renderTagProp($params);
        
        echo "<link {$props}/>";
    }

    public static function vcssV5($href, $params = array())
    {
        $params['href'] = $href;
        if (!self::$cssVersion)
        {
            self::$cssVersion = Tool_Misc::homesiteCssVersion();
        }

        if (!self::$cssDomainPoolV5)
        {
            self::$cssDomainPoolV5 = Comm_Config::get('env.css_domain_pool_v5');
        }

        $cssDomain = self::_getRandomDomain(self::$cssDomainPoolV5);
        
        self::_formatUrl($params, 'href', self::$cssVersion, $cssDomain);
        
        $params = self::_mergeDefaultValue($params, array("type" => "text/css", "rel" => "stylesheet", "charset" => 'utf-8'));
        $props = self::_renderTagProp($params);
        
        echo "<link {$props}/>";
    }

    public static function vjs($src, $params = array())
    {
        $params['src'] = $src;
        if (!self::$jsVersion)
        {
            self::$jsVersion = Tool_Misc::homesiteJsVersion();
        }

        self::_getDomain(self::$jsDomain, 'env.js_domain');

        self::_formatUrl($params, 'src', self::$jsVersion, self::$jsDomain);
        
        $paramStr = self::_renderTagProp(self::_mergeDefaultValue($params, array('type' => 'text/javascript')));
        
        return "<script {$paramStr}></script>";
    }
    
    /*
     * v5 新加
     */
    public static function vjsV5($src, $params = array())
    {
        $src = Tool_Multirelease::jsVersionReplace($src);

        $params['src'] = $src;
        if (!self::$jsVersion)
        {
            self::$jsVersion = Tool_Misc::homesiteJsVersion();
        }

        if (!self::$jsDomain)
        {
            self::$jsDomain = Comm_Config::get('env.js_domain_v5');
        }
        
        self::_formatUrl($params, 'src', self::$jsVersion, self::$jsDomain);
        
        $paramStr = self::_renderTagProp(self::_mergeDefaultValue($params, array('type' => 'text/javascript')));
        
        return "<script {$paramStr}></script>";
    }

    public static function vimg($src, $params = array())
    {
        $params['src'] = $src;
        if (!self::$imgVersion)
        {
            self::$imgVersion = Tool_Misc::homesiteCssVersion();
        }

        self::_getDomain(self::$imgDomain, 'env.img_domain');
        
        self::_formatUrl($params, 'src', self::$imgVersion, self::$imgDomain);
        
        $paramStr = self::_renderTagProp($params);
        
        return "<img {$paramStr}/>";
    }

    public static function vimgV5($src, $params = array())
    {
        $params['src'] = $src;
        if (!self::$imgVersion)
        {
            self::$imgVersion = Tool_Misc::homesiteCssVersion();
        }

        if (!self::$imgDomain)
        {
            self::$imgDomain = Comm_Config::get('env.img_domain_v5');
        }
        
        self::_formatUrl($params, 'src', self::$imgVersion, self::$imgDomain);
        
        $paramStr = self::_renderTagProp($params);
        
        return "<img {$paramStr}/>";
    }

    private static function _mergeDefaultValue($original, array $default)
    {
        if (is_array($original))
        {
            return $original + $default;
        }

        return $original;
    }

    private static function _renderTagProp($props)
    {
        $propStr = '';

        foreach ($props as $k => $v)
        {
            $k = str_replace('_', '-', $k);
            $propStr .= "$k=\"$v\" ";
        }
        
        return $propStr;
    }

    private static function _getRandomDomain($domains)
    {
        static $i = -1, $j = 0;
        $domainCount = count($domains);
        $domain = null;

        if ($i++ % 2 == 0)
        {
            $domain = $domains[$j++ % $domainCount];
        }
        
        if ($domain === null)
        {
            $domain = $domains[0];
        }

        return $domain;
    }

    private static function _getDomain($domain, $key)
    {
        if ($domain)
        {
            return;
        }

        if (Tool_Version::isV5Page() == true)
        {
            $domain = Comm_Config::get("{$key}_v5");
        }
        else
        {
            $domain = Comm_Config::get($key);
        }
    }

    private static function _formatUrl(&$params, $key, $version, $domain)
    {
        if (empty($params[$key]))
        {
            return;
        }

        if (strpos($params[$key], 'http://') !== false)
        {
            $params[$key] .= '?version=' . $version;
        }
        else
        {
            $params[$key] = $domain . $params[$key] . '?version=' . $version;
        }
    }

    protected static $cache = array();

    /**
     * 显示用户微博等级
     *
     * @param array $param            
     * @param Smarty $smarty            
     */
    public static function rankIcon($param)
    {
        $viewer = Comm_Context::get("viewer", false);
        $actionType = (!$viewer) ? "login" : "";
        $iconHtmlSkel = "<a action-type=\"%s\" suda-data=\"key=tblog_grade_float&value=grade_icon_click\" target=\"_blank\" href=\"%s\" ><span class=\"W_level_ico color%d\"><span %s class=\"W_level_num l%d\"></span></span></a>";
        $iconHtml = '';

        if (isset($param['user']))
        {
            $type = (isset($param['aj_detail']) && $param['aj_detail']) ? 1 : 0;
            if (!isset(self::$cache['icon_rank'][$type][$param['user']['id']]))
            {
                try
                {
                    $userRank = Dr_User::getUserRank($param['user']['id']);
                    $userRankRange = Dr_User::getRankRange($userRank);
                    $addHtmlProperty = "";

                    if ($viewer)
                    {
                        if ($type == 1)
                        {
                            // 鼠标滑过调用ajax接口显示详情
                            $addHtmlProperty = ' node-type="level" ';
                        }
                        else
                        {
                            // 鼠标滑过显示title
                            $addHtmlProperty = 'title="' . _('当前等级：') . $userRank . '"';
                        }
                    }

                    $link = Comm_Config::get('domain.level') . '/u/' . (isset($param['user']['id']) && !empty($param['user']['id']) ? '?id=' . $param['user']['id'] : '');
                    $iconHtml = sprintf($iconHtmlSkel, $actionType, $link, $userRankRange['type'], $addHtmlProperty, $userRank);
                }
                catch (Exception $e)
                {
                    Tool_Log::error('FILTER', __METHOD__ . ", show icon by rank error:" . $e->getMessage());
                }
            }
        }
        
        return $iconHtml;
    }

    /**
     * 根据level显示对应的图标样式
     *
     * @param array $param            
     * @param Smarty $smarty            
     */
    public static function levelIcon($param)
    {
        $iconType = '';
        if (isset($param['extra']) && isset($param['extra']['verified']) && isset($param['extra']['verified_type']))
        {
            if ((boolean)$param['extra']['verified'] === true)
            {
                $iconType = ($param['extra']['verified_type'] == 0 ? 'v_person' : 'v_enterprise');
            }
            else
            {
                if ($param['extra']['verified_type'] == 220)
                {
                    $iconType = 'daren';
                }
                elseif ($param['extra']['verified_type'] == 10)
                {
                    $iconType = 'vgirl';
                }
            }
            
            if (!isset(self::$cache['show_icon_by_level']['icon_css']))
            {
                $iconCss = array(
                    'v_person' => array(
                        'css' => 'approve', 
                        'title' => _('新浪个人认证 '), 
                        'alt' => _('新浪个人认证 '), 
                        'href' => Comm_Config::get('domain.verified') . 'verify'
                    ), 
                    'v_enterprise' => array(
                        'css' => 'approve_co', 
                        'title' => _('新浪机构认证'), 
                        'alt' => _('新浪机构认证'), 
                        'href' => Comm_Config::get('domain.verified') . 'verify'
                    ), 
                    'daren' => array(
                        'css' => 'ico_club', 
                        'title' => _('微博达人'), 
                        'alt' => _('微博达人'), 
                        'href' => Comm_Config::get('domain.club') . '/intro'
                    ), 
                    'vgirl' => array(
                        'css' => 'ico_vlady', 
                        'title' => _('微博女郎'), 
                        'alt' => _('微博女郎'), 
                        'href' => Comm_Config::get('domain.vgirl')
                    )
                );

                self::$cache['show_icon_by_level']['icon_css'] = $iconCss;
            }
            else
            {
                $iconCss = self::$cache['show_icon_by_level']['icon_css'];
            }
        }

        if ($iconType)
        {
            $nodeType = ($iconType == 'daren') ? ' node-type="daren"' : "";
            $html = '<img src="' . Comm_Config::get('env.css_domain') . 'style/images/common/transparent.gif" title= "%s" alt="%s" class="%s"' . $nodeType . '/>';
            if (!isset($param['is_link']) || $param['is_link'] !== false)
            {
                $html = '<a target="_blank" href="' . $iconCss[$iconType]['href'] . '">' . $html . '</a>';
            }

            $title = (isset($param['title']) && $param['title']) ? $param['title'] : $iconCss[$iconType]['title'];
            
            return sprintf($html, $title, $iconCss[$iconType]['alt'], $iconCss[$iconType]['css']);
        }
        
        return false;
    }

    /**
     * 根据level显示对应的图标样式 v5版
     *
     * @param array $param  
     */
    public static function levelIconV5($param)
    {
        $isThings = false;
        $iconType = '';
        if (isset($param['extra']) && isset($param['extra']['verified']) && isset($param['extra']['verified_type']))
        {
            if ((boolean)$param['extra']['verified'] === true)
            {
                $iconType = ($param['extra']['verified_type'] == 0 ? 'v_person' : 'v_enterprise');
            }
            else
            {
                if ($param['extra']['verified_type'] == 220)
                {
                    $iconType = 'daren';
                }
                elseif ($param['extra']['verified_type'] == 10)
                {
                    $iconType = 'vgirl';
                }
                elseif (isset($param['extra']['ptype']) && $param['extra']['ptype'] <= 550 && $param['extra']['ptype'] >= 500)
                {
                    $iconType = 'lbs';
                    $isThings = true;
                }
            }

            if (!isset(self::$cache['show_icon_by_level']['icon_css']))
            {
                $iconCss = array(
                    'v_person' => array('css' => 'W_ico16 approve', 'title' => _('新浪个人认证 '), 'alt' => _('新浪个人认证 '), 'href' => Comm_Config::get('domain.verified') . 'verify'),
                    'v_enterprise' => array('css' => 'W_ico16 approve_co', 'title' => _('新浪机构认证'), 'alt' => _('新浪机构认证'), 'href' => Comm_Config::get('domain.verified') . 'verify'),
                    'daren' => array('css' => 'W_ico16 ico_club', 'title' => _('微博达人'), 'alt' => _('微博达人'), 'href' => Comm_Config::get('domain.club') . '/intro'),
                    'vgirl' => array('css' => 'W_ico16 ico_vlady', 'title' => _('微博女郎'), 'alt' => _('微博女郎'), 'href' => Comm_Config::get('domain.vgirl')),
                    'lbs' => array('css' => 'W_ico16 ico_pagelbs', 'title' => _('地点'), 'alt' => _('地点')),
                );

                self::$cache['show_icon_by_level']['icon_css'] = $iconCss;
            }
            else
            {
                $iconCss = self::$cache['show_icon_by_level']['icon_css'];
            }
        }

        if ($iconType)
        {
            $nodeType = ($iconType == 'daren') ? ' node-type="daren"' : "";
            $actionType = (isset($param['is_select']) && $param['is_select']) ? 'action-type="ignore_list"' : "";

            if (isset($param['login_flag']) && !$param['login_flag'])
            {
                $actionType = 'action-type="login"'; // 未登录
            }
            elseif (isset($param['is_select']) && $param['is_select'])
            {
                $actionType = 'action-type="ignore_list"';
            }
            else
            {
                $actionType = "";
            }

            if ($isThings === true)
            {
                $html = '<em title= "%s" class="%s"' . $nodeType . $actionType . '></em>';
            }
            else
            {
                $html = '<i title= "%s" class="%s"' . $nodeType . $actionType . '></i>';
                
                if (!isset($param['is_link']) || $param['is_link'] !== false)
                {
                    $html = '<a target="_blank" href="' . $iconCss[$iconType]['href'] . '">' . $html . '</a>';
                }
            }
            
            if (isset($param['login_flag']) && !$param['login_flag'])
            {
                $title = ""; // 未登录
            }
            else
            {
                $title = (isset($param['title']) && $param['title']) ? $param['title'] : $iconCss[$iconType]['title'];
            }

            return sprintf($html, $title, $iconCss[$iconType]['css']);
        }
        
        return false;
    }

    /**
     * 对微博时间做转换
     *
     * @param string $time            
     * @return string
     */
    public static function formatTime($time)
    {
        return Tool_Formatter_Time::timeFormat($time);
    }

    public static function webIm($param)
    {
        $owner = $param['owner'];
        $viewer = $param['viewer'];
        $relation = $param['relation'];
        $card = isset($param['card']);

        $allowMessage = $param['allow_message'];
        if ($owner['id'] == $viewer['id'])
        {
            return "";
        }

        $showWebIm = 0;
        if ($relation == 1 || $relation == 2 || $allowMessage)
        {
            $showWebIm = 1;
        }

        $html = "";
        if ($showWebIm == 1)
        {
            // 1 在线 2 忙碌3离线4隐身0不在线
            try
            {
                $onlineStatus = Dr_Im::statusQuery($owner['id']);
            }
            catch (Comm_Exception_Program $e)
            {
                $onlineStatus = array();
            }

            $version = self::getWvr();

            if (isset($onlineStatus['status']) && in_array($onlineStatus['status'], array(1, 2, 3)))
            {
                if ($card)
                {
                    $html = '<span class="IM_online"></span>';
                    $html .= '<a href="javascript:;" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">聊天</a>';
                }
                else
                {
                    $html = '<a class="webim_online" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">聊天</a>';
                }
            }
            else
            {
                if ($card)
                {
                    $html = '<span class="IM_offline"></span>';
                    $html .= '<a href="javascript:;" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">私信</a>';
                }
                else
                {
                    $html = '<a class="webim_leave" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">私信</a>';
                }
            }
        }

        return $html;
    }

    public static function webImV5($param)
    {
        $owner = $param['owner'];
        $viewer = $param['viewer'];
        $relation = $param['relation'];
        $card = isset($param['card']);
        $v5 = isset($param['v5']);
        $relation = isset($param['relation']);
        $allowMessage = $param['allow_message'];

        if ($owner['id'] == $viewer['id'])
        {
            return "";
        }

        $showWebIm = 0;
        if ($relation == 1 || $relation == 2 || $allowMessage)
        {
            $showWebIm = 1;
        }

        $html = "";
        if ($showWebIm == 1)
        {
            // 1 在线 2 忙碌3离线4隐身0不在线
            try
            {
                $onlineStatus = Dr_Im::statusQuery($owner['id']);
            }
            catch (Comm_Exception_Program $e)
            {
                $onlineStatus = array();
            }

            $version = self::getWvr();

            if (isset($onlineStatus['status']) && in_array($onlineStatus['status'], array(1, 2, 3)))
            {
                if ($card)
                {
                    $html = '<span class="W_chat_stat W_chat_stat_online"></span>';
                    $html .= '<a href="javascript:;" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">' . _('聊天') . '</a>';
                }
                else if ($v5)
                {
                    if (isset($param['page_suda']) && $param['page_suda'])
                    {
                        $key = 'relation.webim_' . $param['page_suda'];
                        if (Comm_Config::get($key))
                        {
                            $sudaUatrack = 'suda-uatrack="' . Comm_Config::get($key) . ':' . $owner['id'] . '"';
                        }
                    }
                    else
                    {
                        $sudaUatrack = "";
                    }

                    $html = '<a href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '" class="W_btn_c"' . $sudaUatrack . '><span><i class="W_chat_stat W_chat_stat_online"></i>' . _('聊天') . '</span></a>';
                }
                else if ($relation)
                {
                    $html = '<a href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '"><i class="W_chat_stat W_chat_stat_online"></i>' . _('聊天') . '</a>';
                }
                else
                {
                    $html = '<a class="webim_online" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">' . _('聊天') . '</a>';
                }
            }
            else
            {
                if ($card)
                {
                    $html = '<span class="W_chat_stat W_chat_stat_offline"></span>';
                    $html .= '<a href="javascript:;" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">' . _('私信') . '</a>';
                }
                else if ($v5)
                {
                    if (isset($param['page_suda']) && $param['page_suda'])
                    {
                        $key = 'relation.webim_' . $param['page_suda'];
                        if (Comm_Config::get($key))
                        {
                            $sudaUatrack = 'suda-uatrack="' . Comm_Config::get($key) . ':' . $owner['id'] . '"';
                        }
                    }
                    else
                    {
                        $sudaUatrack = "";
                    }

                    $html = '<a href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '" class="W_btn_c"' . $sudaUatrack . '><span><i class="W_chat_stat W_chat_stat_offline"></i>' . _('私信') . '</span></a>';
                }
                else if ($relation)
                {
                    $html = '<a href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '"><i class="W_chat_stat W_chat_stat_offline"></i>' . _('私信') . '</a>';
                }
                else
                {
                    $html = '<a class="webim_leave" suda-data="key=tblog_otherprofile_' . $version . '&value=chat" href="javascript:;" action-type="webim.conversation" action-data="uid=' . $owner['id'] . '&nick=' . $owner['screen_name'] . '">' . _('私信') . '</a>';
                }
            }
        }

        return $html;
    }
    
    /*
     * 根据传入的domain判断用户是否为微号用户，如果是返回微号入口链接 @param array $param $param Smarty $smarty
     */
    public static function domainIcon($param)
    {
        $isOwner = Tool_Misc::checkOwnerIsViewer();
        $weiboDomain = Comm_Config::get("domain.weibo");

        if (!isset($param['extra']) || !isset($param['extra']['id']))
        {
            $html = '<a href="{$weiboDomain}" class="online">' . $weiboDomain . '</a>';
            return $html;
        }

        $weihaoHtml = $html = "";
        $userInfo = $param['extra'];
        $domainBak = $showDomain = $showAllDomain = false;
        if (isset($param['show_domain']) && $param['show_domain'])
        {
            $showDomain = true;
        }

        // 如果模板中传参数show_all_domain的值，则微号及domain全显示
        if (isset($param['show_all_domain']) && $param['show_all_domain'])
        {
            $showAllDomain = true;
        }

        if (isset($userInfo['weihao']) && !empty($userInfo['weihao']))
        {
            $domainBak = true;
            $weihao = $userInfo['weihao'];
        }
        elseif (isset($userInfo['domain_bak']) && !empty($userInfo['domain_bak']))
        {
            $domainBak = true;
            $weihao = $userInfo['domain_bak'];
        }

        // 限制内网访问
        if ($domainBak && Comm_Config::get('control.use_weihao_icon'))
        {
            $weihaoList = Comm_Config::get("weihao");
            $weihaoInfo = Dr_Account::weihaoBatch(array($userInfo['id'] => $weihao));
            $weihaoHtml = '';

            if (isset($weihaoInfo[$userInfo['id']]) && is_array($weihaoInfo[$userInfo['id']]))
            {
                $weihaoInfo = $weihaoInfo[$userInfo['id']];
                $weihaoClass = $weihaoTitle = "";

                if (isset($weihaoInfo['type']) && isset($weihaoList[$weihaoInfo['type']]))
                {
                    if (isset($weihaoList[$weihaoInfo['type']]['class']) && $weihaoList[$weihaoInfo['type']]['class'] && isset($weihaoList[$weihaoInfo['type']]['title']) && $weihaoList[$weihaoInfo['type']]['title'])
                    {
                        $weihaoClass = $weihaoList[$weihaoInfo['type']]['class'];
                        $weihaoTitle = $weihaoList[$weihaoInfo['type']]['title'];
                        $weihaoHtml = '<a target="_blank" href="' . Comm_Config::get("domain.weihao") . '"><img class="' . $weihaoClass . '" src="' . Comm_Config::get('env.css_domain') . 'style/images/common/transparent.gif" title="' . $weihaoTitle . '"/></a>';
                    }
                }
            }
            
            // 微号用户同时显示域名domain信息
            if (isset($userInfo['domain']) && !empty($userInfo['domain']) && (($userInfo['domain'] != $weihao) && ($userInfo['domain'] != $userInfo['id'])) && $showAllDomain)
            {
                $wvr = Dr_User::getUserVersion();
                if ($isOwner)
                {
                    if (isset($wvr) && ($wvr == '3.6'))
                    {
                        $weihaoHtml .= '<br/><a class="online" href="/' . $userInfo['domain'] . '?from=profile&wvr=' . $wvr . '&loc=infdomain">' . $weiboDomain . '/' . $userInfo['domain'] . '</a>';
                    }
                    else
                    {
                        $weihaoHtml .= '<i class="W_vline">|</i><a class="online" href="/' . $userInfo['domain'] . '?from=profile&wvr=' . $wvr . '&loc=infdomain">' . $weiboDomain . '/' . $userInfo['domain'] . '</a>';
                    }
                }
                else
                {
                    if (isset($wvr) && ($wvr == '3.6'))
                    {
                        $weihaoHtml .= '<br/><a class="online" href="/' . $userInfo['domain'] . '?from=otherprofile&wvr=' . $wvr . '&loc=infdomain">' . $weiboDomain . '/' . $userInfo['domain'] . '</a>';
                    }
                    else
                    {
                        $weihaoHtml .= '<i class="W_vline">|</i><a class="online" href="/' . $userInfo['domain'] . '?from=otherprofile&wvr=' . $wvr . '&loc=infdomain">' . $weiboDomain . '/' . $userInfo['domain'] . '</a>';
                    }
                }
            }
        }

        if ($domainBak)
        {
            $userDomain = $userInfo['weihao'];
        }
        else
        {
            $userDomain = self::show_domain($userInfo);
        }

        $wvr = Dr_User::getUserVersion();
        $profile = $isOwner ? "profile" : "otherprofile";

        if ($showDomain)
        {
            $html = '<p><a href="/' . $userDomain . '?from=' . $profile . '&wvr=' . $wvr . '&loc=infweihao" class="online">' . $weiboDomain . '/' . $userDomain . '</a>' . $weihaoHtml . '</p>';
        }
        elseif ($domainBak)
        {
            $html = '<p class="info1"><a href="/' . $userDomain . '?from=' . $profile . '&wvr=' . $wvr . '&loc=infweihao">' . $weiboDomain . '/' . $userDomain . '</a>' . $weihaoHtml . '</p>';
        }

        return $html;
    }

    public static function showWvr()
    {
        $wvr = Dr_User::getUserVersion();
        if (isset($wvr) && ($wvr == '4' || $wvr == '3.6' || $wvr == '5'))
        {
            return "&wvr=" . $wvr;
        }
    }

    public static function showWvrNew()
    {
        return "&wvr=5";
    }

    public static function showDomain($user)
    {
        $isThings = false;
        if (isset($user['id']) && !empty($user['id']) && Dr_User::checkUserPtype($user['id']) === false)
        {
            $isThings = true;
        }

        if (isset($user['profile_url']) && !empty($user['profile_url']))
        {
            if ($isThings === true)
            {
                $user['profile_url'] .= (strpos($user['profile_url'], '?') !== false ? '&' : '?') . 'from=feed';
            }

            return $user['profile_url'];
        }

        // 如果是微号用户优先取微号
        if (isset($user['weihao']) && !empty($user['weihao']))
        {
            return $user['weihao'];
        }
        else
        {
            if (isset($user['domain']) && $user['domain'] == '')
            {
                return "u/" . $user['id'];
            }
            else
            {
                return ($user['domain'] != $user['id']) ? $user['domain'] : "u/" . $user['id'];
            }
        }
    }

    public static function unlogin($viewer)
    {
        if (!$viewer)
        {
            return 'action-type="login"';
        }
        else
        {
            return '';
        }
    }

    /**
     * 从微博信息source字段获取微博来源名 <a href="http://weibo.com">新浪微博</a> ==> 新浪微博
     * 通过定义的配置文件sina_appname来判断哪些应用是新浪应用
     *
     * @param string $source
     *            微博来源链接<a href="http://weibo.com">新浪微博</a>
     */
    public static function analyzeFeedSource($source)
    {
        $sinaAppName = Comm_Config::get("sina_appname");
        $source = strip_tags($source);
        $isactive = 1;

        if (in_array($source, $sina_app_name) || strpos($source, "新浪微群") !== FALSE || strpos($source, "微话题") !== FALSE || strpos($source, "微活动") !== FALSE || strpos($source, "投票") !== FALSE || strpos($source, "微访谈") !== FALSE)
        {
            $isactive = 0;
        }

        return "appname=" . $source . "&isactive=" . $isactive;
    }

    public static function conf($key)
    {
        return Comm_Config::get($key);
    }

    public static function truncate($string, $length = 80, $etc = '...', $breakWords = false, $middle = false)
    {
        if ($length == 0)
            return '';

        if (is_callable('mb_strlen'))
        {
            if (mb_detect_encoding($string, 'UTF-8, ISO-8859-1') === 'UTF-8')
            {
                // $string has utf-8 encoding
                if (mb_strlen($string, 'UTF-8') > $length)
                {
                    $length -= min($length, mb_strlen($etc));
                    
                    if (!$breakWords && !$middle)
                    {
                        $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1, 'UTF-8'));
                    }

                    if (!$middle)
                    {
                        return mb_substr($string, 0, $length, 'UTF-8') . $etc;
                    }
                    else
                    {
                        return mb_substr($string, 0, $length / 2, 'UTF-8') . $etc . mb_substr($string, - $length / 2, $length / 2, 'UTF-8');
                    }
                }
                else
                {
                    return $string;
                }
            }
        }

        // $string has no utf-8 encoding
        if (strlen($string) > $length)
        {
            $length -= min($length, strlen($etc));
            if (!$breakWords && !$middle)
            {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }

            if (!$middle)
            {
                return substr($string, 0, $length) . $etc;
            }
            else
            {
                return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
            }
        }
        else
        {
            return $string;
        }
    }

    /**
     * 根据信用等级显示对应的icon
     *
     * @param
     *            $param
     * @param
     *            $smarty
     */
    public static function creditIcon($param)
    {
        $html = '';
        $viewer = Comm_Context::get("viewer", false);
        $addImgType = (!$viewer) ? 'action-type="login"' : '"node-type="credit"';
        if (isset($param['user']))
        {
            $creditInfo = Dr_Credit::show($param['user']);

            if (isset($creditInfo['level']) && $creditInfo['level'] != '' && Tool_Credit::show_credit_by_level($creditInfo['level']))
            {
                if ($creditInfo['level'] == Comm_Config::get("credit.low_level"))
                {
                    $classname = 'W_ico16 credit_low';
                }
                else
                {
                    $classname = 'W_ico16 credit_middle';
                }

                $url = Comm_Config::get('domain.weibo') . '/' . $param['user']->id . '/' . 'info';
                $title = _('微博信用');
                $html = '<a href="' . $url . '"><img src="' . Comm_Config::get('env.css_domain') . 'style/images/common/transparent.gif"' . $addImgType . 'class="' . $classname . '"></a>';
            }
        }

        return $html;
    }

    public static function getWvr()
    {
        $wvr = Dr_User::getUserVersion();

        if ($wvr == '5')
        {
            return 'v5';
        }
        else if ($wvr == '4')
        {
            return 'v4';
        }
        else
        {
            return 'v3';
        }
    }

    /**
     * 根据用户会员身份信息显示会员icon
     *
     * @param array $param            
     * @param Smarty $smarty            
     */
    public function memberIcon($param)
    {
        $showDis = false; // 是否显示至灰按钮
        if (isset($param['show_dis']) && $param['show_dis'] === true)
        {
            $showDis = true;
        }

        $memberInfo = $param['member'];
        $userInfo = $param['userinfo'];
        if ($memberInfo == "" && isset($param['uid']) && $param['uid'] != '')
        {
            try
            {
                $memberInfo = Dr_Members::show($param['uid']);
            }
            catch (Comm_Weibo_Exception_Api $e)
            {
                $memberInfo = array();
            }
        }

        if ($userInfo == "" && isset($memberInfo['uid']) && $memberInfo['uid'] != '')
        {
            try
            {
                $userInfo = Dr_User::getUserInfo($memberInfo['uid']);
            }
            catch (Exception $e)
            {
            }
        }

        // 是否有身份icon
        $isShowSfIcon = false;
        if (isset($userInfo['verified']) && isset($userInfo['verified_type']))
        {
            if ((boolean)$userInfo['verified'] === true)
            {   
                $userType = ($userInfo['verified_type'] == 0 ? 'v_person' : 'v_enterprise');
                $isShowSfIcon = true;
            }
            else
            {
                if ($userInfo['verified_type'] == 220)
                {
                    $userType = 'daren';
                    $isShowSfIcon = true;
                }
                elseif ($userInfo['verified_type'] == 10)
                {
                    $userType = 'vgirl';
                    $isShowSfIcon = true;
                }
            }
        }

        // 会员icon
        $memberIcon = "";
        if (isset($param['member']) && $param['member'] != "")
        {
            $memberInfo = $param['member'];
            if (isset($memberInfo['type']) && $memberInfo['type'] != 0)
            {
                $title = _('微博会员');
                $href = "http://vip.weibo.com/personal?from=main";
                if ($memberInfo['type'] == 2)
                {
                    if ($showDis === true)
                    {
                        $memberIcon = '<img src="' . Comm_Config::get('env.css_domain') . 'style/images/common/transparent.gif" title= "' . $title . '" alt="' . $title . '" class="ico_member_dis"/>';
                        $memberIcon = '<a target="_blank" href="' . $href . '">' . $memberIcon . '</a>';
                    }
                }
                else
                {
                    $memberIcon = '<img src="' . Comm_Config::get('env.css_domain') . 'style/images/common/transparent.gif" title= "' . $title . '" alt="' . $title . '" class="ico_member"/>';
                    $memberIcon = '<a target="_blank" href="' . $href . '">' . $memberIcon . '</a>';
                }
            }
        }

        $allIcon = array();
        if ($memberIcon)
        {
            $allIcon[] = $memberIcon;
        }

        if ($isShowSfIcon === true && count($allIcon) > 2)
        {
            // 当有身份icon且运营icon数大于2个时只取两个运用icon
            $allIcon = array_slice($allIcon, 0, 2);
        }

        $iconType = "";
        foreach ($allIcon as $icon)
        {
            $iconType .= $icon;
        }

        return $iconType;
    }

    /**
     * v5版 会员插件
     */
    public function memberIconV5($param)
    {
        $iconType = "";
        $showDis = false; // 是否显示至灰按钮
        if (isset($param['show_dis']) && $param['show_dis'] === true)
        {
            $showDis = true;
        }

        $userInfo = $param['userinfo'];
        $memberInfo = $param['member'];
        if ($memberInfo == "" && isset($param['uid']) && $param['uid'] != '')
        {
            try
            {
                $memberInfo = Dr_Members::show($param['uid']);
            } 
            catch (Comm_Weibo_Exception_Api $e)
            {
                $memberInfo = array();
            }
        }

        if ($userInfo == "" && isset($memberInfo['uid']) && $memberInfo['uid'] != '')
        {
            try
            {
                $userInfo = Dr_User::getUserInfo($memberInfo['uid']);
            } 
            catch (Exception $e)
            {
            }
        }

        // 是否有身份icon
        $isShowSfIcon = false;
        if (isset($userInfo['verified']) && isset($userInfo['verified_type']))
        {
            if ((boolean)$userInfo['verified'] === true)
            {
                $userType = ($userInfo['verified_type'] == 0 ? 'v_person' : 'v_enterprise');
                $isShowSfIcon = true;                    
            }
            else
            {
                if ($userInfo['verified_type'] == 220)
                {
                    $userType = 'daren';
                    $isShowSfIcon = true;
                }
                elseif ($userInfo['verified_type'] == 10)
                {
                    $userType = 'vgirl';
                    $isShowSfIcon = true;
                }
            }
        }

        // 会员icon
        $memberIcon = "";
        if (isset($memberInfo['type']) && $memberInfo['type'] != 0)
        {
            $title = (isset($param['login_flag']) && !$param['login_flag']) ? "" : _('微博会员');
            // 未登录弹登录框
            $actionType = (isset($param['login_flag']) && !$param['login_flag']) ? 'action-type="login"' : "";
            
            if ($memberInfo['type'] == 2)
            {
                if ($showDis === true)
                {
                    $memberIcon = '<i class="W_ico16 ico_member_dis"></i>';
                    $memberIcon = '<a title="' . $title . '" target="_blank" href="http://vip.weibo.com/personal?from=main"' . $actionType . '>' . $memberIcon . '</a>';
                }
            }
            else
            {
                $memberIcon = '<i class="W_ico16 ico_member"></i>';
                $memberIcon = '<a action-type="ignore_list" title="' . $title . '" target="_blank" href="http://vip.weibo.com/personal?from=main"' . $actionType . '>' . $memberIcon . '</a>';
            }
        }

        /*// 2013新年红包ico
        $icoHongbao = '';
        if (isset($userInfo['badge']['hongbao']) && $userInfo['badge']['hongbao'])
        {
            if ($userInfo['badge']['hongbao'] == 1 && Tool_EffectiveCondition::isEffectiveByCID(Tool_EffectiveCondition::PARAM_CID_HONGBAO2013))
            {
                $icoHongbao = '<a target="_blank" href="http://2013.weibo.com/%s?source=icon" title="' . _('让红包飞') . '"><em class="W_ico16 %s"></em></a>';
                $icoHongbao = sprintf($icoHongbao, $userInfo['id'], 'ico_2013spring');
            }
        }*/

        $allIcon = array();
        if ($memberIcon)
        {
            $allIcon[] = $memberIcon;
        }

        /*if ($icoHongbao) {
            $allIcon[] = $icoHongbao;
        }*/
        
        if ($isShowSfIcon === true && count($allIcon) > 2)
        {
            // 当有身份icon且运营icon数大于2个时只取两个运用icon
            $allIcon = array_slice($allIcon, 0, 2);
        }

        $iconType = "";
        foreach ($allIcon as $icon)
        {
            $iconType .= $icon;
        }

        return $iconType;
    }

    /**
     * v5版 会员插件 #新版#add 2013-01-09
     *
     * @param $param 用户信息            
     * @param $count 显示icon数            
     * @return string
     */
    public static function memberIconNew($param, $type = self::ICON_TYPE_C)
    {
        $iconType = "";
        $userInfo = isset($param['userinfo']) ? $param['userinfo'] : $param;

        if (isset($userInfo['mbtype']))
        {
            $mbtype = $userInfo['mbtype'];

            if (!isset($userInfo['badge']) && !empty($userInfo['id']))
            {
                // ico补全计划，补全评论用户信息不全。by 2013 让红包飞需求
                try
                {
                    $info = Dr_User::getUserInfo($userInfo['id']);
                }
                catch (Exception $e)
                {
                    $info = array();
                }

                $userInfo['badge'] = isset($info->badge) ? $info->badge : array();
            }
        }
        else
        {
            if (!empty($userInfo['id']))
            {
                try
                {
                    $info = Dr_User::getUserInfo($userInfo['id']);
                }
                catch (Exception $e)
                {
                    $info = array();
                }
            }

            $mbtype = isset($info['mbtype']) ? $info['mbtype'] : 0;
        }
        
        $allIcon = array();

        // 会员icon
        $memberIcon = self::_vipIcon($mbtype, $param['show_dis'], $param["login_flag"], $param['is_link']);
        if ($memberIcon)
        {
            $allIcon[] = $memberIcon;
        }
        
        if ($type != self::ICON_TYPE_C)
        {
            // 合作icon
            $cooperationIcon = self::_cooperationIcon($userInfo);
            
            if ($cooperationIcon)
            {
                $allIcon[] = $cooperationIcon;
            }
        }
        
        if ($type == self::ICON_TYPE_A)
        {
            // 运营icon
            $operationIcon = self::_operationIcon($userInfo);
            
            if ($operationIcon)
            {
                $allIcon[] = $operationIcon;
            }
        }

        $iconType = "";
        foreach ($allIcon as $icon)
        {
            $iconType .= $icon;
        }

        return $iconType;
    }

    /**
     * 会员icon接口，供memeber_Icon调用
     *
     * @param int $mbtype
     *            会员类型
     * @param $isShowDis 会员过期时icon置灰            
     * @param $loginFlag 登录标识            
     * @return string
     */
    private function _vipIcon($mbtype, $isShowDis, $loginFlag, $isLink = true)
    {
        $showDis = false; // 是否显示至灰按钮
        if (isset($isShowDis) && $isShowDis === true)
        {
            $showDis = true;
        }

        $memberIcon = "";
        if ($mbtype != 0)
        {
            $title = (isset($loginFlag) && !$loginFlag) ? "" : _('微博会员');
            // 未登录弹登录框
            $actionType = (isset($loginFlag) && !$loginFlag) ? 'action-type="login"' : "";
            $hrefLink = ' target="_blank" href="http://vip.weibo.com/personal?from=main"';
            if ($mbtype == 2)
            {
                if ($showDis === true)
                {
                    $memberIcon = '<i class="W_ico16 ico_member_dis"></i>';
                    if ($isLink !== false)
                    {
                        $memberIcon = '<a title="' . $title . '"' . $hrefLink . $actionType . '>' . $memberIcon . '</a>';
                    }
                }

            }
            else
            {
                $memberIcon = '<i class="W_ico16 ico_member"></i>';
                if ($isLink !== false)
                {
                    $memberIcon = '<a action-type="ignore_list" title="' . $title . '"' . $hrefLink . $actionType . '>' . $memberIcon . '</a>';
                }
            }
        }

        return $memberIcon;
    }

    /**
     * 合作类icon
     *
     * @param unknown $userInfo            
     * @return string
     */
    private function _cooperationIcon($userInfo)
    {
        $icoCooperation = "";
        $icoSwitch = Comm_Config::get("control.switch_tao_icon", 0);
        if ($icoSwitch == 0)
        {
            return $icoCooperation;
        }

        // 淘宝icon
        if (isset($userInfo['badge']['taobao']) && $userInfo['badge']['taobao'] == 1)
        {
            $icoCooperation = '<a target="_blank" action-type="ignore_list" href="http://weibo.com/a/bind/open" title="' . _("淘宝商户") . '"><i class="W_ico16 ico_taobao"></i></a>';
        }

        // 天猫icon
        if (isset($userInfo['badge']['taobao']) && $userInfo['badge']['taobao'] == 2)
        {
            $icoCooperation = '<i title="' . _("天猫商户") . '" class="W_ico16 ico_tmall"></i>';
        }

        return $icoCooperation;
    }

    /**
     * 运营icon接口，供memberIcon使用
     * 运营icon需要在方法内部保证互斥，只加载一个运营icon
     *
     * @param array $userInfo
     *            用户信息
     * @return array 运营icon数组
     */
    private function _operationIcon($userInfo)
    {
        $now = time();
        
        // 感恩季活动icon
        $startDateGongyi2013 = strtotime('2013-11-18 00:00:00');
        $endDateGongyi2013 = strtotime('2013-12-16 23:59:59');
        if ($startDateGongyi2013 <= $now && $now < $endDateGongyi2013)
        {
            if (isset($userInfo['badge']['gongyi_level']) && 0 < $userInfo['badge']['gongyi_level'] && $userInfo['badge']['gongyi_level'] <= 5)
            {
                return '<a target="_blank" href="http://gongyi2013.weibo.com/?from=icon_' . $userInfo['badge']['gongyi_level'] . '" title="' . _('微博益起来') . '"><i class="W_ico16 ico_gongyi' . $userInfo['badge']['gongyi_level'] . '"></i></a>';
            }
        }
        
        //微公益icon
        $startDateWgy = strtotime("2013-10-14 00:00:00");
        $endDateWgy   = strtotime("2013-12-16 23:59:59");
        if ($startDateWgy <= $now && $now < $endDateWgy)
        {
            if (isset($userInfo['badge']['gongyi']) && $userInfo['badge']['gongyi'] == 1)
            {
                return '<a target="_blank" href="http://gongyi2013.weibo.com/?from=icon_1" title="' . _('微博益起来 ') . '"><i class="W_ico16 ico_gongyi"></i></a>';
            }
        }
    }

    /**
     * 根据用户隐私和关注关系获取是否显示
     *
     * @param $privacy 隐私值            
     * @param $relation 关注关系            
     * @return ture/false
     */
    public static function isShow($privacy, $relation)
    {
        $privacyMap = array(
            'all' => Dr_Privacy::VISIABLE_ALL, // 所有人可见
            'atn' => Dr_Privacy::VISIABLE_FOLLOWED, // 我关注的人可见
            'self' => Dr_Privacy::VISIABLE_SELF, // 仅自己可见
        ); 
        
        switch (intval($privacy))
        {
            case $privacyMap['all']:
                return true;

            case $privacyMap['atn']:
                return in_array($relation, array(Dr_Relation::RELATION_BILATERAL, Dr_Relation::RELATION_FOLLOWED));

            case $privacyMap['self']:
                return false;
        }

        return false;
    }

    /**
     *
     * 构造找人页搜索条件
     *
     * @param $sexSearch 性别关键词            
     * @param $singleSearch 感情关键词            
     * @param $tag1Search 标签关键词            
     * @param $bloodSearch 血型关键词            
     * @param $sexual_orientation 性取向关键词            
     * @return URL 找人页搜索URL
     */
    public static function prepareFindUrl($sexSearch = '', $singleSearch = '', $tag1Search = '', $bloodSearch = '', $sexualSearch = '', $provinceSearch = '')
    {
        $searchUrl = '/find/f?type=1&search=1';
        $sexSearch = $sexSearch != '' ? '&sex=' . $sexSearch : '';
        $singleSearch = $singleSearch != '' ? '&single=' . $singleSearch : '';
        $tag1Search = $tag1Search != '' ? '&tag1=' . rawurlencode($tag1Search) : '';
        $bloodSearch = $bloodSearch != '' ? '&blood=' . $bloodSearch : '';
        $sexualSearch = $sexualSearch != '' ? '&' . $sexualSearch : '';
        $provinceSearch = $provinceSearch != '' ? '&prov=' . $provinceSearch : '';
        $searchKey = $sexSearch . $singleSearch . $tag1Search . $bloodSearch . $sexualSearch . $provinceSearch;
        $searchUrl .= $searchKey;
        return $searchUrl;
    }

    /**
     *
     * 判断用户是否是公众邮箱白名单用户
     *
     * @param int $uid            
     *
     * @return boolean
     */
    public static function isPublicMsgWhiteUser($uid)
    {
        if (empty($uid))
        {
            return false;
        }

        $whiteList = Comm_Config::get("publicmsg.white");
        return is_array($whiteList) && in_array($uid, $whiteList);
    }

    /**
     * 判断用户是否是新版右侧模块白名单用户
     *
     * @param string $uid            
     * @return boolean
     */
    public static function isRightModWhiteUser($uid)
    {
        $whiteList = Comm_Config::get("rightmod.white");
        if (is_array($whiteList) && in_array($uid, $whiteList))
        {
            return true;
        }

        $tailnum = substr(strval($uid), -2);
        $tailWhiteList = array("02", "12", "42", "11");

        return in_array($tailnum, $tailWhiteList, true);
    }

    /**
     * “粉丝头条”道具，常驻微博入口开关（等待直通车改造、全量开放后可下线）
     *
     * @param array $user
     *            用户信息
     *            
     * @return array
     */
    public static function vipPopUserSwitch($user)
    {
        if (empty($user))
        {
            return false;
        }

        return true;
    }

    /**
     * 判断是否垃圾箱白名单用户
     * 
     * @param int $uid
     * @return boolean
     */
    public static function isTrashTargetUser($uid)
    {
        return true;
    }
}
