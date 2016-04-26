<?php
class Comm_Weibo_Api_Mss
{
    const RESOURCE = "mss";
    
    /**
     * 查询文件信息
     * @return [type] [description]
     */
    public static function metaQuery()
    {
        $url = Comm_Weibo_Api_Request_Mss::assembleUrl(self::RESOURCE, "meta_query", 'json', 2);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("fid", "int64", true);
        
        return $plateform;
    }
    
    /**
     * 下载文件
     * @return [type] [description]
     */
    public static function msget()
    {
        $url = Comm_Weibo_Api_Request_Mss::assembleUrl(self::RESOURCE, "msget", 'json', 2);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("fid", "int64", true);
        $plateform->addRule("gid", "int64", false);

        return $plateform;
    }
}