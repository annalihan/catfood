<?php
class Do_Message extends  Do_Abstract
{
    protected $props = array(
        //下行
        'id' => '', //私信ID
        'idstr' => '',
        'created_at' => '', //发送时间
        'sender_id' => '', //发送人UID
        'recipient_id' => '', //接受人UID
        'sender_screen_name' => '', //发送人昵称
        'recipient_screen_name' => '', //接受人昵称
        'sender' => '', //发送人信息
        'recipient' => '', //接受人信息
        'is_read' => '',
        'mid' => '',
        'status_id' => '',
        //上行
        'fids' => '', //需要发送的附件ID
        'status_name' => array(
            'string',
            'width_min,1;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        'uid' => array(
            'int',
            'min,1;',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        'id' => array(
            'int',
            'min,1;',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        'text' => array(
            'string',
            'width_min,1;width_max,600',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
    );
}