<?php
class Comm_Weibo_Api_Tags
{
    const RESOURCE = "tags";

    /**
     * 返回指定用户的标签列表
     */
    public static function getTags()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, null);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->supportPagination();
        
        return $platform;
    }

    /**
     * 批量获取用户标签
     * @todo uids 的类型有疑问
     */
    public static function tagsBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "tags_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "int64", true);
        
        return $platform;
    }

    /**
     * 返回系统推荐的标签列表
     */
    public static function suggestions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "suggestions");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("count", "int");
        
        return $platform;
    }

    /**
     * 添加用户标签
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "create");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("tags", "string", true);
        $callbackObj = new Comm_Weibo_Api_Tags;
        $platform->addSetCallback("tags", $callbackObj, "checkTags");
        
        return $platform;
    }

    /**
     * 删除用户标签
     * @return [type] [description]
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "destroy");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("tag_id", "int64", true);
        
        return $platform;
    }

    /**
     * 批量删除用户标签
     * @todo ids 的类型有疑问
     */
    public static function destroyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "destroy_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("ids", "int64", true);
        
        return $platform;
    }

    /**
     * set tags参数的callback 方法
     */
    public function checkTags($tags)
    {
        if (strlen(trim($tags)) <= 0)
        {
            throw new Comm_Exception_Program("param tags can not be null");
        }

        $tmp = explode(", ", $tags);
        foreach ($tmp as $tag)
        {
            if (mb_strwidth($tag, "utf-8") > 14)
            {
                throw new Comm_Exception_Program("{$tag} is too long");
            }
        }
        
        return $tags;
    }
}
