<?php
class Cache_Account extends Cache_Abstract
{
    protected $configs = array(
        'account' => array('%s_1_%s', 86400), // 解冻帐号时，用户手机号码的激活次数
        'unfreeze_info' => array('%s_2_%s', 86400), // 用户验证码信息
        'weihao_info' => array('%s_3_%s', 1296000), // 微号用户
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'account';

    public function getTimes($phone)
    {
        $key = $this->key('account', $phone);
        return $this->get($key);
    }

    public function clearTimes($phone)
    {
        $key = $this->key('account', $phone);
        return $this->del($key);
    }

    public function createTimes($phone, $value)
    {
        $key = $this->key('account', $phone);
        $this->set($key, $value, $this->livetime('account'));
    }

    public function getUnfreezeInfo($uid)
    {
        $key = $this->key('unfreeze_info', $uid);
        return $this->get($key);
    }

    public function createUnfreezeInfo($uid, $value)
    {
        $key = $this->key('unfreeze_info', $uid);
        $this->set($key, $value, $this->livetime('unfreeze_info'));
    }

    public function clearUnfreezeInfo($uid)
    {
        $key = $this->key('unfreeze_info', $uid);
        return $this->del($key);
    }

    /**
     * 批量从缓存获取微号信息
     */
    public function getWeihaoInfos(array $uids, array &$keys = array())
    {
        foreach ($uids as $uid)
        {
            $keys[$uid] = $this->key('weihao_info', $uid);
        }

        return $this->mget($keys);
    }

    /**
     * 创建微号缓存
     */
    public function createWeihaoInfos(array $items)
    {
        return $this->mset($items, $this->livetime('weihao_info'));
    }

    /**
     * 批量清除微号信息缓存
     */
    public function clearWeihaoInfos(array $uids)
    {
        foreach ($uids as $mid)
        {
            $keys[$mid] = $this->key('weihao_info', $mid);
        }

        return $this->mdel($keys);
    }
}
