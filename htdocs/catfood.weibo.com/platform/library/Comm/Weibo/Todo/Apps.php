<?php
//TODO 专业版
class Comm_Weibo_Apps
{
    // 卸载/删除应用
    const UNINSTALL_APP_URL = "http://i.open.t.sina.com.cn/eweibo/delcomapp.php?sub_appkey=%s&uid=%s";
    // 编辑企业应用
    const EDIT_APP_URL = "http://i.open.t.sina.com.cn/eweibo/editcomapp.php?appkey=%s&uid=%s&appname=%s";
    // 获取开发者应用详情
    const GET_APPINFO_URL = "http://i.open.t.sina.com.cn/eweibo/getcomappinfo.php?appkey=%s";
    // 获取开发者应用列表
    const GET_APPLIST_URL = "http://i.open.t.sina.com.cn/eweibo/getcomapplist.php?page=%s&pageSize=%s&orderByNum=%s";
    // 获取企业安装的应用详情
    const GET_USERAPP_URL = "http://i.open.t.sina.com.cn/eweibo/getusedcomappinfo.php?sub_appkey=%s";
    // 获取企业安装的应用列表
    const GET_USERAPPLIST_URL = "http://i.open.t.sina.com.cn/eweibo/getusedcomapplist.php?uid=%s";
    // 安装企业应用
    const INSTALL_APP_URL = "http://i.open.t.sina.com.cn/eweibo/addcomapp.php?appkey=%s&uid=%s";
    // 获取企业应用行业和功能分类配置接口
    const GET_COM_APP_CLASS_URL = "http://i.open.t.sina.com.cn/eweibo/getcomappclass.php";
    // 企业应用的APP_TYPE value为3 由应用平台配置
    const EPS_APP_TYPE = 3;
    // 搜应用对外接口
    const SERACH_APP_URL = 'http://miniblog.match.sina.com.cn/openapi/rpcApp.php?sid=a_weibo&app_type=%s&cuid=%s&type=%s&page=%s&pagesize=%s&name=%s&%s=%s&black=%s';
    // 获取微热卖数据接口
    const GET_APP_DATA = "http://app.a.weibo.com/htdocs/hotsell/?c=HotsellAppInterface&m=loadGoodsInfo&uid=%s&appKey=%s&page=%s&num=%s";
    // 开放平台新增获取已安装企业应用的企业UID列表接口
    const GET_APPINFO_UID_URL = "http://i.open.t.sina.com.cn/eweibo/getcomuidlistbyappkey.php?appkey=%s&page=%s&pageSize=%s";

    /**
     * 获取应用数据
     *
     * @param unknown_type $appkey
     * @param unknown_type $page
     * @param unknown_type $num
     * @throws Comm_Exception_Program
     */
    public static function getAppData($uid, $appkey, $page, $num)
    {
        if (empty($appkey) || !is_numeric($appkey))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = sprintf(self::GET_APP_DATA, $uid, $appkey, $page, $num);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['result'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['data'];
    }

    /**
     * 卸载/删除应用
     *
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function uninstallApp($appkey, $uid)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::UNINSTALL_APP_URL, $appkey, $uid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1 && $result['errno'] != - 9)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['errno'];
    }

    /**
     * 编辑企业应用
     *
     * @param unknown_type $uid
     * @param unknown_type $appname
     * @throws Comm_Exception_Program
     */
    public static function editApp($uid, $appname)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::EDIT_APP_URL, Comm_Config::get("env.platform_api_source"), $uid, $appname);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['result'];
    }

    /**
     * 获取开发者应用详情
     *
     * @param strin $appkey 待查询应用的APPKEY
     * @throws Comm_Exception_Program
     * @return mixed
     */
    public static function getAppinfo($appkey)
    {
        if (empty($appkey) || !is_numeric($appkey))
        {
            throw new Comm_Exception_Program('argument $appkey must be not empty and is numeric');
        }
        $url = sprintf(self::GET_APPINFO_URL, $appkey);
        $response = Tool_Http::get($url, TRUE);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['result'];
    }

    /**
     * 获取开发者应用列表
     *
     * @param unknown_type $uid
     * @param unknown_type $page
     * @param unknown_type $pagesize
     * @param unknown_type $order
     * @throws Comm_Exception_Program
     */
    public static function getApplist($uid, $page, $pagesize, $order)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::GET_APPLIST_URL, $page, $pagesize, $order);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 获取企业安装的某个应用详情
     *
     * @param unknown_type $subAppkey
     * @throws Comm_Exception_Program
     */
    public static function getUserapp($subAppkey)
    {
        $url = sprintf(self::GET_USERAPP_URL, $subAppkey);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['result'];
    }

    /**
     * 获取企业安装的应用列表
     *
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getUserApplist($uid)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::GET_USERAPPLIST_URL, $uid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return $result['result'];
    }

    /**
     * 安装应用
     *
     * @param string $appkey
     * @param string $uid
     * @throws Comm_Exception_Program
     */
    public static function installApp($appkey, $uid)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::INSTALL_APP_URL, $appkey, $uid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1 && $result['errno'] != '-9')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }

        return true;
    }

    /**
     * 获取企业应用行业和功能分类配置
     */
    public static function getComAppClass()
    {
        $url = self::GET_COM_APP_CLASS_URL;
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 搜应用
     * 返回值样例 ：
     * array(
     * [q] => 搜索条件,
     * [m] => 条数,
     * [m2] => 过滤后条数,
     * [sn] => 起始位置，
     * [en] => 结束位置,
     * [pf] => 搜索服务参数,
     * [ps] => 搜索服务参数,
     * [time] => 搜索耗时,
     * [result] => array(
     * [0] => array(
     * [app_name] => 应用名称
     * [app_id] => 应用id
     * [app_tags] => 应用标签
     * [app_key] => 应用来源id
     * [app_type] => 应用分类
     * [dev_name] => 开发者姓名
     * [user_num] => 使用人数
     * [dev_website] => 开发网站
     * [app_time] => 应用开发的时间
     * [app_desc] => 应用描述
     *)
     * …
     *)
     *)
     *
     * @param string $cuid
     * @param string $type
     * @param int $page
     * @param int $pagesize
     *
     */
    public static function searchEpsApp($uid, $type = '', $page = 1, $pagesize = 10, $name = '', $black = '', $orderby = 'xsort', $order = '')
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
        }

        $url = sprintf(self::SERACH_APP_URL, self::EPS_APP_TYPE, $uid, $type, $page, $pagesize, $name, $order, $orderby, $black);

        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if (isset($result['errno']))
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result;
    }

    /**
     * 获取已安装企业应用的企业UID
     *
     * @param strin $appkey
     *            待查询应用的APPKEY
     * @throws Comm_Exception_Program
     * @return mixed
     */
    public static function getComAppUid($appkey, $page = 1, $pagesize = 10)
    {
        if (empty($appkey) || !is_numeric($appkey))
        {
            throw new Comm_Exception_Program('argument $appkey must be not empty and is numeric');
        }
        $url = sprintf(self::GET_APPINFO_UID_URL, $appkey, $page, $pagesize);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }
}
