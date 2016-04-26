<?php

class Do_Account extends Do_Abstract
{
    protected $props = array(
        // 上行
        'id' => array(
            'int',
            'min,1000;max,9999999999',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        // 4-20字符之间，只能中文和全角字符、字母、下划线(_)
        'screen_name' => array(
            'string',
            'width_min,2;width_max,20;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'name' => array(
            'string',
            'width_min,2;width_max,20;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'real_name_visible' => '', // 0-自己可见；1-我关注人可见；2-所有人可见
        'real_name_visible' => array(
            'enum',
            'enum,0,1,2',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'gender' => array(
            'enum',
            'enum,m,f',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'province' => array(
            'int',
            'min,1;max,100000',
            Comm_ArgChecker::OPT_USE_DEFAULT,
            Comm_ArgChecker::RIGHT,
            11
        ),
        'city' => array(
            'int',
            'min,1;max,100000',
            Comm_ArgChecker::OPT_USE_DEFAULT,
            Comm_ArgChecker::RIGHT,
            1
        ),
        'location' => '',
        'birthday' => '',
        'birthday_visible' => array(
            'enum',
            'enum,0,1,2,3', // 0-保密；1-只显示月日；2-只显示星座；3-所有人可见
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'qq' => '',
        'qq_visible' => array(
            'enum',
            'enum,0,1,2', // 0-自己可见；1-我关注人可见；2-所有人可见
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'msn' => '',
        'msn_visible' => array(
            'enum',
            'enum,0,1,2', // 0-自己可见；1-我关注人可见；2-所有人可见
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'description' => array(
            'string',
            'width_max,140',
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'url' => '',
        'url_visible' => array(
            'enum',
            'enum,0,1,2', // 0-自己可见；1-我关注人可见；2-所有人可见
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ),
        'email' => '',
        'email_visible' => array(
            'enum',
            'enum,0,1,2', // 0-自己可见；1-我关注人可见；2-所有人可见
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'credentials_type' => array(
            'enum',
            'enum,0,1,2,3,4', // 1-身份证；2-学生证；3-军官证；4-护照
            Comm_ArgChecker::OPT_NO_DEFAULT,
            Comm_ArgChecker::RIGHT
        ), 
        'credentials_num' => '',
        'created_at' => '',
        'lang' => '', // 语言设置
        'domain' => '', // 用户个性域名

        // 下行（包括上行所有）
        'real_name' => '',
        'profile_image_url' => '',
    );
    
    public function getLang()
    {
        return str_replace('_', '-', $this->getData('lang'));
    }
}