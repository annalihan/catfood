<?php
/**
 * 新浪产品相关域名定义
 * 
 * 模板中使用方法 <?"domain.xxx"|conf?>
 */
//海外加速
$sinasrv_domain = isset($_SERVER['SINASRV_DOMAIN']) ? $_SERVER['SINASRV_DOMAIN'] : 'weibo.com';
return array(
    
    'product_page_uri' => 'http://weibo.com/p/100122', // 电商商品大类iid对应的page 地址
    'hd_page_uri' => 'http://weibo.com/p/100123', // 互动actid对应的page 地址
    'hudong' => 'http://hd.e.weibo.com', // added by liuyu6
    'epsweibo' => 'http://e.weibo.com',
    'ordersc' => 'http://mall.sc.weibo.com',
    'paysc' => 'http://buy.sc.weibo.com/alipay/pay',
    'apisc' => 'http://open.sc.weibo.com',
    //新浪微博
    'weibo' => 'http://' . $sinasrv_domain,
    'weibo_bak' => 'http://weibo.com',
    'search' => 'http://s.weibo.com',
    //同城
    'city' => 'http://city.weibo.com',
    //微群
    'qun' => 'http://q.weibo.com',
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
    'pub_star' => 'http://' . $sinasrv_domain . '/pub/star',
    //风云榜
    'pub_top' => 'http://' . $sinasrv_domain . '/pub/top',
    //微话题
    'pub_topic' => 'http://' . $sinasrv_domain . '/pub/topic',
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
    //短链域名
    'shorturl' => 'http://t.cn',
    //名人堂
    'verified' => 'http://verified.weibo.com/',
    'verified_platform' => 'http://verified.weibo.com/verify',
    'downloadclient' => 'http://down.sina.cn/weibo/',
    'school'    => 'http://xueyuan.weibo.com/',
    'school_label'  => 'http://xueyuan.e.weibo.com/',
    'test'  => 'http://newxy.weibo.com/',
    'school_test'  => 'http://school.weibo.com/',
);
