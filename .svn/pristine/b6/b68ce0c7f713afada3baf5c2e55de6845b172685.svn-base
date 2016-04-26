<?php
class Comm_Weibo_Api_Account
{
    const RESOURCE = "account";

    /**
     * 上传用户头像
     */
    public static function uploadAvatar()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "avatar/upload");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("image", "filepath");
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 设置pid
     */
    public static function setPid()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "avatar/set_pid");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("pid", "string", true);
        $platform->addRule("icon_version", "int64", true);
        $platform->addRule("coordinates", "string", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 获取用户基本信息
     */
    public static function getProfileBasic()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/basic");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 获取教育信息
     */
    public static function getProfileEducation()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/education");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");

        return $platform;
    }

    /**
     * 批量获取教育信息
     * @todo 确认uids的类型
     */
    public static function educationBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/education_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "int64");

        return $platform;
    }

    /**
     * 获取职业信息
     */
    public static function getProfileCareer()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/career");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");

        return $platform;
    }

    /**
     * 批量获取职业信息
     */
    public static function profileCareerBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/career_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "int64");

        return $platform;
    }
    /**
     * 获取隐私信息
     */
    public static function getPrivacy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_privacy");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

        return $platform;
    }

    /**
     * 批量获取用户的隐私设置
     */
    public static function getPrivacyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_privacy_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "string");

        return $platform;
    }
    /**
     * 获取所有学校列表
     */
    public static function profileSchoolList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/school_list");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("province", "int");
        $platform->addRule("city", "int");
        $platform->addRule("area", "int");
        $platform->addRule("type", "int", false);
        $platform->addRule("capital", "string");
        $platform->addRule("keyword", "string");
        $platform->addRule("count", "int");

        return $platform;
    }

    /**
     * 获取用户的手机绑定状态
     */
    public static function mobile()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "mobile");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'int64', false);

        return $platform;
    }

    /**
     * 批量获取当前用户手机绑定状态 （sass检测用，取根微博用户绑定情况）
     */
    public static function mobileMulti()
    {
        $url = 'http://mobile.cws.api.matrix.sina.com.cn/api/get_mobile_status.php';
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "string", true);

        return $platform;
    }

    /**
     * 获取用户个性设置
     */
    public static function getSettings()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "settings");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 获取当前登录用户的水印设置信息
     */
    public static function watermark()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "watermark");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

        return $platform;
    }

    /**
     * 申请添加新学校名称
     */
    public static function profileNewSchool()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/new_school");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("province", "int", true);
        $platform->addRule("city", "int");
        $platform->addRule("schooltype", "int", true);
        $platform->addRule("school_name", "string", true);

        return $platform;
    }

    /**
     * 更新用户基本信息
     */
    public static function updateProfileBasic()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/basic_update");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("screen_name", "string");
        $platform->addRule("real_name", "string");
        $platform->addRule("real_name_visible", "int");
        $platform->addRule("province", "int");
        $platform->addRule("city", "int");
        $platform->addRule("birthday", "date");
        $platform->addRule("birthday_visible", "int");
        $platform->addRule("qq", "string");
        $platform->addRule("qq_visible", "int");
        $platform->addRule("msn", "string");
        $platform->addRule("msn_visible", "int");
        $platform->addRule("url", "string");
        $platform->addRule("url_visible", "int");
        $platform->addRule("gender", "string");
        $platform->addRule("credentials_type", "int");
        $platform->addRule("credentials_num", "string");
        $platform->addRule("email", "string");
        $platform->addRule("email_visible", "int");
        $platform->addRule("lang", "string");
        $platform->addRule("description", "string");
        $platform->addRule("_trace", "string");

        return $platform;
    }

    /**
     * 更新用户教育信息
     */
    public static function updateProfileEducation()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/education");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("id", "int64");
        $platform->addRule("year", "int");
        $platform->addRule("department", "string");
        $platform->addRule("visible", "int");
        $platform->addRule("type", "int");
        $platform->addRule("school_id", "int");

        $callbackObj = new Comm_Weibo_Api_Account();
        $platform->addBeforeSendCallback($callbackObj, "idTypeSchool");

        return $platform;
    }

    /**
     * 删除用户教育信息
     */
    public static function deleteProfileEducation()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/education");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "DELETE");
        $platform->addRule("id", "int64");

        return $platform;
    }

    /**
     * 更新用户职业信息
     */
    public static function updateProfileCareer()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "profile/career");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("id", "string");
        $platform->addRule("start", "int");
        $platform->addRule("end", "int");
        $platform->addRule("department", "string");
        $platform->addRule("visible", "int");
        $platform->addRule("province", "int");
        $platform->addRule("city", "int");
        $platform->addRule("company", "string");

        $callbackObj = new Comm_Weibo_Api_Account();
        $platform->addBeforeSendCallback($callbackObj, "idCompany");

        return $platform;
    }

    /**
     * 更改账户密码
     */
    public static function password()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "password");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("old_psw", "string", true);
        $platform->addRule("new_psw", "string", true);

        return $platform;
    }

    /**
     * 绑定用户手机
     */
    public static function mobileUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "mobile/update");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("number", "string", true);

        return $platform;
    }

    /**
     * 解除手机绑定
     */
    public static function mobileDestroy()
    {
        //TODO
    }

    /**
     * 更新用户个性设置(只支持手机)
     */
    public static function settingsUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "settings/update");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("follower", "int", false);
        $platform->addRule("dm", "int", false);
        $platform->addRule("mention", "int", false);
        $platform->addRule("status_type", "int", false);
        $platform->addRule("from_user", "int", false);
        $platform->addRule("comment", "int", false);
        $platform->addRule("to_me_list_ids", "int", false);
        $platform->addRule("short_messages", "int64", false);
        $platform->addRule("groups", "int", false);
        $platform->addRule("notice", "int", false);
        $platform->addRule("sys_notice", "int", false);
        $platform->addRule("app_notice", "int", false);
        $platform->addRule("public_group_notice", "int", false);
        $platform->addRule("invitation", "int", false);
        $platform->addRule("album", "int", false);
        $platform->addRule("email", "int", false);
        $platform->addRule("attitude", "int", false);
        $platform->addRule("common_comments", "int", false);
        $platform->addRule("beginner", "int", false);
        $platform->addRule("fun_weibo", "int", false);
        $platform->addRule("favorite_tip", "int", false);
        $platform->addRule("pop", "int", false);
        $platform->addRule("subscribe_msg_email", "int", false);
        $platform->addRule("subscribe_hots_email", "int", false);
        $platform->addRule("sina_news", "int", false);
        $platform->addRule("sinat", "int", false);
        $platform->addRule("friend_circle", "int", false);
        $platform->addRule("friend_group", "int", false);

        return $platform;
    }

    /**
     * 更新用户隐私设置
     * source true    string  申请应用时分配的AppKey，调用接口时候代表应用的唯一身份（采用OAuth授权方式不需要此参数）。
     * comment false   int 是否可以评论我的微博，0--所有人，1--关注的人,2--可信用户，3--仅密友。默认不更新.
     * geo false   int 是否开启地理信息，0--不开启，1--开启。默认不更新。
     * message false   int 是否可以给我发私信，0--所有人，1--我关注的人，2--可信用户，3--仅密友。默认不更新。
     * realname    false   int 是否可以通过真名搜索到我，0--不可以，1--可以，默认不更新。
     * badge   false   int 勋章是否可见，0--不可见，1--可见，默认不更新。
     * mobile  false   int 是否可以通过手机号码搜索到我，0--不可以，1--可以，默认不更新。
     * profile_url_type    false   int 微博地址类型，1--微号，2--个性域名。默认不更新.
     * webim   false   int 是否开启webim，0--不开启，1--开启。默认不更新。
     */
    public static function updatePrivacy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "update_privacy");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule('comment', 'int', false);
        $platform->addRule("geo", 'int', false);
        $platform->addRule("message", 'int', false);
        $platform->addRule("realname", 'int', false);
        $platform->addRule("badge", 'int', false);
        $platform->addRule("mobile", 'int', false);
        $platform->addRule("profile_url_type", 'int', false);
        $platform->addRule("webim", 'int', false);

        return $platform;
    }

    /**
     * before_send callback方法
     */
    public function idTypeSchool($platform)
    {
        if (!is_null($platform->id))
        {
            if (!is_null($platform->type) || is_null($platform->school_id))
            {
                throw new Comm_Exception_Program("update education params error");
            }
        }
        else
        {
            if (is_null($platform->type) || is_null($platform->school_id))
            {
                throw new Comm_Exception_Program("type and school_id should be set value");
            }
        }
    }

    public function idCompany($platform)
    {
        if (is_null($platform->company) && is_null($platform->id))
        {
            throw new Comm_Exception_Program("company or id should be set value");
        }
    }
    
    /**
     * 更新用户isucdomain字段
     */
    public static function temporaryUcDomain()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "temporary/uc_domain");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", 'int64', true);
        $platform->addRule("value", 'int', true);

        return $platform;
    }

    /**
     * 获取解冻验证码
     * */
    public static function activationCode()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "activation_code");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("mobile", 'int', true);

        return $platform;
    }

    /**
     * 获取微号信息
     * */
    public static function weihao()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "weihao");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("number", 'int', true);
        $platform->addRule("need_all", 'string', false);
        $platform->addRule("language", 'string', false);

        return $platform;
    }

    /**
     * 获取微号信息批量
     * */
    public static function weihaoBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "weihao_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("numbers", 'string', true);
        $platform->addRule("need_all", 'string', false);
        $platform->addRule("language", 'string', false);
        
        return $platform;
    }

}
