<?php
//TODO 专业版业务
class Comm_Weibo_Brand
{
    //汽车调用host
    const CAR_DOMAIN = "http://weidealer.auto.sina.com.cn";
    const CAR_DOMAIN1 = "http://data.auto.sina.com.cn";
    const APP_RRALM = "http://api.apps.sina.cn";
    const CAR_PHOTO = "http://photo.auto.sina.com.cn";

    //手机调用host
    const MOBILE_DOMAIN = "http://i.api.dp.sina.cn";

    const COMMENT_DOMAIN = "http://123.125.106.100";

    /**
     * 获取POST请求的响应内容
     * @param unknown_type $url
     * @param unknown_type $postdata
     * @param unknown_type $isRawUrl
     */
    public static function  fetchPostResponseResult($url, $postdata, $isRawUrl = FALSE)
    {
        $request = new Comm_HttpRequest();
        $request->setMethod('POST');
        if (is_array($postdata))
        {
            foreach ($postdata as $key => $value)
            {
                $request->addPostField($key, $value);
            }
        }
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

    /**
     * 获取点评
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getComments($key, $page = 1, $pagesize = 100)
    {
        if (empty($key))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $key = urlencode($key);
        $url = self::COMMENT_DOMAIN."/lib/libac8.php?key={$key}&cuid=0&sid=t_search&page={$page}&pagesize={$pagesize}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result == false)
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['sp'];
    }

    /**
     * 获取大品牌数据页面
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getBrandProfile($uid)
    {
        if (empty($uid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN1."/api/weibo/get_brand_profile.php?uid={$uid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['data'][0];
    }

    /**
     * 获取子品牌list页面
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getSubbrandList($uid)
    {
        if (empty($uid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN1."/api/weibo/get_subbrand_list.php?uid={$uid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['data'][0];
    }

    /**
     * 获取经销商list
     * @param unknown_type $subid 子品牌id
     * @param unknown_type $province 省份
     * @param unknown_type $page
     * @param unknown_type $limit
     * @throws Comm_Exception_Program
     */
    public static function getDealers($subid, $province = 11, $page = 1, $limit = 50)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $viewer = Comm_Context::get('viewer', false);
        $province = $viewer['province'];
        $url = self::CAR_DOMAIN."/api/weibo/subbrand_dealers.php?subid={$subid}&province={$province}&page={$page}&limit={$limit}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['data'];
    }

    /**
     * 获取大品牌经销商list
     * @param unknown_type $subid 子品牌id
     * @param unknown_type $province 省份
     * @param unknown_type $page
     * @param unknown_type $limit
     * @throws Comm_Exception_Program
     */
    public static function getDealersbybrand($bid, $province = 11, $page = 1, $limit = 50)
    {
        if (empty($bid) || !is_numeric($bid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $viewer = Comm_Context::get('viewer', false);
        $province = $viewer['province'];
        $url = self::CAR_DOMAIN."/api/weibo/brand_dealers.php?bid={$bid}&province={$province}&page={$page}&limit={$limit}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['data'];
    }

    /**
     * 获取经销商详情
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getDealerdetail($uid)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN."/api/weibo/dealer.php?uid={$uid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];
    }

    /**
     * 获取经销商主营子品牌
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getBrandlist($uid)
    {
        if (empty($uid) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN."/api/weibo/dealer_subbrand_price.php?uid={$uid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];
    }

    /**
     * 获取子品牌详情页
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getSubbrandProfile($subid)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN1."/api/weibo/get_subbrand_profile.php?subid={$subid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];
    }
    /**
     * 获取车型list
     * @param unknown_type $uid
     * @throws Comm_Exception_Program
     */
    public static function getCarList($subid)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = self::CAR_DOMAIN1."/api/weibo/get_car_list.php?subid={$subid}";
        Tool_Log::info("TEST" . $url);

        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];
    }

