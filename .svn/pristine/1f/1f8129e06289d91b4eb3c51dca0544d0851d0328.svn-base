<?php
require_once T3P_PATH . '/pin/pincode.php';
class Tool_Analyze_SaasCheck
{
    private static $_errorCode = array(
        '20015' => true,
        '20016' => true,
        '20017' => true,
        '20018' => true,
        '20019' => true,
        '20020' => true,
        '20021' => true,
        '20022' => true,
        '20023' => true,
        '20031' => true, //验证码错误
        '20507' => true, //验证码错误
        '20033' => true, //异地登陆验证码
        '20032' => true,
        '20308' => true, //你今天给太多人发过私信了，休息一下吧~
        '20034' => true, //只读用户
        '20035' => true, //未实名认证
    );
    
    /**
     * 根据code返回对应的saas错误信息
     * @param unknown_type $code
     */
    public static function getErrInfo($code)
    {
        $errInfo = array();
        if (isset(self::$_errorCode[$code]))
        {
            if ('20031' == $code || '20507' == $code || '20033' == $code)
            {
                $errInfo['msg'] = '';
                $errInfo['riacode'] = Comm_Config::get('riacode.sass');
            }
            elseif ('20035' == $code || '20034' == $code)
            { 
                $errInfo['msg'] = Comm_I18n::get('ajax.saas.saas_error_' . $code);
                $errInfo['riacode'] = Comm_Config::get('riacode.user_read_error_' . $code);
                $errInfo['data'] = array(
                    'location' => Comm_I18n::get('ajax.saas.location_' . $code), 
                    'OKText' => Comm_I18n::get('ajax.saas.OKText_' . $code), 
                    'cancelText' => Comm_I18n::get('ajax.saas.cancelText_' . $code), 
                    'OKSuda' => Comm_I18n::get('ajax.saas.OKSuda_' . $code), 
                    'cancelSuda' => Comm_I18n::get('ajax.saas.cancelSuda_' . $code),
                );
                
                if ('20035' == $code)
                {
                    //身份验证弹层改造，只输出错误码100004
                    $errInfo['msg'] = '';
                    $errInfo['data'] = array();
                }
            }
            else
            {
                $errInfo['msg'] = Comm_I18n::get('ajax.saas.saas_error_' . $code);
                $errInfo['riacode'] = Comm_Config::get('riacode.error');
            }
        }

        return $errInfo;
    }

    public static function checkRetcode($retcode)
    {
        $userObj = Comm_Context::get('viewer', false);
        if ($userObj !== false)
        {
            $uid = $userObj->id;
        }

        $objpincode = new PinStore();
        $codeName = $objpincode->get_pincodekey_from_mc($uid);
        $key = md5($uid . $codeName);
        
        return $key == $retcode;
    }
}
