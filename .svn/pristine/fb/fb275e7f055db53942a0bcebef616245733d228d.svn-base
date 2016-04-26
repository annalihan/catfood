<?php

class Page_Weibo extends Page_Abstract
{
    public $pageId = '';
    public $domainVer = 5; //4:v4, 5:v5

    /**
     * 宽版7个页面添加pageType配置
     * @var array
     */
    private $_widePageIds = array(
        'content_home' => 1, 
        'content_meWeibo' => 1, 
        'content_meComment' => 1, 
        'comments' => 1, 
        'commentsend' => 1, 
        'content_fav' => 1,
    );

    /**
     * v5相关的配置
     * @var array
     */
    private $_v5PageIds = array(
        'v5_profile_info' => 1, //v5 profile  info页
        'hisfollow' => 1, //v5 关注/粉丝页
        'v5_profile_album' => 1, //v5 相册页
        'content_myWeibo' => 1, //v5 我的profile
        'content_hisWeibo' => 1, //v5他人的profile
    );

    /**
     * v5关注、粉丝、密友页
     * @var array
     */
    private $_relationPageIds = array(
        'myfans' => 1, 
        'myfollow' => 1, 
        'myclosefriends' => 1, 
        'publicgroup' => 1,
    );

    public final function getMetaData()
    {
        $metaData = Tool_Meta::getConfigMeta($this->domainVer);
        
        $metaData['g_current_bp_mode'] = $this->getBigpipeMode();
        $metaData['g_page_id'] = $this->pageId;
        $metaData['g_bp_type'] = $this->_getBigpipeType();

        return $metaData;
    }

    private function _getBigpipeType()
    {
        switch (true)
        {
            case isset($this->_widePageIds[$this->pageId]):
                $bigpipeType = 'main';
                break;

            case isset($this->_v5PageIds[$this->pageId]):
                $bigpipeType = 'profile';
                break;

            case isset($this->_relationPageIds[$this->pageId]):
                $bigpipeType = 'relation';
                break;

            case (0 === strpos($this->pageId, 'taobao_')):
                $bigpipeType = 'taobao';
                break;
            
            default:
                $bigpipeType = '';
                break;
        }

        return $bigpipeType;
    }
}
