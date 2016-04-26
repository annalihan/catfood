<?php
//TODO 专业版
class Comm_Weibo_ResMenu
{
    const GETUIDRESMENULIST = 'http://i.api.place.weibo.cn/getUidResMenuList.php?uid=%s';
    const GETRESMENU_URL = 'http://i.api.place.weibo.cn/getResMenu.php?poiid=%s&poiids=%s&num=%d&page=%d&flag=%d&display=%d';
    const GETRESMENUPIC_URL = 'http://i.api.place.weibo.cn/getResMenuPic.php?id=%d&count=%d&page=%d';
    const ADDRESMENUFAVOR_URL = 'http://i.api.place.weibo.cn/addResMenuFavor.php';
    const GETRESMENUFAVORS_URL = 'http://i.api.place.weibo.cn/getResMenuFavors.php?id=%d&count=%d&page=%d';
    const GETRESMENUCOMMENT_URL = 'http://i.api.place.weibo.cn/getResMenuComment.php?resid=%d&top=%d&score=%d&count=%d&page=%d';
    // 通过企业微博uid获取 poiid和企业微博绑定关系
    const GETPOOIDBYUID_URL = 'http://api.place.weibo.cn/getPoiidByUid.php?uid=%s';
    // 获取照片墙信息接口
    const GETPHOTOWALL_URL = 'http://i.api.place.weibo.cn/getPoiCooperationPic.php?poiid=%s&poiids=%s&num=%d&page=%d&display=%d';

    /**
     * 获取GET请求的响应内容
     *
     * @param string $url
     * @param BOOL $isRawUrl
     *            是否直接使用原始url，此参数解决GET参数传递数组的情况，如id[]=xxx&id[]=yyy
     * @return mixed
     */
    public static function getResponseResult($url, $isRawUrl = FALSE)
    {
        $request = new Comm_HttpRequest();
        if ($isRawUrl === FALSE)
        {
            $request->setUrl($url);
        }
        else
        {
            $request->url = $url;
        }

        $request->send();
        return $request->getResponseContent();
    }

    public static function getResmenuByEpsid($uid)
    {
        if (empty($uid))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETUIDRESMENULIST, $uid);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }

    /**
     * 获取指定POIID的餐饮商家的菜单
     *
     * @param unknownType $poiid
     *            企业poi点ID
     * @param unknown_type $page
     *            第几页
     * @param unknown_type $count
     *            返回结果数量(最多100)
     * @param int $flag
     *            是否为购买照片墙的用户
     * @param int $display
     *            显示：0否，1是，2全部。默认1。 （0表示购买者在后台将其设置为隐藏,前台展示请传1）
     * @return multitype: mixed
     */
    public static function getResmenuInfo($poiid, $poiids = '', $page = 1, $count = 10, $flag = 0, $display = 1)
    {
        if (empty($poiid) && empty($poiids))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETRESMENU_URL, $poiid, $poiids, $count, $page, $flag, $display);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }

    /**
     * 获取用户照片墙信息
     *
     * @param unknown_type $poiid
     * @param unknown_type $poiids
     * @param unknown_type $page
     * @param unknown_type $count
     * @param unknown_type $flag
     *            现阶段暂时不用该参数。
     * @param unknown_type $display
     * @return multitype: mixed
     */
    public static function getPhotoWallInfo($poiid, $poiids = '', $page = 1, $count = 10, $flag = 0, $display = 1)
    {
        if (empty($poiid) && empty($poiids))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETPHOTOWALL_URL, $poiid, $poiids, $count, $page, $display);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }
    public static function getResmenuPic($id, $page = 1, $count = 10)
    {
        if (empty($id))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETRESMENUPIC_URL, $id, $count, $page);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }
    public static function getResmenuComment($id, $top = 0, $score = 0, $page = 1, $count = 10)
    {
        if (empty($id))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETRESMENUCOMMENT_URL, $id, $top, $score, $count, $page);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }

    /**
     * 根据uid获取企业微博绑定的poiid
     *
     * @param int $uid
     * @return array
     */
    public static function getPoiidByUid($uid)
    {
        if (empty($uid))
        {
            return array();
        }
        try
        {
            $url = sprintf(self::GETPOOIDBYUID_URL, $uid);
            $result = Tool_Http::get($url);
            $result = json_decode($result, true);
            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }
}
