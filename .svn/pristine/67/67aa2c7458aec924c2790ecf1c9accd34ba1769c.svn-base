<?php
class Tool_Formatter_Face
{
    public static $faceType = array(
        'small' => 1, 
        'medium' => 1,
        'big' => 1,
    );
    
    public static $faceUrls = array();
    
    /**
     * 判断用户是否上传了头像(规则:URL的倒数第二位参数为0 例如:http://xxx.xxx.xxx/xx/xx/0/xx)
     * 
     * @return BOOL
     */
    public static function checkHasProfileImage($profileImageUrl)
    {
        if ($profileImageUrl)
        {
            $tmpArr = explode('/', $profileImageUrl);

            return $tmpArr[count($tmpArr) - 2] != 0;
        }

        return false;
    }
    
    /**
     * 将默认头像格式化为V4版对应头像
     * @param string $gender
     * @param string $type
     */
    public static function formatProfileImage($gender, $type = 'medium')
    {
        $type = isset(self::$faceType[$type]) ? $type : 'medium';
        $key = $gender . '_' . $type;

        if (!isset(self::$faceUrls[$key]))
        {
            $profileImageUrl = Comm_Config::get("env.css_domain") . 'style/images/face/' . Tool_Formatter_Gender::formatToEnName($gender) . '_' . $type . '.png';
            self::$faceUrls[$key] = $profileImageUrl;
        }

        return self::$faceUrls[$key];
    }
    
    /**
     * 将默认头像转化为带上传提示的头像
     * @param string $profileImageUrl
     */
    public static function convertToUpload($profileImageUrl)
    {
        return str_replace('.png', '_uploading.png', $profileImageUrl);
    }
}