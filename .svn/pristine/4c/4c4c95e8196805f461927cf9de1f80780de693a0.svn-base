<?php
class Dr_File extends Dr_Abstract
{
    /**
     * 文件上传获取签名
     * 
     * @param string $fileName
     */
    public static function attachmentUploadSign($fileName)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_File::attachmentUploadSign();       
            $apiObj->file_name = $fileName;
            $apiObj->getHttpRequest()->connectTimeout = 10000;
            $apiObj->getHttpRequest()->timeout = 10000;
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        } 
    }
    
    /**
     * 文件上传回调接口
     * @param string $fileName
     * @param string $key
     * @throws Dr_Exception
     */
    public static function attachmentUploadBack($fileName, $key)
    {
        try
        {
            $apiObj = Comm_Weibo_Api_File::attachmentUploadBack();
            $apiObj->file_name = $fileName;
            $apiObj->key = $key;   
            $apiObj->getHttpRequest()->connectTimeout = 180000;
            $apiObj->getHttpRequest()->timeout = 180000;
            $rtn = $apiObj->getResult();

            //缓存
            $cacheFile = new Cache_File();
            if (!empty($rtn['fid']))
            {
                $cacheFile->setInfo($rtn['fid'], $rtn);
            }
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $rtn;    
    }
    
    /**
     * 根据fid获取附件文件的信息
     * 
     * @param string or int64  $fid //文档暂未定目标是支持多个文件id
     * @throws Dr_Exception
     */
    public static function attachmentInfo($fid, $isCache = 0)
    {
        try
        {
            $cacheFile = new Cache_File();

            if ($isCache != 1)
            {
                $data = $cacheFile->getInfo($fid);
                if ($data)
                {
                    return $data;
                }
            }

            //$apiObj = Comm_Weibo_Api_File::attachmentInfo();
            $apiObj = Comm_Weibo_Api_Mss::metaQuery();
            $apiObj->fid = $fid;
            $apiObj->getHttpRequest()->connectTimeout = 10000;
            $apiObj->getHttpRequest()->timeout = 10000;
            $rtn = $apiObj->getResult();
            $rtn['src_id'] = $fid;
            $rtn = self::_translateFileFormat($rtn);

            //加缓存
            $cacheFile->setInfo($fid, $rtn);

        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $rtn;    
    }

    /**
     * 获取微盘使用情况
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public static function fileQuota($uid)
    {
        try
        {
            $cacheFile = new Cache_File();
            $data = $cacheFile->getVdiskUsage($uid);
            if (!empty($data))
            {
                return $data;
            }

            $apiObj = Comm_Weibo_Api_File::fileQuota();
            $apiObj->dir_id = 3;
            $rtn = $apiObj->getResult();
            if (!empty($rtn))
            {
                $cacheFile->setVdiskUsage($uid, $rtn);
            }

            return $rtn;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 文件数据适配器
     * @param  [type] $dataMss [description]
     * @return [type]           [description]
     */
    private static function _translateFileFormat($dataMss)
    {
        $fileinfo = array();
        $fileinfo['uid'] = '';
        $fileinfo['name'] = $dataMss['filename'];
        $fileinfo['ctime'] = '';
        $fileinfo['dir_id'] = '';
        $fileinfo['size'] = $dataMss['filesize'];
        $fileinfo['type'] = '';
        $fileinfo['url'] = '';
        //$fileinfo['thumbnail'] = isset($dataMss['thumbnail_60']) ? $dataMss['thumbnail_60'] . "&source=" . $apiSource : '';

        $apiSource = Comm_Config::get("env.platform_api_source");

        if ($dataMss['fid'] == 0)
        {
            $fileinfo['fid'] = $dataMss['vfid'];
            $fileinfo['thumbnail'] = isset($dataMss['thumbnail_191']) ? $dataMss['thumbnail_191'] . "&source=" . $apiSource : '';
            $fileinfo['thumbnail_big'] = isset($dataMss['thumbnail_600']) ? $dataMss['thumbnail_600'] . "&source=" . $apiSource : '';
        }
        else
        {
            $fileinfo['fid'] = $dataMss['fid'];
            $fileinfo['thumbnail'] = isset($dataMss['thumbnail_120']) ? $dataMss['thumbnail_120'] . "&source=" . $apiSource : '';
            $fileinfo['thumbnail_big'] = isset($dataMss['thumbnail_600']) ? $dataMss['thumbnail_600'] . "&source=" . $apiSource : '';
        }

        $fileinfo['s3_url'] = '';
        $fileinfo['vfid'] = $dataMss['vfid'];
        $fileinfo['src_id'] = $dataMss['src_id'];
        return $fileinfo;
    }
}
