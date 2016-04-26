<?php

class Tool_Meta
{
    const DOMAIN_V4 = 4;
    const DOMAIN_V5 = 5;

    public static function getConfigMeta($domainVer = self::DOMAIN_V5)
    {
        // 4:v4版 5:v5版
        if (self::DOMAIN_V4 != $domainVer && self::DOMAIN_V5 != $domainVer)
        {
            $domainVer = self::DOMAIN_V5;
        }

        $viewer = Comm_Context::get('viewer', false);
        $owner = Comm_Context::get('owner', false);
        if (false == $owner)
        {
            $owner = $viewer;
        }

        if ($viewer)
        {
            try
            {
                $viewerPrivacy = Dr_Privacy::getPrivacy();
                $showWebIM = (isset($viewerPrivacy['webim']) && $viewerPrivacy['webim'] == 0) ? 0 : 1;
                if (!isset($viewer['level']))
                {
                    $showWebIM = 0;
                }
            }
            catch (Exception $e)
            {
                $showWebIM = 0;
            }
        }
        else
        {
            $showWebIM = 0;
        }
        
        //TODO
        //企业用户引导
        /*$openEnterprise = Dr_UserExtend::VALUE_ENTERPRISE_OPEN;
        $closeEnterprise = Dr_UserExtend::VALUE_ENTERPRISE_CLOSE;
        $enterprise = $closeEnterprise;
        if (isset($viewer->verified_type) && $viewer->verified_type > 0 && $viewer->verified_type <= 8)
        {
            $viewerRegTime = time() - strtotime(Comm_Context::get("viewer")->created_at);
            
            if ($viewerRegTime < 604800 && $viewerRegTime > 0)
            {
                $enterprise = Dr_UserExtend::getExtend($viewer->id, Dr_UserExtend::TYPE_ENTERPRISE_LAYER);
                if ($enterprise != $closeEnterprise)
                {
                    $enterprise = $openEnterprise;
                    //TODO
                    //Dw_UserExtend::setExtend($viewer->id, Dr_UserExtend::TYPE_ENTERPRISE_LAYER, $closeEnterprise);
                }
            }
        }
        $enterprise = $enterprise == $openEnterprise ? "publisher" : '';
        */

        //TODO 需要在其他地方设定enterprise这个值，比如现有的doorgod
        $enterprise = Comm_Context::get("enterprise", '');

        //根据语言类型得到需要加载的皮肤css文件名
        $skinCss = ($viewer && $viewer->lang == 'zh-tw') ? 'skin_narrow_CHT.css' : 'skin_narrow.css';

        //TODO 确认是否都是V5
        $verSuffix = self::DOMAIN_V5 == $domainVer ? '_v5' : '';
        $gJsDomain = Comm_Config::get("env.js_domain{$verSuffix}");
        $gCssDomain = Comm_Config::get("env.css_domain{$verSuffix}");
        $gImgDomain = Comm_Config::get("env.img_domain{$verSuffix}");
        $gSkinDomain = Comm_Config::get("env.skin_domain{$verSuffix}");
        
        //TODO
        $brandOpen = 0;
        /*$iconBrand = Comm_Config::get("icon_brand");
        if (!empty($iconBrand) && isset($iconBrand['is_open']))
        {
            $brandOpen = $iconBrand['is_open'];
        }*/
        
        return array(
            'g_skin_id'            => Comm_Context::get("g_skin_id", Comm_Config::get("skin_misc.default_skinid")), 
            'g_customize_skin'     => Comm_Context::get("g_customize_skin", ''), 
            'g_background'         => Comm_Context::get("g_background", ''), 
            'g_scheme'             => Comm_Context::get("g_scheme", ''), 
            'g_colors_type'        => Comm_Context::get("g_colors_type", 0), 
            'g_lang'               => Comm_I18n::getCurrentLang(), 
            'g_owner'              => $owner, 
            'g_viewer'             => $viewer, 
            'g_domain'             => Comm_Config::get('domain.weibo'), 
            'g_js_domain'          => $gJsDomain, 
            'g_search_domain'      => Comm_Config::get('domain.search'), 
            'g_css_domain'         => $gCssDomain,
            'g_img_domain'         => $gImgDomain, 
            'g_skin_domain'        => $gSkinDomain,  
            'g_detect_special_css' => Comm_Weibo_Util::detectSpecialCss(), 
            'g_wvr'                => Tool_Helper::showWvr(), 
            'g_global_title'       => Comm_I18n::get('微博-随时随地分享身边的新鲜事儿'),
            'g_show_webim'         => $showWebIM, 
            'g_new_user'           => Comm_Context::get('new_user', false), 
            'g_version_js'         => Tool_Misc::homesiteJsVersion(), 
            'g_version_css'        => Tool_Misc::homesiteCssVersion(),
            'g_enterprise'         => $enterprise, 
            'g_show_en_lang'       => Comm_Context::get('show_en_lang', 1), 
            'g_skin_css'           => $skinCss, 
            'g_is_miyou'           => Comm_Context::get("is_miyou", false), 
            'g_request_ua'         => htmlspecialchars(Comm_ClientProber::getAgentString(), ENT_QUOTES),
            'g_recfeed'            => 0,
            'g_brand'              => $brandOpen,
            'g_current_bp_mode'    => 0,
            'g_page_id'            => '',
            'g_bp_type'            => '',
            'g_domain_ver'         => $domainVer,
        );
    }
}