    /**
     * 获取相关汽车品牌推荐
     * @param $subid
     * @return unknown_type
     */
    public function getSubbrandRivalList($subid)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::CAR_DOMAIN1."/api/weibo/get_subbrand_rival_list.php?subid={$subid}";
        $response = Tool_Http::get($url);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];

    }

    /**
     * 获取图片及图解的信息
     * @param $subid
     * @param $type
     * @return unknown_type
     */
    public function getPhotoBySubidType($subid, $type = 1, $page = 1, $limit = 24)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::CAR_PHOTO."/interface/general/get_photo_by_subid_type.php?subid={$subid}&type={$type}&page={$page}&limit={$limit}&encode=utf-8&format=json";
        $response = file_get_contents($url);
        //$response = self::getResponseResult($url, TRUE);

        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['data'];

    }

    /**
     * 获取图片及图解的图片总数
     * @param $subid
     * @param $type
     * @return unknown_type
     */
    public function getPhotoCountBySubid($subid)
    {
        if (empty($subid) || !is_numeric($subid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::CAR_PHOTO."/interface/general/get_photo_count_by_subid.php?subid={$subid}";
        $response = file_get_contents($url);
        //$response = self::getResponseResult($url, TRUE);
        //Tool_Log::error("API_APP_ERROR" . $url.'|' .$response);
        $result = json_decode($response, true);

        if ($result['result'] != 'succ')
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result['data'];

    }

    //__________汽车end____________
    /**
     * 获取手机的热门产品
     * @param $num
     * @param $brand
     * @return unknown_type
     */
    public static function getHotProduct($brand, $page = 1, $num = 3, $w = 100, $h = 100)
    {
        if (empty($num) || !is_numeric($page))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $brand = urlencode($brand);
        $url = self::MOBILE_DOMAIN."/interface/i/mobile/hot_mobile.php?page={$page}&num={$num}&brand={$brand}&w={$w}&h={$h}";
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['status']['code'] != false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result;
    }

    /**
     * 获取手机的评论数
     * @param $num
     * @param $brand
     * @return unknown_type
     */
    public static function getMoblieComment($moblieId, $num = 1)
    {
        if (empty($num) || !is_numeric($num))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::MOBILE_DOMAIN."/interface/i/mobile/reviews.php?num={$num}&id={$moblieId}";
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['status']['code'] != false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result;
    }

    /**
     * 获取手机具体信息
     * @param $moblie_id
     * @return unknown_type
     */
    public static function getMoblieBaseInfo($moblieId)
    {
        if (empty($moblieId))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::MOBILE_DOMAIN."/interface/i/mobile/detail.php?id={$moblieId}";
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['status']['code'] != false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result;
    }

    /**
     * 手机图片接口
     * @param $moblie_id
     * @return unknown_type
     */
    public static function getMobliePic($moblieId, $page, $pagesize, $w = 100, $h = 100)
    {
        if (empty($moblieId))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::MOBILE_DOMAIN."/interface/i/mobile/images.php?id={$moblieId}&page={$page}&pagesize={$pagesize}&w={$w}&h={$h}";
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['status']['code'] != false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result;
    }

    /**
     * 获取推荐应用
     * @param $ua
     * @param $gsid
     * @return unknown_type
     */
    public static function getRecommendApp($ua, $gsid = '')
    {
        if (empty($ua))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = self::APP_RRALM."/interface/api_recommend.php?ua={$ua}&gsid={$gsid}";
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['result'] == false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return array();
        }
        return $result;
    }
    /**
     * 裁剪图片
     * @param $ua
     * @param $gsid
     * @return unknown_type
     */
    public static function imgResize($imgUrl, $h = 100, $w = 100, $flag = 1)
    {
        if (empty($imgUrl))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }
        $url = "http://i.api.place.weibo.cn/resizeImge.php";
        $data = array(
            'url' => $imgUrl,
            'height' => $h,
            'width' => $w,
            'flag' => $flag,
        );
        $response = Tool_Http::post($url, $data);
        $result = json_decode($response, true);
        if ($result['original_pic'] == false)
        {
            Tool_Log::error("API_APP_ERROR" . $url.'|'.$response);
            return false;
        }
        return $result['original_pic'];
    }

}
