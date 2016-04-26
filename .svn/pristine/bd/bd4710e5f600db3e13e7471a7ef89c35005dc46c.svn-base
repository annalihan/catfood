<?php

class Tools_MiniBlogApiWrapper
{
    
    //通过nickname跳转域名
    const WEIBO_URL = "http://weibo.com/n/";
    
    //微盘文件类型
    const WORD = 0; //word文档

    const VIDEO = 1; //视频文件

    const ANYONE = 99; //所有文件

    public static function jsUnescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++)
        {
            if ($str[$i] == '%' && $str[$i + 1] == 'u')
            {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                $i += 5;
            }
            else if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            }
            else
                $ret .= $str[$i];
        }
        return $ret;
    }

    public static function getSign($uid, $api, $method = 'GET')
    {
        if (strpos($api, 'http://') !== false)
        {
            return $api;
        }
        
        $vdiskHeaders = array(
            
            'x-vdisk-cuid:' . $uid
        );
        // $vdiskHeaders = array('x-vdisk-cuid:' . $uid, 'x-vdisk-cip:' . $_SERVER['SERVER_ADDR']);
        

        $expire = time() + 10800;
        $stringToSign = $method . "\n\n" . $expire . "\n" . implode("\n", $vdiskHeaders) . "\n" . $api;
        
        $appKey = VDISK_APP_KEY;
        $appSecret = VDISK_APP_SECRET;
        
        $ssig = urlencode(substr(base64_encode(hash_hmac('sha1', $stringToSign, $appSecret, true)), 5, 10));
        
        return "http://api.weipan.cn{$api}?app_key={$appKey}&expire={$expire}&ssig={$ssig}";
        
        // $myIp = $_SERVER['SERVER_ADDR'];
        // return "http://api.weipan.cn{$api}?app_key={$appKey}&expire={$expire}&ssig={$ssig}&cip={$myIp}";
    }

    public static function dateFormat($format = 'Y-m-d', $time)
    {
        return date($format, $time);
    }

    /** 微盘访问接口
     *
     * @param $domain http://api.weipan.cn            
     * @param $api string            
     * @param $method GET
     *            or POST
     * @param $uid int            
     * @param array $parameter
     *            额外的参数
     * @return mixed */
    public static function requestVdiskApi($uid, $api, $method = "GET", $parameter = array())
    {
        $headers = array(
            
            'x-vdisk-cuid:' . $uid
        );
        // $headers = array('x-vdisk-cuid:' . $uid, 'x-vdisk-cip:' . $_SERVER['SERVER_ADDR']);
        //print_r($parameter);

        $url = self::getSign($uid, Tools_StringUtil::encode_path($api), $method);
        if ($method == 'GET' && !empty($parameter))
        {
            $url .= '&' . http_build_query($parameter);
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameter));
        }
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }

    /**
     * 
     * @param
     *            $uid
     * @param
     *            $result
     * @param $type 0
     *            = office files(word,ppt,pdf) 1 = videos(mp4,wmv,mpg)
     * @param string $path             */
    public static function getMyFilesInVdisk($uid, &$result, $type = Tools_MiniBlogApiWrapper::ANYONE, $path = '/')
    {
        $folder = json_decode(self::requestVdiskApi($uid, "/2/metadata/basic" . $path, "GET"));
        
        if (!$folder->is_dir)
        {
            return;
        }
        else
        {
            if ($type == Tools_MiniBlogApiWrapper::WORD)
            {
                $files = array(
                    
                    'application/pdf',
                    'application/vnd.ms-powerpoint'
                );
            }
            else if ($type == Tools_MiniBlogApiWrapper::VIDEO)
            {
                $files = array(
                    //                    'audio/mp3'             //mp3
                    //                    'video\/quicktime',     //mov
                    //                    'video/x-matroska',     //mkv
                    //                    'application/vnd.rn-realmedia-vbr', //rmvb
                    //                    'video/x-ms-wmv',       //wmv
                    //                    'video/avi',            //avi
                    'video/mp4', //mp4
                    'video/x-flv' //flv
                );
            }
            
            foreach ($folder->contents as $one)
            {
                if ($one->is_dir == false)
                {
                    if ($type == Tools_MiniBlogApiWrapper::ANYONE)
                    {
                        $one = (array) $one;
                        $one['name'] = Tools_StringUtil::get_name_from_path($one['path']);
                        $result[] = $one;
                    }
                    else if (array_search($one->mime_type, $files) !== false)
                    {
                        $one = (array) $one;
                        $one['name'] = Tools_StringUtil::get_name_from_path($one['path']);
                        $result[] = $one;
                    }
                }
                else
                {
                    self::getMyFilesInVdisk($uid, $result, $type, $one->path);
                }
            }
        }
    }

    /** 获取所有目录，搜索文件用
     *
     * @param
     *            $uid
     * @param
     *            $result
     * @param string $path             */
    public static function getAllFolders($uid, &$result, $path = '/')
    {
        $folder = json_decode(self::requestVdiskApi($uid, "/2/metadata/basic" . $path, "GET"));
        
        if (!$folder->is_dir)
        {
            return;
        }
        else
        {
            $result[] = $folder->path;
            foreach ($folder->contents as $one)
            {
                if ($one->is_dir == true)
                {
                    self::getAllFolders($uid, $result, $one->path);
                }
            }
        }
    }

    public static function searchInVdisk($uid, $query, $type, &$result, $path = "/")
    {
        $parameter = (empty($query) || strlen($query) < 1) ? array() : array(
            
            "query" => $query
        );
        //echo self::requestVdiskApi($uid, "/2/search/basic".$path ,"GET", $parameter);
        //return;
        $r = json_decode(self::requestVdiskApi($uid, "/2/search/basic" . $path, "GET", $parameter));
        
        if ($type == Tools_MiniBlogApiWrapper::WORD)
        {
            $files = array(
                
                'application/pdf',
                'application/vnd.ms-powerpoint'
            );
        }
        else if ($type == Tools_MiniBlogApiWrapper::VIDEO)
        {
            $files = array(
                //                    'audio/mp3'             //mp3
                //                    'video\/quicktime',     //mov
                //                    'video/x-matroska',     //mkv
                //                    'application/vnd.rn-realmedia-vbr', //rmvb
                //                    'video/x-ms-wmv',       //wmv
                //                    'video/avi',            //avi
                'video/mp4', //mp4
                'video/x-flv' //flv
            );
        }
        
        foreach ($r as $one)
        {
            if ($one->is_dir == false)
            {
                if ($type == Tools_MiniBlogApiWrapper::ANYONE)
                {
                    $one = (array) $one;
                    $one['name'] = Tools_StringUtil::get_name_from_path($one['path']);
                    $result[] = $one;
                }
                else if (array_search($one->mime_type, $files) !== false)
                {
                    $one = (array) $one;
                    $one['name'] = Tools_StringUtil::get_name_from_path($one['path']);
                    $result[] = $one;
                }
            }
        }
    }

    /** 0 - 全部
     * 1 - 蓝 V 认证
     * 2 - 橙 V 认证
     * 3 - 达人用户
     * 4 - 普通用户 */
    public static function getVipLevel($user)
    {
        if ($user["verified"] && $user['verified_type'] >= 1 && $user['verified_type'] <= 7)
        {
            return 1;
        }
        
        if ($user["verified"] && $user['verified_type'] == 0)
        {
            return 2;
        }
        
        if ($user["verified"] && ($user['verified_type'] == 200 || $user['verified_type'] == 220))
        {
            return 3;
        }
        
        if (!$user["verified"] && $user['verified_type'] == - 1)
        {
            return 4;
        }
        
        return 5; // Others
    }

    /** 关注一个用户
     *
     * @param int64 $uid             */
    public static function create($uid, $skipCheck = 0)
    {
        try
        {
            $comm = Comm_Weibo_Api_Friendships::create();
            $comm->uid = $uid;
            $comm->skip_check = $skipCheck;
            $rtn = $comm->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return $e->getMessage();
        }
        
        return TRUE;
    }

    /** 增加评论 */
    public static function addComment($mid, $comment, $commentOri = 0, $skipCheck = 0)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::create();
            $commApi->id = $mid;
            $commApi->comment = $comment;
            $commApi->comment_ori = $commentOri;
            $commApi->skip_check = $skipCheck;
            $rtn = $commApi->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return $e->getMessage();
        }
        return $rtn;
    }

    /**
     *
     * 回复评论
     *
     * @param array $arr_comment             */
    public static function commentsReply($mid, $comment, $cid, $commentOri = 0, $withoutMention = 0, $skipCheck = 0)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::reply();
            $commApi->id = $mid;
            $commApi->cid = $cid;
            $commApi->comment = $comment;
            $commApi->comment_ori = $commentOri;
            $commApi->without_mention = $withoutMention;
            $commApi->skip_check = $skipCheck;
            $rtn = $commApi->getResult();
            
            return true;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return false;
            // throw $e;
        }
    }

    public static function commentReply($id, $cid, $content, $commentOri = 0, $withoutMention = 0, $skipCheck = 0)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::reply();
            $commApi->id = $id;
            $commApi->cid = $cid;
            $commApi->comment = $content;
            $commApi->comment_ori = $commentOri;
            $commApi->without_mention = $withoutMention;
            $commApi->skip_check = $skipCheck;
            $rtn = $commApi->getResult();
            //$arr_comment = Dr_Comment::format_comment($rtn);
            return $rtn;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw $e;
        }
    }

    /**
     *
     * 删除评论
     *
     * @param
     *            $cid */
    public static function commentDestroy($cid)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::destroy();
            $commApi->cid = $cid;
            $rtn = $commApi->getResult();
            return true;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            // return false;
            return $e->getMessage();
        }
    }

    public static function publishBlog($text, $pic_id, $skipCheck = 0)
    {
        $api = Comm_Weibo_Api_Statuses::uploadUrlText();
        
        // $api->mid = $creat_mid;
        $api->status = $text;
        $api->pic_id = $pic_id;
        // $api->base_app = $base_app;
        #$api->visible = $status->visible;
        

        $api->skip_check = $skipCheck;
        
        $rtn = $api->getResult(true);
        
        return $rtn;
    }
    
    // 删除微博
    public static function destroyBlog($mid)
    {
        $commApi = Comm_Weibo_Api_Statuses::destroy();
        $commApi->id = $mid;
        
        return $commApi->getResult();
    }
    
    //发送一条微博
    public static function sendWeibo($text, $picUrl)
    {
        try
        {
            if ($picUrl != "")
            {
                $api = Comm_Weibo_Api_Statuses::uploadUrlText();
                $api->url = $picUrl;
            }
            else
            {
                $api = Comm_Weibo_Api_Statuses::update();
            }
            $api->status = $text;
            $rtn = $api->getResult(true);
            if (isset($rtn['error_code']))
            {
                return false;
            }
            return $rtn;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public static function getCommentList($id, $count, $page, $filterByAuthor)
    {
        $return = Tools_MiniBlogApiWrapper::getCommentListByMid($id, $count, $page, $filterByAuthor);
        //结果为空并且不是第一页，则取第一页数据
        if (empty($return) && $page > 1)
        {
            $page = 1;
            $return = Tools_MiniBlogApiWrapper::getCommentListByMid($id, $count, $page, $filterByAuthor);
        }
        return $return;
    }

    public static function getCommentListByMid($id, $count, $page, $filterByAuthor)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Comments::show();
            $commApi->id = $id;
            $commApi->page = $page;
            $commApi->count = $count;
            
            if ($filterByAuthor)
            {
                $commApi->filter_by_author = $filterByAuthor;
            }
            $result = $commApi->getResult();
            
            return $result;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public static function getPlayUrl($uid, $file)
    {
        $api = '/2/metadata/basic/' . $file;
        $parameters = array(
            
            'need_ext' => 'audio_mp3'
        );
        return self::requestVdiskApi($uid, $api, "GET", $parameters);
    }

    /** 将指定的文件复制到指定的微盘账户下
     *
     * @param string $path
     *            指定的文件路径
     * @param int $toUid
     *            指定的微盘账户
     * @return mixed */
    public static function copyFileToSchool($path, $toUid, $sha1, $size, $type = Model_Course::PERSONAL)
    {
        $viewer = Comm_Context::get('viewer', '');
        $me = $viewer ? $viewer->id : 0;
        
        /* $api = "/2/copy_ref/basic" . $path;
        
        $r = json_decode(self::requestVdiskApi($me, $api, "POST"),true);

        $copy_ref = $r->copy_ref; */

        //$api = "/2/fileops/copy/basic/";
        $api = "/2/relax/batch";
        
        if ($type == Model_Course::PERSONAL)
        {
            Tools_MiniBlogApiWrapper::createFolder($toUid, "/Contribution");
            Tools_MiniBlogApiWrapper::createFolder($toUid, "/Contribution/" . $me);
            $path = "/Contribution/" . $me . "/" . Tools_StringUtil::getName($path) . '-' . date('YmdHis', time()) . Tools_StringUtil::getExtFromPath($path);
        }
        else
        {
            Tools_MiniBlogApiWrapper::createFolder($toUid, "/Courses");
            Tools_MiniBlogApiWrapper::createFolder($toUid, "/Courses/" . $me);
            $path = "/Courses/" . $me . "/" . Tools_StringUtil::getName($path) . '-' . date('YmdHis', time()) . Tools_StringUtil::getExtFromPath($path);
        }
        $data = array(
            array(
                'sha1' => $sha1,
                'path' => $path,
                'size' => $size,
            ),
        );
        $ret = self::requestVdiskApi($toUid, $api, "POST", array(
                'data' => json_encode($data),
                //"to_path" => $path,
                //"from_copy_ref" => $copy_ref
        ));
        return $ret;
    }

    /** 在指定微盘账户下创建一个目录
     *
     * @param int $uid
     *            微盘账户
     * @param string $folderName
     *            目录名称
     * @return mixed */
    public static function createFolder($uid, $folderName)
    {
        $api = "/2/fileops/create_folder/";
        
        return self::requestVdiskApi($uid, $api, "POST", array(
            
            "root" => "basic",
            "path" => "/" . $folderName
        ));
    }

    /**
     *
     * 检测任意两个关系
     *
     * @param unknown_type $uid
     *            登录用户uid
     * @param unknown_type $fuid
     *            资源用户uid
     *            
     *            return 0 没关系
     *            return 1 我关注的
     *            return 2 粉丝
     *            return 3 互相关注
     *            return 10 其他 */
    public static function checkRelation($uid, $fuid)
    {
        if ($uid == "")
        {
            return 10;
        }
        if ($fuid == "")
        {
            return 10;
        }
        
        if ($uid == $fuid)
        {
            return 3;
        }
        
        try
        {
            $comm = Comm_Weibo_Api_Friendships::show();
            $comm->source_id = $uid;
            $comm->target_id = $fuid;
            $relationShip = $comm->getResult();
            
            //双向关注
            if ($relationShip['source']['followed_by'] == true && $relationShip['source']['following'] == true)
            {
                $status = 3;
            }
            
            //我的粉丝
            if ($relationShip['source']['followed_by'] == true && $relationShip['source']['following'] == false)
            {
                $status = 2;
            }
            
            //我是他的粉丝
            if ($relationShip['source']['followed_by'] == false && $relationShip['source']['following'] == true)
            {
                $status = 1;
            }
            
            //没有关注关系
            if ($relationShip['source']['followed_by'] == false && $relationShip['source']['following'] == false)
            {
                $status = 0;
            }
        }
        catch (Comm_Exception_Program $e)
        {
            $status = 0;
        }
        
        return $status;
    }

    /** 转发微博 */
    public static function repostBlog($oriMid, $text, $isComment = 0, $skipCheck = 0)
    {
        $api = Comm_Weibo_Api_Statuses::repost();
        $api->mid = null;
        $api->status = $text;
        $api->id = $oriMid;
        $api->is_comment = $isComment;
        $api->skip_check = $skipCheck;
        #$api->visible = $status->visible;
        $log_ext['isTransmit'] = 1;
        $log_ext['filter'][2] = 1;
        
        return $api->getResult(true);
    }

    public static function hex64to10($m)
    {
        $m = (string) $m;
        $hex2 = '';
        $code = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
        
        for ($i = 0, $l = strlen($code); $i < $l; $i ++)
        {
            $keyCode[] = $code[$i];
        }
        
        $keyCode = array_flip($keyCode);
        
        for ($i = 0, $l = strlen($m); $i < $l; $i ++)
        {
            $one = $m[$i];
            $hex2 .= str_pad(decbin($keyCode[$one]), 6, '0', STR_PAD_LEFT);
        }
        
        return bindec($hex2);
    }
    
    /* 根据微盘地址获得文档的fid */
    
    /** public static function getfid($file_id){
     * $data = array();
     * //$url = '/2ops/media?from_copy_ref='.$file_id;
     * //$result = self::requestVdiskApi($url);
     * //$file_info = json_decode($result,true);
     * //$fid = self::hex64to10($file_id);
     * $fid = $file_id;
     * if (strlen($file_id) == 5){
     * $fid = self::hex64to10($file_id);
     * }else{
     * $url="/2/get?copy_ref=".$fid;
     * $wp_info = self::requestVdiskApi(trim($url));
     * $wp_info = json_decode($wp_info,true);
     * $fid = empty($wp_info['obj_id']) ? 0 : $wp_info['obj_id'];
     * }
     *
     * if (empty($fid)){
     * //应该去sorry页
     * //Tool_Redirect::page_not_found();
     * return false;
     * }
     *
     * //$data['download_url'] = $file_info['url'];
     * //$fileType =substr($data['download_url'],strrpos($data['download_url'],".")+1);
     * //$data['fid'] = $fid.'@'.$fileType;
     * $url2="/2/account/info?t=1";
     * $user_info = self::requestVdiskApi($url2);
     * $user_info = json_decode($user_info,true);
     * if (empty($user_info['uid'])){
     * $user_info['uid'] = '37198877';
     * }
     * $data['fid'] = $user_info['uid'].'|'.$fid.'@pdf';
     * return $data;
     *
     * } */
    public static function getFileInfoByFid($uid, $ref_id)
    {
        $url = "/2/share/get?copy_ref=" . $ref_id;
        $wp_info = self::requestVdiskApi($uid, trim($url));
        $wp_info = json_decode($wp_info, true);
        
        return $wp_info;
        // $fid = empty($wp_info['obj_id']) ? 0 : $wp_info['obj_id'];
    }

    /** 获取微盘文件信息
     *
     * @param
     *            $uid
     * @param
     *            $path
     * @param string $fileType            
     * @return mixed */
    public static function getFileMetaInfo($uid, $path, $fileType = 'doc_swf')
    {
        $api = '/2/metadata/basic/' . $path;
        $parameters = array(
            
            'need_ext' => $fileType
        );
        return self::requestVdiskApi($uid, $api, "GET", $parameters);
    }

    public static function getTestFleMetaInfo($uid, $fileType = 'video_flv', $path = '企业微博升级课程.avi')
    {
        $api = '/2/metadata/basic/' . $path;
        $parameters = array(
            
            'need_ext' => $fileType
        );
        
        return self::requestVdiskApi($uid, $api, "GET", $parameters);
    }

    /** 获取微盘文件预览信息
     *
     * @param
     *            $uid
     * @param
     *            $url
     * @return mixed */
    public static function getVideoSwfInfo($path)
    {
        $temp = explode(".", $path);
        
        if (count($temp) < 2)
        {
            return "";
        }
        
        $suffix = array_pop($temp);
        
        $fileType = array(
            
            "ppt" => "doc_swf",
            "pdf" => "doc_swf",
            "avi" => "video_flv",
            "flv" => "video_flv",
            "mp4" => "video_flv"
        );
        
        $info = json_decode(Tools_MiniBlogApiWrapper::getFileMetaInfo(SCHOOL_WEIBO_ID, $path, $fileType[$suffix]), true);
        
        if (empty($info))
        {
            return array();
        }
        
        if (count($info) == 0)
        {
            return array();
        }
        
        $key = $fileType[$suffix] . "_url";
        if (array_key_exists($key, $info))
        {
            $preview_url = $info[$fileType[$suffix] . "_url"];
        }
        else
        {
            return array();
        }
        
        // $preview_url = $info[$fileType[$suffix]."_url"];
        

        /** if ($suffix == 'avi') {
         * return $preview_url;
         * } */
        
        $api = str_replace("http://api.weipan.cn", "", $preview_url);
        
        return self::requestVdiskApi(SCHOOL_WEIBO_ID, $api);
    }

    public static function getDocSwfInfo($path)
    {
        $temp = explode(".", $path);
        
        if (count($temp) < 2)
        {
            return array();
        }
        
        $suffix = array_pop($temp);
        
        $fileType = array(
            
            "ppt" => "doc_swf",
            "pdf" => "doc_swf",
            "avi" => "video_flv",
            "flv" => "video_flv",
            "mp4" => "video_flv"
        );

        $swf_url_array = json_decode(Tools_MiniBlogApiWrapper::getFileMetaInfo(SCHOOL_WEIBO_ID, $path, $fileType[$suffix]), true);
        
        if (empty($swf_url_array))
        {
            return array();
        }
        
        if (count($swf_url_array) == 0)
        {
            return array();
        }
        
        $key = $fileType[$suffix] . "_url";
        if (array_key_exists($key, $swf_url_array))
        {
            $preview_url = $swf_url_array[$fileType[$suffix] . "_url"];
        }
        else
        {
            return array();
        }
        
        $api = str_replace("http://api.weipan.cn", "", $preview_url);
        $swf_detail_info = self::requestVdiskApi(SCHOOL_WEIBO_ID, $api);
        //$swf_detail_info = json_decode($swf_detail_info,true);
        return array(
            
            $swf_url_array,
            $swf_detail_info
        );
    }
    
    // $size type: 0 =>　缩略图;　1 => 小图;　2 => 中图;　３=> 大图
    public static function getPictureUrl($sizeType, $fid)
    {
        if ($sizeType == 0)
        {
            return 'http://ww2.sinaimg.cn/thumbnail/' . $fid . '.jpg';
        }
        
        if ($sizeType == 1)
        {
            return 'http://ww1.sinaimg.cn/small/' . $fid . '.jpg';
        }
        
        if ($sizeType == 2)
        {
            return 'http://ww2.sinaimg.cn/bmiddle/' . $fid . '.jpg';
        }
        
        if ($sizeType == 3)
        {
            return 'http://ww2.sinaimg.cn/mw1024/' . $fid . '.jpg';
        }
    }

    public static function getUserInfo($uid, $nickName = '')
    {
        try
        {
            $api = Comm_Weibo_Api_Users::show();
            
            if (empty($uid))
            {
                if (empty($nickName))
                {
                    return array();
                }
                
                $api->screen_name = $nickName;
            }
            else 
            {
                $api->uid = $uid;
            }
            $user_info = $api->getResult();
            $vipLevel = self::getVipLevel($user_info);
            
            $user_info['vip_level'] = $vipLevel;
        }
        catch (Exception $e)
        { 
            return false;
        }
        return $user_info;
    }

    /** 添加一条私信
     * 约束: uid、screen_name两个参数必选其一
     *
     * @param string $text            
     * @param int64 $uid            
     * @param string $screenName            
     * @param string $fids
     *            逗号隔开的文件id,不能超过10个
     * @param int64 $id
     *            转发的微博id * */
    public static function sendPrivateMsg($text, $uid = NULL, $screenName = NULL, $fids = NULL, $skipCheck = 0)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_Messages::newMessage();
            
            if (empty($uid) && empty($screenName))
            {
                return false;
            }
            
            if ($uid > 0 && !empty($screenName))
            { //两个都传，接口不支持，以uid为主
                $screenName = NULL;
            }
            
            $apiObj->text = $text;
            if (!empty($uid))
            {
                $apiObj->uid = $uid;
            }
            
            if (!empty($screenName))
            {
                $apiObj->screen_name = $screenName;
            }
            
            if (!empty($fids))
            {
                $apiObj->fids = $fids;
            }
            
            $apiObj->skip_check = $skipCheck;
            $return = $apiObj->getResult();
            
            return $return;
        }
        catch (Comm_Exception_Program $e)
        {
            return $e->getMessage();
        }
    }

    /**
     *
     * 根据微博id获取该微博的转发列表
     *
     * @param int $mid
     *            微博ID。
     * @param int $count
     *            返回结果条数数量，默认50默认为50
     * @param int $page
     *            页码.返回的结果的页码。默认为1
     * @param int $filterByAuthor
     *            转发列表筛选，0.全部 1. 关注人 2. 陌生人。默认0
     * @param int $sinceId
     *            若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。默认为0
     * @param int $maxId
     *            若指定此参数，则返回ID小于或等于max_id的微博消息。默认为0 */
    public static function repostTimeLine($mid, $count = 50, $page = 1, $filterByAuthor = 0, $sinceId = 0, $maxId = 0)
    {
        try
        {
            $api_statuses = Comm_Weibo_Api_Statuses::repostTimeline();
            $api_statuses->id = $mid;
            $api_statuses->page = $page;
            $api_statuses->count = $count;
            $api_statuses->filter_by_author = $filterByAuthor;
            
            //$api_statuses->since_id = $sinceId;
            //$api_statuses->max_id = $maxId;
            

            $repostRst = $api_statuses->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array(
                
                'list' => array(),
                'total' => 0,
                'next_cursor' => 0
            );
            //throw new Dr_Exception($e);
        }
        
        if ($repostRst)
        {
            $repostTotalNum = $repostRst['total_number'];
            $repostNextCursor = $repostRst['next_cursor'];
            $repostPreviousCursor = $repostRst['previous_cursor'];
            if (isset($repostRst['reposts']))
            {
                $repostFeedList = array();
                foreach ($repostRst['reposts'] as $report)
                {
                    //过滤已经删除的微博
                    if (!isset($report['user']))
                    {
                        continue;
                    }
                    $repostFeedList[] = $report;
                }
                
                /* if ($repostFeedList) { $repostFeedList = Dr_Status::mapping_status($repostFeedList); } */
            }
        }
        else
        {
            return array(
                
                'list' => array(),
                'total' => 0,
                'next_cursor' => 0
            );
        }
        return array(
            
            'list' => $repostFeedList,
            'total' => $repostTotalNum,
            'next_cursor' => $repostNextCursor,
            'previous_review' => $repostPreviousCursor
        );
    }

    public static function getCommentAndRepostsNum(array $mids)
    {
        $rcNum = array();
        try
        {
            $commApi = Comm_Weibo_Api_Statuses::statusesCount();
            $commApi->ids = implode(',', $mids);
            $rst = $commApi->getResult();
            foreach ($rst as $status)
            {
                if (!isset($status['id']))
                {
                    continue;
                }
                $rcNum[$status['id']] = array(
                    'comments' => $status['comments'],
                    'rt' => $status['reposts']
                );
            }
        }
        catch (Comm_Exception_Program $e)
        {
            return $e->getMessage();
        }
        return $rcNum;
    }
    
    /* @author quanjunw<wbquanjunw@staff.sina.com.cn> 
     * @date 2014年4月2日 
     * @note 二维数组去重，且可自定义返回数量的二维数组 
     * @param $arr 二维数组 $key 指定的键 $limit 指定剪切的数量 
     * */
    public static function arrayUnique($arr, $key, $limit)
    {
        $newArr = array();
        for ($i = 0; $i < count($arr); $i ++)
        {
            if (!isset($newArr[$arr[$i][$key]]))
            {
                $newArr[$arr[$i][$key]] = $arr[$i];
                
                if (count($newArr) == $limit && isset($limit))
                {
                    break;
                }
            }
        }
        return array_values($newArr);
    }
    
    //获取用户的uid
    public static function getCurrentUID()
    {
        $sso = new SSOClient();
        if ($sso->isLogined())
        {
            $user = $sso->getUserInfo();
            return $user['uid'];
        }
        else
        {
            return 0;
        }
    }
    
    //判断关注
    public function isTeacherFollowed($tid)
    {
        $status = self::checkRelation(self::getCurrentUID(), $tid);
        if ($status == 1)
        {
            return 1;
        }
        elseif ($status == 3)
        {
            return 3;
        }
        else
        {
            return 0;
        }
    }
    
    //判断用户是否订阅
    public static function hasCategory($categories, $categoryId)
    {
        foreach ($categories as $category)
        {
            if ($category['info_id'] == $categoryId)
            {
                return 1;
            }
        }
        return 0;
    }
    
    //判断文件类型
    //$checkType 1 文件
    //$checkType 2 图片
    public static function fileCheck($checkType, $file)
    {
        
        $msg = '';
        //check mime
        $mime = $file['mime_type'];
        
        $fileType = array(
            'application/pdf',
            'application/mspowerpoint',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'video/mp4',
            'video/x-flv',
            'video/x-msvideo'
        );
        
        if ($checkType == 1)
        {
            if (!in_array($mime, $fileType))
            {
                return $msg = 'this is'.$mime.',The file type is not legal';
            }
        }
        elseif ($checkType == 2)
        {
            if ($mime != "image/png" && $mime != "image/jpeg")
            {
                return $msg = 'You can only upload a JPG or PNG format images';
            }
        }
        
        
        //check file size
        /* $fileSize = $file['bytes'];
        
        $checkType == 1 ? $checkSize = 8000000 : $checkSize = 2000000;
        $checkType == 1 ? $checkSizeText = 8 : $checkSizeText = 2;
        
        if ($fileSize > $checkSize)
        {
            if ($mime == 'application/pdf' || $mime == 'application/mspowerpoint' || $mime == 'application/msword' || $mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            {
                return $msg = 'the file max only update ' . $checkSizeText . ' M';
            }
        } */
        
        return $msg;
    }
    
    //获取用户兴趣
    public static function getInterestUserInterestTop5()
    {
        $uid = self::getCurrentUID();
        $url = 'http://user.opendata.sina.com.cn/interface/intf/get_interest_user_interest_top5.jsp';
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', "int", true);
        $platform->__set('uid', $uid);
        //getResult 参数FALSE 为不解析返回资源
        $platform->getResult(false);
        return $platform->getHttpRequest()->responseContent;
    }
    
    //注册后处理页面
    public function pageDisposeRegister ($url, $code, $msg)
    {
        Tool_Redirect::response($url, $code, $msg);
    }
}