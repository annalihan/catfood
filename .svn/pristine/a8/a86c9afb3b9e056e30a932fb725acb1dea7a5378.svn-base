<?php
/**
 * Feed缓存相关操作
 * @author 郑伟 <zhengwei7@staff.sina.com.cn>
 * @todo 待完整迁移
 */
class Cache_Feedindex extends Cache_Abstract
{
    protected $configs = array(
        'feedindex' => array('%s_2_%s', 432000), //缓存5天
    );

    protected $cachePool = 'FEEDINDEX';
    protected $keyPrefix = 'feedindex';

    /**
     * 删除FEED列表
     * @param $uid 用户ID
     * @return bool 是否成功
     */
    public function delFeedList($uid)
    {
        $key = $this->key('feedindex', $uid);
        return $this->del($key);
    }
}