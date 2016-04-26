How TO use SinaLeopardService:

说明：
    支持message内容按自定义等级推送往不同catocary地址
    规定写入的message最大不超过2000B
    SinaScribeService处理后的message格式：时间戳 服务器IP message内容
    例子：2011-06-02 14:17:57 10.73.13.124 level2 test-message-body

example
<?php
//包含 SinaScribeService 包
require_once('SinaLeopardService/SinaLeopardService.php');
//新建一个 SCRIBE_dpool 对象
$dScribe = new SinaLeopardService();
//写日志,约定第一字段是类别标识（开发人员自定义）
//level1类别的日志写往catocary：test1
//level2类别的日志写往catocary：test2
//无类别或者default的往日志写catocary：test3
//$catocaries = array('test3'=>'default');
$catocaries = array('test1'=>'level1','test2'=>'level2','test3'=>'default');
$message = 'level2 test-message-body';//message内容
$ret = $dScribe->sendMessage($catocaries,$message);
//打印异常,如果成功将返回SinaScribeService处理后的日志（带时间戳、服务器IP、message内容）
print_r($ret);
?>
