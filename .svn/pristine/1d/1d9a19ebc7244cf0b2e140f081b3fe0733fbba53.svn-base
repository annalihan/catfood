<?php
/**
 * 用户认证
 * @package Core
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2014-06-04
 */
class Core_Authorize
{
    public static $viewerId = 0;
    public static $viewer = false;

    public static function getViewer($mustCheckSession = false)
    {
        $viewerInfo = Comm_Context::get('viewer', false);
        if ($viewerInfo)
        {
            return $viewerInfo;
        }

        try
        {
            $ssoInfo = Comm_Weibo_SinaSSO::getUserInfo($mustCheckSession);
            Comm_Context::set('sso_info', $ssoInfo, array(), true);
        }
        catch (Exception $e)
        {
            throw new Core_Exception_Authorize($e->getMessage(), Core_Exception_Authorize::CODE_SSO);
        }

        self::$viewerId = $ssoInfo['uid'];

        self::$viewer = Dr_User::getUserInfo(self::$viewerId);

        if (self::$viewer)
        {
            Comm_Context::set('viewer', self::$viewer, array(), true);
        }

        return self::$viewer;
    }

    public static function getOwner()
    {
        $owner = false;
        $ownerId = Comm_Context::param('uid', 0);
        $ownerDomain = Comm_Context::param('domain');
        $ownerNick = Comm_Context::param('nick');
        $weihao = self::_getWeihao();

        //传递的uid或domain为登录用户的时，owner即为viewer
        if (false !== self::$viewer && !$weihao)
        {
            if (self::$viewer->id == $ownerId || self::$viewer->domain == $ownerDomain) 
            {
                Comm_Context::set('owner', self::$viewer, array(), true);
                return self::$viewer;
            }
        }

        //以domain取owner
        if ($ownerDomain !== null)
        {
            $owner = Dr_User::getUserInfoByDomain($ownerDomain);
            Comm_Context::set('owner', $owner, array(), true);
            return $owner;
        }

        //以微号取owner
        if ($weihao)
        {
            $owner = Dr_User::getUserInfoByDomain($weihao);
            if ($owner['weihao'] == $weihao)
            {
                Comm_Context::set('owner', $owner, array(), true);
                return $owner;
            }
        }

        //以uid取owner
        if (0 !== $ownerId)
        {
            try
            {
                $ownerInfo = Dr_User::getUserInfo($ownerId);
                Comm_Context::set('owner', $owner, array(), true);
                return $owner;
            }
            catch (Comm_Exception_Program $e)
            {
                $ownerInfo = Dr_User::getUserInfo($ownerId);
                Comm_Context::set('owner', $owner, array(), true);
                return $owner;
            }
        }
        
        //以昵称取owner
        if (null != $ownerNick)
        {
            try
            {
                $owner = Dr_User::getUserInfoByScreenName($ownerNick);
                Comm_Context::set('owner', $owner, array(), true);
                return $owner;
            }
            catch (Exception $e)
            {
                throw new Core_Exception_Authorize($e->getMessage(), Core_Exception_Authorize::CODE_NICKNAME);
            }
        }

        //未传递uid和domain时，owner等于viewer
        if ($owner)
        {
            Comm_Context::set('owner', $owner, array(), true);
            return $owner;    
        }

        return false;
    }

    private static function _getWeihao()
    {
        $scriptUrl = Comm_Context::getServer('SCRIPT_URL');
        $weihao = 0;
        $matches = array();
        if (preg_match("/^\/(\d{5,10})\/?$/", $scriptUrl, $matches))
        {
            //数字小于10位，或者以4和8开头的为微号
            if ((is_numeric($matches[1]) && strlen($matches[1]) < 10) || in_array($matches[1][0], array('4', '8')))
            {
                $weihao = $matches[1];
            }
        }

        return $weihao;
    }
}