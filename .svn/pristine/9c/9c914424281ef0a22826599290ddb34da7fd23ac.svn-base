<?php
/**
 * 微博相关操作 - 写入器
 * @author 郑伟 <zhengwei7@staff.sina.com.cn>
 * @todo 待迁移，目前只迁移用到的
 */

class Dw_Status extends Dw_Abstract
{
    /**
     * 发布一条带图片的微博
     * @param  string $text 文本
     * @param  string $url  图片URL
     * @return array 失败抛异常
     */
    public static function uploadUrlText($text, $url = "")
    {
        if ($url != "")
        {
            $api = Comm_Weibo_Api_Statuses::uploadUrlText();
            $api->url = $url;
        }
        else
        {
            $api = Comm_Weibo_Api_Statuses::update();
        }

        $api->status = $text;

        return $api->getResult(true);
    }
}