<?php
/**
 * Cache_Main
 * @todo 待处理转换格式
 * @not_check 先不检查内容
 */
class Cache_Main extends Cache_Abstract
{
    protected $configs = array(
        'announcement' => array('%s_1', 300), //首页右侧公告栏辟谣信息
        'fun' => array('%s_2', 300), //首页右侧玩转微博
        'task_area_mids' => array('%s_13_%s_%s', 3600), //微博宝典推荐微博
        'task_province_mids' => array('%s_13_%s', 3600), 
        'task_gender_mids' => array('%s_14_%s', 3600), 
        'grass_uid' => array('%s_15_%d_%d', 86400), //人气草根用户
        'manual_recommend_user' => array('%s_16', 86400), //人气关注用户
        'invite_num' => array('%s_17_%s', 600), 
        'mobile_bind' => array('%s_19_%s', 600), 
        'task_baodian_mids' => array('%s_20', 3600), 
        'task_hot_fav_mids' => array('%s_21', 3600), 
        'task_hot_comment_mids' => array('%s_22', 3600), 
        'task_hot_forward_mids' => array('%s_23', 3600), 
        'task_friend_mids' => array('%s_24_%s', 3600), 
        'task_friend_uids' => array('%s_25_%s', 3600), 
        'find_hot_word' => array('%s_29', 3600), //找人页热门搜索关键词
        'v5_top_game' => array('%s_game_%s', 600), //V5顶导游戏
        'v5_top_app' => array('%s_app_%s', 600), //V5顶导应用
        'v5_top_bar' => array('%s_32_%s_%s_%s', 600), //V5顶导微吧
        'v5_top_hot' => array('%s_hot_%s_%s', 600),      //v5顶导热门
        'lefnav_app_list' => array('%s_33_%s_%s', 1200), //V5左导应用列表
        'tauth2_token' => array('%s_255', 604800), //tauth2的token
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'main';

    const MBS_SYNC_CODE = 'mb_v5_main';

    public function getAnnouncement()
    {
        $key = $this->key('announcement');
        return $this->get($key);
    }

    public function createAnnouncement($value)
    {
        $key = $this->key('announcement');
        $this->set($key, $value, $this->livetime('announcement'));
    }

    public function getFun()
    {
        $key = $this->key('fun');
        return $this->get($key);
    }

    public function createFun($data)
    {
        $key = $this->key('fun');
        $this->set($key, $data, $this->livetime('fun'));
    }

    /**
     * 设置微博宝典推荐同城微博mid
     * @param unknown_type $province
     * @param unknown_type $city
     * @param unknown_type $value
     */
    public function createTaskAreaMids($province, $city, $value)
    {
        $key = $this->areaKey($province, $city);
        $this->set($key, $value, $this->livetime("task_area_mids"));
    }

    /**
     * 获取微博宝典推荐同城微博mid
     * @param unknown_type $province
     * @param unknown_type $city
     */
    public function getTaskAreaMids($province, $city)
    {
        $key = $this->areaKey($province, $city);
        return $this->get($key);
    }

    /**
     * 设置微博宝典推荐异性微博mid
     * @param unknown_type $gender
     * @param unknown_type $value
     */
    public function createTaskGenderMids($gender, $value)
    {
        $key = $this->genderKey($gender);
        $this->set($key, $value, $this->livetime('task_gender_mids'));
    }

    /**
     * 获取微博宝典推荐异性微博mid
     * @param unknown_type $gender
     */
    public function getTaskGenderMids($gender)
    {
        $key = $this->genderKey($gender);
        return $this->get($key);
    }

    public function getBaodianMids()
    {
        $key = $this->key("task_baodian_mids");
        return $this->get($key);
    }

    public function createBaodianMids($value)
    {
        $key = $this->key("task_baodian_mids");
        return $this->set($key, $value);
    }

    public function getTaskHotFavMids()
    {
        $key = $this->key('task_hot_fav_mids');
        return $this->get($key);
    }

    public function createTaskHotFavMids($value)
    {
        $key = $this->key('task_hot_fav_mids');
        return $this->set($key, $value, $this->livetime('task_hot_fav_mids'));
    }

    public function get_task_hot_comment_mids()
    {
        $key = $this->key('task_hot_comment_mids');
        return $this->get($key);
    }

    public function create_task_hot_comment_mids($value) {
        $key = $this->key('task_hot_comment_mids');
        return $this->set($key, $value, $this->livetime('task_hot_comment_mids'));
    }

    public function get_task_hot_forward_mids() {
        $key = $this->key('task_hot_forward_mids');
        return $this->get($key);
    }

    public function create_task_hot_forward_mids($value) {
        $key = $this->key('task_hot_forward_mids');
        return $this->set($key, $value, $this->livetime('task_hot_forward_mids'));
    }

    public function get_task_friend_mids($uid) {
        $key = $this->key('task_friend_mids', $uid);
        return $this->get($key);
    }

    public function create_task_friend_mids($uid, $value) {
        $key = $this->key('task_friend_mids', $uid);
        return $this->set($key, $value, $this->livetime('task_friend_mids'));
    }

    /**
     * 获取微博宝典推荐微博缓存key
     * @param unknown_type $province
     * @param unknown_type $city
     */
    protected function areaKey($province, $city)
    {
        if ($province == 1000)
        {
            return false;
        }

        if ($city == 1000)
        {
            return $this->key('task_province_mids', $province);
        }
        else
        {
            return $this->key('task_area_mids', $province, $city);
        }
    }

    protected function genderKey($gender)
    {
        return $this->key("task_gender_mids", $gender == 'f' ? 2 : 1);
    }

    /**
     * 获取人气草根用户
     */
    public function getGrassUser($class1, $class2)
    {
        $key = $this->key('grass_uid', $class1, $class2);
        return $this->get($key);
    }

    /**
     * 设置人气草根用户
     * Enter description here ...
     * @param unknown_type $value
     */
    public function createGrassUser($value, $class1, $class2)
    {
        $key = $this->key('grass_uid', $class1, $class2);
        return $this->set($key, $value, $this->livetime('grass_uid'));
    }

    /**
     * 获取人气关注用户
     * Enter description here ...
     */
    public function getManualRecommendUser($type, $isMixed)
    {
        $key = $this->key('manual_recommend_user');
        return $this->get($key);
    }

    /**
     * 设置人气关注用户
     * Enter description here ...
     * @param unknown_type $value
     */
    public function createManualRecommendUser($value, $type, $isMixed)
    {
        $key = $this->key('manual_recommend_user');
        return $this->set($key, $value, $this->livetime("manual_recommend_user"));
    }

    /**
     * 获取一起修炼宝典的人
     * Enter description here ...
     * @param unknown_type $uid
     */
    public function get_task_friend_uids($uid) {
        $key = $this->key('task_friend_uids', $uid);
        return $this->get($key);
    }

    /**
     * 设置一起修炼宝典的人
     * Enter description here ...
     * @param unknown_type $uid
     * @param unknown_type $value
     */
    public function create_task_friend_uids($uid, $value) {
        $key = $this->key('task_friend_uids', $uid);
        return $this->set($key, $value);
    }

    public function get_invite_num($uid) {
        $key = $this->key('invite_num', $uid);
        return $this->get($key);
    }

    public function set_invite_num($value, $uid) {
        $key = $this->key('invite_num', $uid);
        return $this->set($key, $value);
    }

    public function get_mobile_bind($uid) {
        $key = $this->key('mobile_bind', $uid);
        return $this->get($key);
    }

    public function set_mobile_bind($value, $uid) {
        $key = $this->key('mobile_bind', $uid);
        return $this->set($key, $value);
    }

    /**
     * 获取找人页热门搜索关键词
     */
    public function get_find_hot_word() {
        $key = $this->key('find_hot_word');
        return $this->get($key);
    }

    /**
     * 设置找人页热门搜索关键词
     */
    public function set_find_hot_word($value) {
        $key = $this->key('find_hot_word');
        return $this->set($key, $value);
    }

    /**
     * 获取v5顶导游戏
     *
     * @return array
     */
    public function get_v5_game($uid) {
        $key = $this->key('v5_top_game', $uid);
        return $this->get($key);
    }

    /**
     * 设置v5顶导游戏
     * 
     * @param array $value 游戏列表
     *
     * @return boolean
     */
    public function set_v5_game($uid, $value) {
        $key = $this->key('v5_top_game', $uid);
        return $this->set($key, $value);
    }

    /**
     * 获取v5顶导应用
     * 
     * @param int64 $uid 用户uid
     *
     * @return array
     */
    public function get_v5_app($uid) {
        $key = $this->key('v5_top_app', $uid);
        return $this->get($key);
    }

    /**
     * 设置v5顶导应用
     * 
     * @param int64 $uid   用户uid
     * @param array $value 应用列表
     *
     * @return boolean
     */
    public function set_v5_app($uid, $value) {
        $key = $this->key('v5_top_app', $uid);
        return $this->set($key, $value);
    }

    /**
     * 获取v5顶导微吧
     * 
     * @param int64 $uid 用户uid
     *
     * @return array
     */
    public function get_v5_bar($uid, $version, $datatype) {
        $key = $this->key('v5_top_bar', $uid, $version, $datatype);
        return $this->get($key);
    }

    /**
     * 设置v5顶导微吧
     * 
     * @param int64 $uid   用户uid
     * @param array $value 微吧列表
     *
     * @return boolean
     */
    public function set_v5_bar($uid, $version, $datatype, $value) {
        $key = $this->key('v5_top_bar', $uid, $version, $datatype);
        return $this->set($key, $value);
    }

    
    /**
     * 获取v5顶导热门
     * 
     * @param int $random_key 端口随机数
     * @param string $datatype 数据类型
     * 
     * @return array 热门数组
     */
    public function get_v5_hot($random_key, $uid) {
        $key = $this->key("v5_top_hot", $random_key, $uid);
        return $this->get($key);
    }
    
    /**
     * 设置v5顶导热门
     * 
     * @param array 热门数据
     * @param string 数据类型
     * 
     * @return boolean
     */
    public function set_v5_hot($value, $uid) {
        $random_arr = array(0,1, 2, 3, 4, 5, 6, 7, 8, 9);
        $mc_data = array();
        foreach ($random_arr as $val) {
            $key = $this->key("v5_top_hot", $val, $uid);
            $mc_data[$key] = $value;
        }
        return $this->mset($mc_data, $this->livetime("v5_top_hot"));
    }
    
    /**
     * 获取左导应用列表
     * @param int64 $uid 用户uid
     * @param string $lang
     * @return array
     */
    public function get_left_app_nav($uid, $lang = 'zh_cn') {
        $key = $this->key('lefnav_app_list', $uid, $lang);
        return $this->get($key);
    }

    /**
     * 设置左导应用列表
     * @param int64 $uid 用户uid
     * @param string $lang
     * @return boolen
     */
    public function set_left_app_nav($uid, $lang = 'zh_cn', $value) {
        $key = $this->key('lefnav_app_list', $uid, $lang);
        return $this->set($key, $value, $this->livetime("lefnav_app_list"));
    }
    /**
     * 设置左导应用列表
     * @param int64 $uid 用户uid
     * @param string $lang
     * @return boolen
     */
    public function delete_left_app_nav($uid, $lang = 'zh_cn')
    {
        $key = $this->key('lefnav_app_list', $uid, $lang);
        return $this->delete($key);
    }
    
    /**
     * 清除左导应用列表缓存
     * @param int64 用户uid
     * @param string $lang
     * @return bool
     */
    public function clear_left_app($uid, $lang = 'zh_cn')
    {
        $key = $this->key("lefnav_app_list", $uid, $lang);
        return $this->del($key);
    }
    
    public function getTauth2Token()
    {
        $key = $this->key('tauth2_token');
        return $this->get($key);
    }

    public function setTauth2Token($value)
    {
        $key = $this->key('tauth2_token');
        //TODO 同步
        return $this->set($key, $value, $this->livetime('tauth2_token'));
    }
}
