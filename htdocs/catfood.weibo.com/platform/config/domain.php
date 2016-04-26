<?php
/**
 * 新浪产品相关域名定义
 * 
 * 模板中使用方法 <?=Tool_Helper:conf('weibo.xxx')?>
 */

$weiboDomain = Comm_Context::getServer('SINASRV_DOMAIN', 'weibo.com');

return array(
    //新浪微博
    'weibo' => 'http://' . $weiboDomain,
    'weibo_bak' => 'http://weibo.com',
    'search' => 'http://s.weibo.com',
    //同城
    'city' => 'http://city.weibo.com',
    //微群
    'qun'   => 'http://q.weibo.com',
    //游戏
    'games' => 'http://game.weibo.com',
    //相册
    'photo' => 'http://photo.weibo.com',
    //音乐
    'music' => 'http://music.weibo.com',
    //活动
    'event' => 'http://event.weibo.com',
    //投票
    'vote' => 'http://vote.weibo.com',
    //微直播
    'live' => 'http://live.weibo.com',
    //微电台
    'radio' => 'http://radio.weibo.com',
    //微访谈
    'talk' => 'http://talk.weibo.com',
    //名人堂
    'pub_star' => 'http://' . $weiboDomain . '/pub/star',
    //风云榜
    'pub_top' => 'http://' . $weiboDomain . '/pub/top',
    //微话题
    'pub_topic' => 'http://' . $weiboDomain . '/pub/topic',
    //勋章馆
    'badge' => 'http://badge.weibo.com',
    //微数据
    'mic_data' => 'http://data.weibo.com',
    'mic_disk' => 'http://vdisk.weibo.com',
    //微女郎
    'vgirl' => 'http://vgirl.weibo.com',
    //微币
    'credits' => 'http://credits.weibo.com',
    //微秀
    'show' => 'http://show.weibo.com/',
    //微博达人
    'club' => 'http://club.weibo.com',
     //广场
    'plaza' => 'http://plaza.weibo.com',
    //大屏幕
    'screen' => 'http://screen.weibo.com/',
    //开放平台
    'openapi' => 'http://open.weibo.com/',
    //连接网站
    'service' => 'http://weibo.com/app/website',
    
    //微号
    'weihao' => 'http://hao.weibo.com',

    //企业微博
    'enterprise' => 'http://a.weibo.com',
    'new_enterprise' => 'http://e.weibo.com',

    //应用首页
    'app' => 'http://weibo.com/app',
    //桌面微博
    'desktop' => 'http://desktop.weibo.com',
     //听歌
    'ting' => 'http://ting.weibo.com',

    //帐号设置
    'account' => 'http://account.weibo.com',
    //風雲榜
    'data_top' => 'http://data.weibo.com/top',
    //微博招聘
    'weibo_hr' => 'http://hr.weibo.com',
    //广告系统
    'adsystem' => 'http://biz.weibo.com',
    //名人堂
    'verified' => 'http://verified.weibo.com/',
    //帮助
    'help' => 'http://help.sina.com.cn',
    //手机版
    'mweibo' => 'http://m.weibo.com',
    //微公益
    'gongyi' => 'http://gongyi.weibo.com',
    //明星用户profile页面
    'new_star' => 'http://star.weibo.com',
    //微美食
    'food' => 'http://food.weibo.com',
    //话题
    'huati' => 'http://huati.weibo.com/k/%s?from=510',
);
