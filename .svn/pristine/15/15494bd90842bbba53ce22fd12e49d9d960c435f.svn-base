<?php
class Dr_Face extends Dr_Abstract
{
    public static $faceList = array();
    public static $faceListLite = array();
    
    /**
     * @TODO 按类型获取表情列表 
     * @param $faceType 表情类型 
     * @return array(do_face,do_face)
     */
    public static function getFaceList($faceType = 'face', $language = null)
    {
        $cacheKey = $faceType . $language;
        if (isset(self::$faceList[$cacheKey]))
        {
            return self::$faceList[$cacheKey];
        }

        $cacheObject = new Cache_Face();
        $list = $cacheObject->getList($faceType, $language);
        if ($list)
        {
            self::$faceList[$cacheKey] = $list;
            return $list;
        }

        $commWeiboApiFace = Comm_Weibo_Api_Statuses::emotions();
        $commWeiboApiFace->type = $faceType;
        if (!is_null($language))
        {
            $commWeiboApiFace->language = $language;
        }
        
        try
        {
            $arrFace = $commWeiboApiFace->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        if (!is_array($arrFace) || empty($arrFace))
        {
            throw new Comm_Weibo_Exception_Api("error_data");
        }

        foreach ($arrFace as $oneFace)
        {
            $doFaces[] = new Do_Face($oneFace, "output");
        }

        self::$faceList[$cacheKey] = $doFaces;
        $cacheObject->createList($faceType, $language, $doFaces);

        return $doFaces;
    }
    
    /**
     * @TODO 按类型获取表情列表
     * @param $face_type 表情类型
     * @return array(do_face,do_face)
     */
    public static function getFaceListLite($faceType = 'face', $language = null)
    {
        $cacheKey = $faceType . $language;
        if (isset(self::$faceListLite[$cacheKey]))
        {
            return self::$faceListLite[$cacheKey];
        }

        $cacheObject = new Cache_Face();
        $list = $cacheObject->getLite($faceType, $language);
        if ($list)
        {
            self::$faceListLite[$cacheKey] = $list;
            return $list;
        }

        $commWeiboApiFace = Comm_Weibo_Api_Statuses::emotions();
        $commWeiboApiFace->type = $faceType;
        if (!is_null($language))
        {
            $commWeiboApiFace->language = $language;
        }
        
        try
        {
            $arrFace = $commWeiboApiFace->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        if (!is_array($arrFace) || empty($arrFace))
        {
            throw new Comm_Weibo_Exception_Api("error_data");
        }

        foreach ($arrFace as $oneFace)
        {
            // 只保存需要的数据
            $requiredData = array(
                'type' => $oneFace ['type'],
                'phrase' => $oneFace ['phrase'],
                'url' => $oneFace ['url'],
            );

            $doFaces[$oneFace['phrase']] = new Do_Face($requiredData, "output");
        }

        self::$faceListLite[$cacheKey] = $doFaces;
        $cacheObject->createLite($faceType, $language, $doFaces);

        return $doFaces;
    }
}
