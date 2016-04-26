<?php
class AuthorizePlugin extends Yaf_Plugin_Abstract
{
    public $viewerRequire = false;
    public $viewerInfo = null;
    public $viewerUserId = null;
    public $mustCheckSession = false;

    private $_scriptUrl = '';
    private $_reservedUrlList = array(
        '/unfreeze' => 1, 
        '/aj/mobile/unfreeze' => 1, 
        '/aj/user/checkstatus' => 1,
    );

    private $_blockCodes = array(
        '10025' => true, 
        '20003' => true, 
        '20401' => true
    );

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $this->_scriptUrl = Comm_Context::getServer('SCRIPT_URL');
        $controllerName = $request->getControllerName();
        if (strpos($controllerName, 'I_') === 0)
        {
            $controllerName = 'Interface_' . substr($controllerName, 2);
        }

        $properties = get_class_vars($controllerName . "Controller");
        $authorizeType = isset($properties["authorizeType"]) ? $properties["authorizeType"] : AbstractController::MAYBE_LOGIN;
        $this->viewerRequire = ($authorizeType === AbstractController::MUST_LOGIN);

        //关键操作要验证用户的session
        $this->mustCheckSession = !empty($properties["checkSession"]);

        //初始化用户
        if ($authorizeType !== AbstractController::NOT_LOGIN)
        {
            $this->initViewer();
        }

        //设定语言环境
        $lang = $this->viewerInfo ? $this->viewerInfo->lang : Comm_Context::param('lang', 'zh-cn', true);
        Comm_I18n::setCurrentLang($lang);

        //防止冻结+强制改密码用户重定向
        if (isset($this->_reservedUrlList[$this->_scriptUrl]) === false)
        {
            //用户是否跳转修改密码页
            $this->_checkUserPasswordPage();
        }

