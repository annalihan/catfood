<?php
/**
 * 用户关系操作相关 - 写入类
 * @author 郑伟 <zhengwei7@staff.sina.com.cn>
 * @todo 待迁移，目前只迁移了用到的
 */

class Dw_Relation extends Dw_Abstract
{
    /**
     * 删除用户关系缓存
     * @param  [type] $uid  关注人uid
     * @param  [type] $fuid 被关注人uid
     * @return null
     */
    public static function delCacheOfRelation($uid, $fuid)
    {
        $cache = new Cache_User();
        $cache->delUserInfos(array($uid, $fuid));
        $cache->delMayInterested($uid);
    }

    /**
     * 关注一个用户
     * @param  [type]  $fuid       待关注的uid
     * @param  integer $skipCheck [description]
     * @return true 失败抛异常
     */
    public static function create($fuid, $skipCheck = 0)
    {
        try
        {
            $api = Comm_Weibo_Api_Friendships::create();
            $api->uid = $fuid;
            $api->skip_check = $skipCheck;
            $res = $api->getResult();

            //删除关系相关缓存
            self::delCacheOfRelation(Comm_Context::get('viewer')->id, $fuid);

            $cache = new Cache_Feedindex();
            $cache->delFeedList(Comm_Context::get('viewer')->id);
            return true;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            self::delCacheOfRelation(Comm_Context::get('viewer')->id, $fuid);
            throw $e;
        }
    }
}
