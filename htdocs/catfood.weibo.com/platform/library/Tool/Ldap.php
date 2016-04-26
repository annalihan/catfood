<?php

class Tool_Ldap
{
    const LDAP_HOST = '10.210.97.21';
    const LDAP_PORT = '389';

    const COOKIE_LU = 'LU'; //LDAP user info
    const COOKIE_EXPIRE = 604800; //7 days
    const COOKIE_PATH = '/';
    const SECRET_KEY = 'SAAJcDZky0esBTCzBpvyRk74X81KUBtN';

    private static $_infoKeys = array(
        'un', //用戶名 
        'ui', //用戶信息，加密後的JSON串，包括密碼:用於SVN獲取、登錄時間等
        'es', //驗證信息
        'et', //過期時間
    );

    private static $_userInfo = null;
    private static $_domains = array(
        '.sina.com.cn',
        '.weibo.com',
        '.weibo.cn',
        '.sina.cn',
    );
 
    public static function logoutUser()
    {
        self::$_userInfo = null;
        $_COOKIE[self::COOKIE_LU] = 'deleted';

        foreach (self::$_domains as $domain)
        {
            setcookie(self::COOKIE_LU, 'deleted', 1, self::COOKIE_PATH, $domain);
        }

        return true;
    }

    public static function loginUser($userName, $password)
    {
        if ($userName == '')
        {
            return false;
        }

        $rl = ldap_connect(self::LDAP_HOST, self::LDAP_PORT);
        $login = @ldap_bind($rl, $userName, $password);
        ldap_close($rl);

        if ($login === false)
        {
            return false;
        }

        self::_setUserCookie($userName, $password);

        return self::getUser();
    }

    private static function _getFromCookie($cookie)
    {
        $userInfo = array();
        parse_str($cookie, $userInfo);

        // 无效
        foreach (self::$_infoKeys as $key)
        {
            if (isset($userInfo[$key]) === false)
            {
                return false;
            }
        }

        $expiration = $userInfo['et'];
        $userName = $userInfo['un'];
        $encryptString = $userInfo['es'];

        // 过期
        if ($expiration > 0 && $expiration < time())
        {
            return false;
        }

        if ($encryptString != self::_hash($userName, $expiration))
        {
            // 无效Cookie
            return false;
        }

        $ui = json_decode($userInfo['ui'], true);
        $userInfo['name'] = $userInfo['un'];
        $userInfo['password'] = self::_decrypt($ui['p']);
        $userInfo['id'] = substr($userInfo['un'], 0, strpos($userInfo['un'], '@'));

        return $userInfo;
    }

    public static function getUser()
    {
        if (self::$_userInfo)
        {
            return self::$_userInfo;
        }

        // Cookie不存在
        if (empty($_COOKIE[self::COOKIE_LU]))
        {
            return false;
        }

        self::$_userInfo = self::_getFromCookie($_COOKIE[self::COOKIE_LU]);
        
        return self::$_userInfo;
    }

    private static function _hash($userName, $expiration)
    {
        $key = hash_hmac('md5', $userName . $expiration, self::SECRET_KEY);
        return hash_hmac('md5', $userName . $expiration, $key);
    }

    private static function _encrypt($value)
    {
        if ($value == '')
        {
            return false;
        }

        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $cryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::SECRET_KEY, $value, MCRYPT_MODE_ECB, $iv);
        
        return trim(base64_encode($cryptText)); //encode for cookie
    }

    private static function _decrypt($value)
    {
        if ($value == '')
        {
            return false;
        }

        $cryptText = base64_decode($value); //decode cookie
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decrypText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::SECRET_KEY, $cryptText, MCRYPT_MODE_ECB, $iv);
        
        return trim($decrypText);
    }

    private static function _setUserCookie($userName, $password)
    {
        $createTime = time();
        $expiration = $createTime + self::COOKIE_EXPIRE;
        $userInfo = array(
            'p' => self::_encrypt($password),
            't' => $createTime,
        );

        $cookie = 'un=' . rawurlencode($userName);
        $cookie .= '&et=' . $expiration;
        $cookie .= '&es=' . self::_hash($userName, $expiration);
        $cookie .= '&ui=' . rawurlencode(json_encode($userInfo));

        foreach (self::$_domains as $domain)
        {
            setcookie(self::COOKIE_LU, $cookie, $expiration, self::COOKIE_PATH, $domain);
        }

        self::$_userInfo = self::_getFromCookie($cookie);

        return true;
    }
}