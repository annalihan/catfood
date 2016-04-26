<?php
class Model_Index
{
    public static $domain = "http://weibo.com";

    const DEFAULT_TYPE = 1;        //默认显示type：1为昨日净增榜单，2为总积分榜单
    const DEFAULT_PAGE = 1;        //榜单排名默认显示第一页
    const PAGE_SIZE    = 5;       //榜单排名每页显示条数
    public $totalRecord;
    public function getIntegralByUserId($uid)
    {
        $url = 'http://p.e.weibo.com/i/ecom/info';
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', "int", true);
        $platform->setValue('uid', $uid);
        //getResult 参数FALSE 为不解析返回资源
        $platform->getResult(false);
        $result = $platform->getHttpRequest()->responseContent;
//         Comm_Helpers::p($result,1);
        return $result ? json_decode($result, true) : '';
    }

    public function isOpenMblogPayment($uid)
    {
        $url = 'http://i.e.weibo.com/epspcpage/nav/applist';
        $pageId = intval(100606 . $uid);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('page_id', "int", true);
        $platform->addRule('uid', "int", true);
        $platform->setValue('page_id', $pageId);
        $platform->setValue('uid', $uid);
        //getResult 参数FALSE 为不解析返回资源
        $platform->getResult(false);
        $result = $platform->getHttpRequest()->responseContent;
        return $result ? json_decode($result, true) : '';
    }
    /**
     * 通过接口获取积分榜单信息
     * @param int $uid 当前登录用户uid
     * @param int $type 获取积分榜单信息类型：1昨日增长榜单，2总积分榜单
     * @return array
     * @interface ==> http://p.e.weibo.com/i/ecom/top
     * */
    public function getRankList($uid = 0, $type = self::DEFAULT_TYPE, $page = self::DEFAULT_PAGE, $size = self::PAGE_SIZE)
    {
        $url = Comm_Config::get("api.getRankList");
        if ($url != "")
        {
            $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

            $platform->addRule('uid', "int", false);
            $platform->setValue('uid', $uid);

            $platform->addRule('type', "int", false);
            $platform->setValue('type', $type);

            $platform->addRule('page', "int", false);
            $platform->setValue('page', $page);

            $platform->addRule('size', "int", false);
            $platform->setValue('size', $size);

            //getResult 参数FALSE 为不解析返回资源
            $platform->getResult(false);

            $result = $platform->getHttpRequest()->responseContent;
        }

        return $result ? json_decode($result, true) : array();
    }

    /**
     * 获取企业用户信息并与接口数据合并
     * @param int $uid 当前登录用户uid
     * @param int $type 获取积分榜单信息类型：1昨日增长榜单，2总积分榜单
     * @param int $page 当前页页数
     * @return array
     * */
    public function getMergeRankList($uid, $type = self::DEFAULT_TYPE, $page = self::DEFAULT_PAGE)
    {
        $rankList = $this->getRankList($uid, $type, $page);

        if (empty($rankList) && $rankList["code"] != 100000)
        {
            //log interface error!
            return false;
        }
        $this->totalRecord = $rankList["data"]["totalRecord"];

        if (!empty($rankList["data"]["pageData"]))
        {
            foreach ($rankList["data"]["pageData"] as $k => $v)
            {
                $userIds[] = $v["uid"];
            }
        }

        if (!empty($userIds))
        {
            $drUser = new Dr_User();
            $userInfos = $drUser->getUserInfos($userIds, true);
        }
        else
        {
            //log none uids
        }
        //合并用户信息和积分等级信息
        if (!empty($rankList["data"]["pageData"]))
        {
            foreach ($rankList["data"]["pageData"] as $k => $v)
            {
                if (isset($userInfos[$v["uid"]]))
                {
                    $rankList["data"]["pageData"][$k]["order"] = $k + ($page - 1) * 10 + 1; //排名算法
                    $rankList["data"]["pageData"][$k]["userInfo"] = $userInfos[$v["uid"]];
                    $rankList["data"]["pageData"][$k]["userInfo"]["homeUrl"] = self::$domain . "/u/" . $v["uid"];
                }
            }
        }
        $rankList = $rankList["data"]["pageData"];
        return $rankList ? $rankList : array();
    }

    public function getPageSize()
    {
        return self::PAGE_SIZE;
    }

    public function getTotalRecord()
    {
        return !empty($this->totalRecord) ? $this->totalRecord : 0;
    }
}