<?php
/**
 * 表情Do对象 
 * 目前表情只有下行数据，所以不需要做检测
 */
class Do_Face extends  Do_Abstract
{
    protected $props = array(
        //-----全部下行接口--------
        'category' => '', //分类名称
        'type' => '', //表情类型
        'phrase' => '', //表情名称
        'common' => '', //是否常用表情
        'hot' => '', //是否热门表情
        'icon' => '', //表情缩略图地址
        'picid' => '', //魔法表情的picid
        'url' => '', //表情原始地址
        'value' => '', //表情发布内容
        'swf' =>  '', //flash url
        'original' => '',
        'thumb' => '',
    );
    
    /*
     * 表情类型
     * face：普通表情，ani：魔法表情  cartoon：推荐配图
     */
    const TYPE_COMMON = "face";
    const TYPE_MAGIC = "ani";
    const TYPE_CARTOON = "cartoon";
    private static $_faceTypes = array(
        self::TYPE_COMMON => true, 
        self::TYPE_MAGIC => true, 
        self::TYPE_CARTOON => true
    );
    
    public function __construct($initData = null, $mode = Do_Abstract::MODE_OUTPUT)
    {   
        if ($initData['type'] == self::TYPE_CARTOON && !empty($initData['url']))
        {
            $initData['original'] = $initData['url'];
            $initData['thumb'] = $initData['icon'];
            //unset($this->props['url'],$this->props['icon']);
        }

        parent::__construct($initData, $mode);
    }
    
    /**
     * 检查表情type字段的合法性
     */
    public static function checkFaceType($type)
    {
        if (isset(self::$_faceTypes[$type]) === false)
        {
            throw new Do_Exception("error_face_type");
        }

        return true;
    }
    
    public function getOriginal()
    {
        if ($this->type === self::TYPE_CARTOON && empty($this->url))
        {
            parent::offsetSet('original', $this->url);
        }
    }
    
    public function getThumb()
    {
        if ($this->type === self::TYPE_CARTOON && empty($this->icon))
        {
            parent::offsetSet('thumb', $this->icon);
        }
    }
}