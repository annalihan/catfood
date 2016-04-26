<?php
/**
 * 私信相关操作 - 写入器
 * @author 郑伟 <zhengwei7@staff.sina.com.cn>
 * @todo 待完整迁移，需要什么加什么
 */

class Dw_Message extends Dw_Abstract
{
    /**
     * 发送一条私信
     * @param  [type]  $text       私信内容
     * @param  [type]  $uid        接收方uid
     * @param  [type]  $screenName 接收方昵称，与uid只能选一个
     * @param  [type]  $fids       附件id，以','分隔，最多10个
     * @param  [type]  $id         需要发送的微博id
     * @param  integer $skipCheck  [description]
     * @param  boolean $type       [description]
     * @return array 错误时抛异常
     */
    public static function newMessage($text, $uid = null, $screenName = null, $fids = null, $id = null, $skipCheck = 0, $type = false)
    {
        $api = Comm_Weibo_Api_Messages::newMessage();

        if ($type == 'monitor')
        {
            $api->addUserPassword('492809497@qq.com', 'mcpteam2012');
        }

        $api->uid = $uid;
        $api->screen_name = $screenName;
        $api->text = $text;
        $api->fids = $fids;
        $api->id = $id;
        $api->skip_check = $skipCheck;

        return $api->getResult();
    }

}
