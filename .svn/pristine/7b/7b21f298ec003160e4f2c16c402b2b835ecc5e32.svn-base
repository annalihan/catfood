<?php
//TODO 专业版
//合并GetMsgCode和Msg
class Comm_Weibo_Message
{
    // 发送短信接口
    const MSG_URL_OUTER = "http://qxt.mobile.sina.cn/cgi-bin/qxt/sendSMS.cgi?msg=%s&usernumber=%s&count=%s&from=%s&longnum=%s";
    const MSG_URL_INNER = "http://qxt.intra.mobile.sina.cn/cgi-bin/qxt/sendSMS.cgi?msg=%s&usernumber=%s&count=%s&from=%s&longnum=%s";

    private static function _getRandomCode($len = 6, $format = 'NUMBER')
    {
        switch ($format)
        {
            case 'ALL' :
                $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
                break;

            case 'NUMBER' :
                $chars = '0123456789';
                break;

            default :
                $chars = '0123456789';
                break;
        }

        mt_srand((double)microtime() * 1000000 * getmypid());
        $randCode = "";
        
        while (strlen($randCode) < $len)
        {
            $randCode .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }

        return $randCode;
    }

    public static function sendCode($phoneNumber)
    {
        $code = self::_getRandomCode();
        $result = self::sendMessage($phoneNumber, $code);
        
        return array(
            'code' => $code,
            'result' => $result,
        );
    }

    /**
     * 发送短信内容
     * @param  [type]  $phoneNumber [description]
     * @param  [type]  $message     [description]
     * @param  integer $from        [description]
     * @param  integer $longNumber     [description]
     * @return [type]               [description]
     */
    public static function sendMessage($phoneNumber, $message, $from = 86680, $longNumber = 106903336612002)
    {
        if (empty($phoneNumber) || empty($message))
        {
            throw new Comm_Exception_Program('number or message empty');
        }

        $message = urlencode(mb_convert_encoding($message, "GB2312", "UTF-8"));
        $url = sprintf(self::MSG_URL_INNER, $message, $phoneNumber, 1, $from, $longNumber);
        $result = Tool_Http::get($url);

        Tool_Log::info("Send sms message to {$phoneNumber}, result: {$result}");

        return $result > 0;
    }
}