        if (!empty($properties["initOwner"]))
        {
            if ($this->viewerRequire == false && $this->viewerInfo == false)
            {
                //未登录标识
                Comm_Context::set('UNLOGIN_ACCESS', true);
            }

            $this->initOwner();
        }
    }
    
    public function initViewer()
    {
        try
        {
            $this->viewerInfo = Core_Authorize::getViewer($this->mustCheckSession);
        } 
        catch (Core_Exception_Authorize $e) 
        {
            $code = $e->getCode();
            if ($code == Core_Exception_Authorize::CODE_SSO)
            {
                if ($this->viewerRequire)
                {
                    Tool_Redirect::unlogin();
                }
                else 
                {
                    return;
                }
            }
        }
        catch (Comm_Exception_Program $e) 
        {
            Core_Debug::error('Cannt get viewer(PRO)', $e->getMessage());
            $this->_dealViewerInfoFailed($e);
        }
        catch (Exception $e) 
        {
            Core_Debug::error('Cannt get viewer', $e->getMessage());
        }

        $this->_checkUserLossPage();
    }

    public function initOwner()
    {
        try
        {
            $owner = Core_Authorize::getOwner();
            $this->_dealUserType($owner);
        } 
        catch (Core_Exception_Authorize $e) 
        {
            $code = $e->getCode();
            if ($code == Core_Exception_Authorize::CODE_NICKNAME)
            {
                $ownerNick = Comm_Context::param('nick');
                $searchUrl = Comm_Config::get('domain.search') . '/user/' . urlencode($ownerNick) . '&Refer=at';
                header('Location:' . $searchUrl); 
                exit;
            }
        }
        catch (Comm_Exception_Program $e) 
        {
            Core_Debug::error('Cannt get owner(PRO)', $e->getMessage());
            $this->_dealUserException($e);
        }
        catch (Exception $e) 
        {
            Core_Debug::error('Cannt get owner', $e->getMessage());
        }
    }

    /**
     * 强制出现安全问题的用户跳转修改密码页
     * @return [type] [description]
     */
    private function _checkUserPasswordPage()
    {
        $ssoInfo = '';
        $ssoInfo = Comm_Context::cookie('SUP');
        if (empty($ssoInfo))
        {
            return;
        }

        $matches = array();
        $matchResult = preg_match('/\bfmp\b=(.*?)&/i', $ssoInfo, $matches);
        if ($matchResult === false && isset($matches[1]) === false)
        {
            return;
        }

        $viewer = Comm_Context::get('viewer', false);
        if (isset($viewer->id) === false)
        {
            return;
        }

        $userId = $viewer->id;
        if ($userId == 0 || $matches[1] != '1')
        {
            return;
        }

        $sso = Comm_Weibo_SinaSSO::getUserInfoBySSO($userId);
        if (isset($sso['status']) && (int)($sso['status']) === 222)
        {
            //TODO:作用未知，先关闭
            /*
            //该部分用户不再走安全引导页检查
            $extendInfo = Dr_UserExtend::getExtend($userId, Dr_UserExtend::TYPE_SECURITY_REDIRECT_FLG);
            if ($extendInfo !== Dr_Userextend::VALUE_SECURITY_REDIRECT_FLG)
            {
                Dw_UserExtend::setExtend($userId, Dr_UserExtend::TYPE_SECURITY_REDIRECT_FLG, Dr_UserExtend::VALUE_SECURITY_REDIRECT_FLG);
            }*/

            header('Location: http://account.weibo.com/settings/password?force=' . $userId);
            exit();
        }
    }
    
    /**
     * 检查用户是是否在被盗用户黑名单中
     * @return [type] [description]
     */
    private function _checkUserLossPage()
    {
        $viewerUserId = Core_Authorize::$viewerId;
        $stolenFile = Comm_Config::get("env.privdata_dir_referer") . "monitor/stolen_uid.txt";
        if (file_exists($stolenFile) === false)
        {
            return;
        }

        $fp = fopen($stolenFile, "r");
        if ($fp === false)
        {
            return;
        }
        
        $blackUserList = array();
        while (($line = @fgets($fp, 4096)) !== false)
        {
            $line = trim($line);
            if (empty($line))
            {
                continue;
            }

            if (strpos($line, '#') === 0)
            {
                continue;
            }

            if (preg_match("/^(\d+),(\d+)$/", $line, $matches))
            {
                $blackUserList[$matches[1]] = $matches[2];
                continue;
            }
        }

        if (empty($blackUserList[$viewerUserId]))
        {
            return;
        }

        $blackTime = $blackUserList[$viewerUserId];
        $ssoInfo = Comm_Weibo_SinaSSO::getUserInfo();
        if (isset($ssoInfo['bt']) && $ssoInfo['bt'] < $blackTime)
        {
            $url = Comm_Config::get('domain.weibo') . '/logout.php?backurl=/';
            header('Location: ' . $url);
            exit();
        }
    }
    
    /**
     * 获取用户信息异常处理
     * @param Comm_Exception_Program $e
     */
    private function _dealViewerInfoFailed($e)
    {
        $dealResult = false;  //跳出处理异常标识

        //用户存在
        try
        {
            $viewerType = Dr_User::getUserType(Core_Authorize::$viewerId);
            $state = isset($viewerType['level']) ? $viewerType['level'] : Do_User::STATE_BLOCK;
            $dealResult = $this->_dealLoginUserState($state);
        } 
        catch (Comm_Exception_Program $e) 
        {
            if ($e->getCode() == '10013' || $e->getCode() == '20003')
            {
                //检测用户信息不完整
                Tool_Redirect::fullInfo();
            }
            else
            {
                $this->_dealSystemBusy($e);
                $dealResult = $this->_dealLoginUserState(Do_User::STATE_BLOCK);
            }
        }

        //处理系统繁忙异常
        $this->_dealSystemBusy($e);
        if ($dealResult === false)
        {
            //未处理用户信息异常
            $this->_dealLoginUserState(Do_User::STATE_BLOCK);
        }
    }
    
    /**
     * 系统繁忙处理
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    private function _dealSystemBusy($e)
    {
        if (0 == $e->getCode() || strstr($e->getMessage(), 'timed out'))
        {
            Tool_Redirect::systemBusy();
        }
    }
    
    /**
     * 处理用户类型
     * @param int $state
     */
    private function _dealLoginUserState($state)
    {
        $dealResult = false;  //跳出处理异常标识

        switch ($state)
        {
            case Do_User::STATE_FREEZE:
                if (isset($this->_reservedUrlList[$this->_scriptUrl]) === false)
                {
                    Tool_Redirect::pageUnfreeze();
                }

                $dealResult = true;
                //冻结页需要返回的用户信息
                $info = array(
                    'id' => Core_Authorize::$viewerId, 
                    'name' => Core_Authorize::$viewerId, 
                    'screen_name' => Core_Authorize::$viewerId, 
                    'lang' => 'zh-cn', 
                    'gender' => 'm',
                );
                $this->viewerInfo = new Do_User($info, Do_Abstract::MODE_OUTPUT);
                Comm_Context::set('viewer_user_id', Core_Authorize::$viewerId);
                break;

            case Do_User::STATE_BLOCK:  //同default
            default:
                //防止系统出错而出现的死循环跳转
                if ('/sorry' != $this->_scriptUrl) 
                {
                    Tool_Redirect::userBlock();
                }

                $dealResult = true;
                break;
        }

        return $dealResult;
    }

    /**
     * 处理用户信息获取异常
     * @param Exception $e
     */
    private function _dealUserException(Exception $e)
    {
        $errorCode = $e->getCode();

        if (isset($this->_blockCodes[$errorCode]))
        {
            Tool_Redirect::userBlock(true);
        }

        Tool_Redirect::userNotExists();
    }
    
    /**
     * 处理用户类型
     * @param Do_User $owner
     */
    private function _dealUserType($user, $isSelf = false)
    {
        if (isset($user['type']) == false)
        {
            return;
        }
        
        switch ($user['type'])
        {
            case Do_User::STATE_BLOCK:
            case Do_User::STATE_FREEZE:
                Tool_Redirect::userBlock(!$isSelf);
                break;
        }
    }
}
