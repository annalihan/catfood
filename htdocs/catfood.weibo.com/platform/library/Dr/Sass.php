<?php
class Dr_Sass extends Dr_Abstract
{
    private static $_sassErrorCodes = array(
        '-1' => '20014',
        '-2' => '20015',
        '-3' => '20016',
        '-4' => '20017',
        '-5' => '20018',
        '-6' => '20019',
        '-7' => '20020',
        '-8' => '20021',
        '-9' => '20022',
        '3'  => '20023',
        '4'  => '20031',
        '5'  => '20032',
        '7'  => '20033',
    );

    //以下特殊返回码也不重写MCQ
    private static $_sendErrorCodes = array(
        '10012', //非法请求
        '20012', //输入文字太长，请确认不超过140个字符
        '20001', //IDs参数为空
        '20008', //内容为空
        '20005', //不支持的图片类型，仅仅支持JPG、GIF、PNG
        '10006', //缺少source (appkey) 参数
        '10004', //IP限制不能请求该资源
        '10022', //IP请求频次超过上限
        '10023', //用户请求频次超过上限
        '10024', //用户请求特殊接口 (%s) 频次超过上限
        '20034', //只读用户
        '20035', //未实名认证
        '20044', //发布成功，发布器出蒙层
        '20045', //发布失败，发布器出蒙层
        '20131', //转发恶意链接
    );
    
    public static function getSassCode()
    {
        return self::$_sassErrorCodes;
    }
    
    public static function getNoSendMcqCode()
    {
        $code = array_merge(self::$_sassErrorCodes, self::$_sendErrorCodes);
        return $code;
    }

    /**
     * 检测发微博
     * 
     * @param int $count
     * @param 
     * @return Dr_Status
     */
    public static function checkMblog($status, $rMblog = array(), $filter = 0, $token = '')
    {
        try
        {
            //获取接口需要数据
            $uid = Comm_Context::get('viewer')->id;
            $content = $status->text;
            $repostStatusId = $status->repost_status_id;
            if ($uid == '' || $content == '')
            {
                return false;
            }

            $rootContent = '';
            $rootUid = $rootUsertype = $rootUserlevel = false;
            if ($repostStatusId)
            {
                if (empty($rMblog))
                {
                    $rMblog = Dr_Status::getMblogs(array($repostStatusId));
                }

                if (isset($rMblog[$repostStatusId]))
                {
                    $ropostMblog = $rMblog[$repostStatusId];
                    if (isset($ropostMblog['retweeted_status']))
                    {
                        $ropostMblog = $ropostMblog['retweeted_status'];
                    }

                    $rootUid = $ropostMblog['uid'];
                    $rootMid = $ropostMblog['mid'];
                    $rootContent = strip_tags($ropostMblog['text']);
                    $rootUsertype = isset($ropostMblog['user']['type']) && !empty($ropostMblog['user']['type']) ? $ropostMblog['user']['type'] : 1;
                    $rootUserlevel = $ropostMblog['user']['level'];
                    $rootstate = isset($ropostMblog['state']) ? $ropostMblog['state'] : 0;
                }
            }

            $usertype = isset(Comm_Context::get('viewer')->type) && !empty(Comm_Context::get('viewer')->type) ? Comm_Context::get('viewer')->type : 1;
            $userlevel = Comm_Context::get('viewer')->level;
            $createdAtTimestamp = strtotime(Comm_Context::get('viewer')->created_at);
            $regTime = date("Y-m-d H:i:s", $createdAtTimestamp);
            $ip = Comm_Context::getClientIp();
            $rootMid = empty($rootMid) ? $repostStatusId : $rootMid;
            
            //获取手机绑定
            $uids[] = intval($uid);
            if ($rootUid)
            {
                $uids[] = $rootUid;
            }

            $mobileInfo = Dr_Account::getMobileBatch($uids);
            $bindMobileInfo = array();
            foreach ($uids as $id)
            {
                $bindMobileInfo[$id] = isset($mobileInfo[$id]) ? $mobileInfo[$id]['binding'] : false;
            }

            //拼装接口参数
            $sassCheckData = array(
                'uid' => $uid,
                'content' => $content,
                'appid' => 174,
                'usertype' => $usertype,
                'filter' => $filter,
                'ip' => array($ip),
                'userlevel' => $userlevel,
                'mobile' => $bindMobileInfo[$uid],
                'reg_time' => $regTime
            );

            if (!empty($rootContent))
            {
                $sassCheckData['fwdinfo']['content'] = $rootContent;
                $sassCheckData['fwdinfo']['mid'] = $rootMid;
            }

            if (!empty($rootUid))
            {
                $sassCheckData['fwdinfo']['uid'] = $rootUid;
                $sassCheckData['fwdinfo']['mobile'] = $bindMobileInfo[$rootUid];
                $sassCheckData['fwdinfo']['status'] = $rootstate;
            }

            if ($rootUserlevel !== false)
            {
                $sassCheckData['fwdinfo']['userlevel'] = $rootUserlevel;
            }

            if ($rootUsertype !== false)
            {
                $sassCheckData['fwdinfo']['usertype'] = $rootUsertype;
            }

            $sassCheckData['userinfo'] = array(
                'province' => Comm_Context::get('viewer')->province,
                'city' => Comm_Context::get('viewer')->city
            );
            
            //sass检测接口调用
            $sassApi = Comm_Weibo_Api_Sass::checkMblog();
            $sassApi->s = 3;
            $sassApi->t = 1;
            $sassApi->a = urlencode(json_encode($sassCheckData));
            $re = $sassApi->getResult();
            
            if (isset($re['errno']))
            {
                $code = $re['errno'];
                if (isset(self::$_sassErrorCodes[$code]))
                {
                    return self::$_sassErrorCodes[$code];
                }
            }

            return true;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            //throw $e;
            return false;
        }
    }

    /**
     * 获取用户是否通过身份验证
     * @param int $uid
     * @return 0 未通过 , 1 通过, 2不需验证用户, 3接口异常
     */
    public static function retrieveVerify($uid)
    {
        try
        {
            $sassApi = Comm_Weibo_Api_Sass::retrieveVerify();
            $sassApi->u = $uid;
            $re = $sassApi->getResult();
            $verifyState = $re['errno'];
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        return $verifyState;
    }
}
