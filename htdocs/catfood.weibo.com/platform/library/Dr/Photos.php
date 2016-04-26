<?php
class Dr_Photos extends Dr_Abstract
{
    /**
     * 获取用户微相册 照片列表
     * @param int $uid
     * @param int $page
     * @param int $count
     * @return array 
     */
    public static function getUserPhotos($uid, $page = 1, $count = 20)
    {
        try
        {
            $photosCache = new Cache_Photos();
            $photosInfo = $photosCache->getPhotos($uid);
            if (!empty($photosInfo))
            {
                return $photosInfo;
            }

            $comm = Comm_Weibo_Api_Photos::getUserPhotos();
            $comm->uid = $uid;
            $comm->page = $page;
            $comm->count = $count;
            $photosInfo = $comm->getResult();

            if (!empty($photosInfo))
            {
                $photosCache->setPhotos($uid, $photosInfo);
            }

            return $photosInfo;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 获取用户最新n条微博配图
     * @param int $uid
     * @param int $page
     * @param int $count
     * @return array 
     */
    public static function getWeiboPhotos($uid, $count = 20)
    {
        try
        {
            $page = 1;
            $photosCache = new Cache_Photos();
            $realCount = 20;    //默认取20条
            if ($count > $realCount)
            {
                //若大于20条,则以大数据为准
                $realCount = $count;
            }

            if ($realCount > 50)
            {
                //接口支持上限50
                $realCount = 50;    
            }

            $photosInfo = $photosCache->getWeiboPhotos($uid, $realCount);
            if (!empty($photosInfo) || is_array($photosInfo))
            {
                //安全缓存，也有可能为空，不必理会，如果真有数据5分钟后会出来
                if (isset($photosInfo['statuses']))
                {
                    $tmp = array_slice($photosInfo['statuses'], 0, $count);
                    $photosInfo['statuses'] = $tmp;
                }

                return $photosInfo;
            }
            else
            {
                $comm = Comm_Weibo_Api_Photos::getWeiboPhotos();
                $comm->uid = $uid;
                $comm->page = $page;
                $comm->count = $realCount;
                $photosInfo = $comm->getResult();

                if (!empty($photosInfo))
                {
                    $photosCache->setWeiboPhotos($uid, $realCount, $photosInfo);
                }
                else
                {
                    //安全缓存,防刷,也存5分钟
                    $photosCache->setWeiboPhotos($uid, 1000, array());
                }

            }
        } 
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        $tmp = array_slice($photosInfo['statuses'], 0, $count);
        $photosInfo['statuses'] = $tmp;
        
        return $photosInfo;
    }
}
