<?php

class Do_User extends Do_Abstract
{
    /**
     * 大头像尺寸
     */
    const ICON_LARGE_SIZE = 180;
    
    /**
     * 小头像尺寸
     */
    const ICON_SMALL_SIZE = 50;
    
    /**
     * 冻结用户
     */
    const STATE_FREEZE = 8;

    /**
     * 封杀用户
     */
    const STATE_BLOCK = 7;
    
    protected $props = array(
        'id' => array(
            'int',
            'min,1',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        
        //4-20字符之间，只能中文和全角字符、字母、下划线(_)
        'screen_name' => array(
            'string',
            'width_min,1;width_max,30;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),

        'name' => array(
            'string',
            'width_min,1;width_max,30;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT,
        ),

        //暂不知目前的省份的规则，暂写100000, 省份是否要缺省值? BJ 11 
        'province' => array(
            'int',
            'min,1;max,100000',
            Comm_ArgChecker::OPT_USE_DEFAULT,
            Comm_ArgChecker::RIGHT,
            11,
        ),

        'city' => array(
            'int',
            'min,0;max,100000',
            Comm_ArgChecker::OPT_USE_DEFAULT, 
            Comm_ArgChecker::RIGHT,
            1,
        ),

        'location' => '',
        'description' => array(
            'string',
            'width_max,140',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        'url' => '',
        'profile_image_url' => '',
        'has_profile_image' => '',  
        'large_profile_image_url' => '', 
        'domain' => '',
        'domain_bak' => '',
        'weihao' => '',
        'gender' => array(
            'enum',
            'enum,m,f',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT,
        ),
        
        'followers_count' => '',
        'friends_count' => '',
        'statuses_count' => '',
        'favourites_count' => '',
        'bi_followers_count' => '',
        'private_friends_count' => '',
        'allow_all_comment' => '',
        'follow_me' => '',
        'avatar_large' => '',
        'verified_reason' => '',
        'online_status' => '',
        'created_at' => '',
        'following' => '',
        'verified' => '',
        'verified_type' => '',
        'allow_all_act_msg' => '',
        'geo_enabled' => '',
        'status' => 'Do_Status',
        'remark' => '',
        'email' => '',
        'status_id' => '',
        'lang' => '',
        'level' => '',
        'type' => '',
        'badage' => '', //旧
        'badge' => '',  //新
        'lang' => '',
        'state' => '',
        'profile_url' => '',
        'mbtype' => '',
        'mbrank' => '',
        'ptype' => '', // page type
    );
    
    /**
     * 检测当前登录用户是否已开通微博
     * @return string|string
     */
    public function isOpenMblog()
    {
        return (bool)($this->profile_image_url || $this->gender);
    } 
    
    /**
     * 检测用户信息是否完整
     */
    public function isUserInfoFull()
    {
        return !(empty($this->gender) || $this->screen_name == $this->id);
    }

    public function getProfileImageUrl()
    {
        $profileImageUrl = $this->getData('profile_image_url');
        
        if (Tool_Formatter_Face::checkHasProfileImage($profileImageUrl) === false)
        {
            $profileImageUrl = Tool_Formatter_Face::formatProfileImage($this->gender, 'medium');
        }

        return $profileImageUrl;
    }

    public function getLargeProfileImageUrl()
    {
        $profileImageUrl = $this->getData('profile_image_url');

        if (Tool_Formatter_Face::checkHasProfileImage($profileImageUrl) === false)
        {
            $largeProfileImageUrl = Tool_Formatter_Face::formatProfileImage($this->gender, 'big');
        } 
        else 
        {
            $largeProfileImageUrl = str_replace('/' . self::ICON_SMALL_SIZE . '/', '/' . self::ICON_LARGE_SIZE . '/', $profileImageUrl);
        }

        return $largeProfileImageUrl;
    }

    public function setDomain($domain)
    {
        $domainBak = null;
        if (strlen(trim($domain)) <= 0)
        {
            $id = parent::offsetGet("id");
            $domain = $id;
        }
        else
        {
            if (!preg_match('/\D+/', $domain))
            {
                $domainBak = $domain;
            }
        }

        $validatedValue = self::applyRuleOnProperty("domain", $domain);
        parent::setData("domain_bak", $domainBak);
        parent::setData("domain", $validatedValue);
    }

    public function getDomain()
    {
        $domain = $this->getData("domain");       
        return empty($domain) ? $this->id : $domain;
    }

    public function getUrl()
    {
        $url = $this->getData("url");
        return $url == 'http://1' ? '' : $url;
    }   
    
    public function setScreenName($name)
    {
        if (strlen(trim($name)) <= 0)
        {
            $id = parent::offsetGet("id");
            $screenName = $id;     
        }
        else
        {
            $screenName = self::applyRuleOnProperty("screen_name", $name);
        }

        parent::setData("screen_name", $screenName);
    }
    
    public function getScreenName()
    {
        $screenName = $this->getData("screen_name");     
        return empty($screenName) ? $this->id : $screenName;
    }
    
    public function setName($name)
    {
        if (strlen(trim($name)) <= 0)
        {
            $id = parent::offsetGet("id");
            $setName = $id;   
        }
        else
        {
            $setName = self::applyRuleOnProperty("name", $name);
        }

        parent::setData("name", $setName);
    }
        
    public function getName()
    {
        $name = $this->getData("name");       
        return empty($name) ? $this->id : $name;
    }

    public function getProfileUrl()
    {
        $profileUrl = $this->getData('profile_url');
        if (strlen(trim($profileUrl)) <= 0)
        {
            $weihao = $this->getData('weihao');
            $domain = $this->getData('domain');
            $id = $this->getData('id');

            if (strlen(trim($weihao)) > 0)
            {
                $profileUrl = $weihao;
            }
            elseif (strlen(trim($domain)) > 0 && $domain !== $id)
            {
                $profileUrl = $domain;
            }
            else
            {
                $profileUrl = 'u/' . $id;
            }
        }
        
        return $profileUrl;
    }
}
