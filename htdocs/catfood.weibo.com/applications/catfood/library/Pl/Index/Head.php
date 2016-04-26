<?php

class Pl_Index_Head extends Pl_Abstract
{
    public $name = 'pl_index_head';

    public $tpl = 'index/pl_index_head.phtml';

    public function prepareData()
    {

        return array();
    }
    public function getPageMetaData(){
        $viewer = Comm_Context::get('viewer');
        $uid = $viewer['id'];
        $ret = Dr_User::getUserInfo($uid);
        $api = Comm_Weibo_Api_Account::getProfileEducation();
        $api->setValue('uid', $uid);
        $school_info= $api->getResult();
        $api = Comm_Weibo_Api_Account::getProfileCareer();
        $api->setValue('uid', $uid);
        $career_info= $api->getResult();
        $api = Comm_Weibo_Api_Account::getProfileBasic();
        $api->setValue('uid', $uid);
        $bir= $api->getResult();
        $data['user'] = $ret;
        $data['bir']=$bir;
        $data['career_info']=$career_info[0];
        $data['school_info']=$school_info[0];
//        var_dump($data['user']);
        return $data;
    }
}