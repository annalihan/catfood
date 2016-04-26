<?php
/**
 * TAuth2认证方法
 *
 * 获取token的参数从配置文件tauth里读取，包括source、ips和ttl三个参数
 * 
 * @author 罗圆 <luoyuan@staff.sina.com.cn>
 * @version 2.0 2014-04-29
 */
class Tool_TAuth2
{
    private static $_tokenServer = 'http://i2.api.weibo.com/tauth2/access_token.json';
    
    private static function _getToken()
    {
        try 
        {
            $source = Comm_Config::get("tauth.source", 3000312714);
            $tokenFile = Comm_Context::getServer('SINASRV_PRIVDATA_DIR') . '/tauth2/' . $source . '.txt';
            if (file_exists($tokenFile))
            {
                $token = file_get_contents($tokenFile);

                if (!empty($token))
                {
                    $token = json_decode($token, true);
                    if (isset($token['expire']) && ($token['expire'] > time()))
                    {
                        return $token;
                    }
                }
            }            

            $ips = Comm_Config::get("tauth.ips", "10.75,172.16,10.73");
            $ttl = Comm_Config::get("tauth.ttl", 172800);
            $data = array(
                'ips' => $ips,
                'source' => $source,
                'ttl' => $ttl,
            );
            $token = Tool_Http::callServiceByUrl(self::$_tokenServer, $data);

            if (!isset($token['tauth_token']))
            {
                Tool_Log::error('TAuth2 update error: response=' . json_encode($token));
                return false;
            }
            
            Tool_Log::info('TAuth2 update success: token=' . json_encode($token));
            $token['expire'] = time() + 86400; //机器上保存token的时间为一天
            
            //token保存到文件
            if (is_dir(dirname($tokenFile)) === false)
            {
                mkdir(dirname($tokenFile), 0777, true);
            }
            file_put_contents($tokenFile, json_encode($token));

            return $token;
        } 
        catch (Comm_Exception_Program $e) 
        {
            Tool_Log::error('TAuth2 error:' . $e->getMessage());
            return false;
        }
    }

    /**
     * 使用token认证时传递过去的header字符串
     * @param unknown $uid  必须的uid，可以为当前登录者
     * @param unknown $token  get_token返回值
     * @return string
     */
    private static function _getTAuth2HeaderOfUid($uid, $token)
    {
        $param = 'uid=' . $uid;
        $sign = self::_getSignature($param, $token['tauth_token_secret']);
        $encodeToken = 'token="' . urlencode($token['tauth_token']) . '"';
        $encodeParam =  'param="' . urlencode($param) . '"';
        $encodeSign = 'sign="' . urlencode($sign) . '"';

        return "TAuth2 {$encodeToken},{$encodeParam},{$encodeSign}";
    }

    private static function _getSignature($str, $key)
    {
        $signature = "";
        if (function_exists('hash_hmac'))
        {
            $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
        }
        else
        {
            $blocksize = 64;
            if (strlen($key) > $blocksize)
            {
                $key = pack('H*', sha1($key));
            }

            $key  = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack('H*', sha1(($key ^ $ipad) . $str));
            $hmac = pack('H*', sha1(($key ^ $opad) . $hmac));

            $signature = base64_encode($hmac);
        }
        return $signature;
    }

    public static function getTAuth2Header($uid)
    {
        $token = self::_getToken();
        if ($token == false)
        {
            return false;
        }

        return self::_getTAuth2HeaderOfUid($uid, $token);
    }
}
