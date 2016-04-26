<?php
class Tool_PictureId
{
    const PHOTO_URL_CRC = "http://ww%d.sinaimg.cn/%s/%s.%s";
    const PHOTO_URL_CRC_BACKUP = "http://wb%d.sina.cn/%s/%s.%s";
    const PHOTO_URL = "http://ss%d.sinaimg.cn/%s/%s&690";
    const HEAD_PIC_URL = "http://tp%d.sinaimg.cn/%d/%d/%s";

    /**
     * 根据图片ID以及类型获得图片url
     * @param string $pictureId
     * @param string $type
     * @param boolean $wap
     */
    public static function getPictureUrl($pictureId, $type = 'bmiddle', $wap = false)
    {
        if (!is_array($pictureId))
        {
            $pictureId = array($pictureId);
        }

        $urlF = $wap ? "http://wp%d.sina.cn/%s/%s.%s" : "http://ww%d.sinaimg.cn/%s/%s.%s";
        $urls = array();

        foreach ($pictureId as $pid)
        {
            $url = '';

            if ($pid[9] === 'w')
            {
                $zone = (crc32($pid) & 3) + 1;
                $ext = (($pid[21] == 'g') ? 'gif' : 'jpg');
                $url = sprintf($urlF, $zone, $type, $pid, $ext);
            }
            else
            {
                if ("&690" == substr($pid, strlen($pid) - 4, 4))
                {
                    $pid = substr($pid, 0, strlen($pid) - 4);
                }

                $zone = (hexdec(substr($pid, -2))%16) + 1;
                $url = "http://ss{$zone}.sinaimg.cn/{$type}/{$pid}&690";
            }

            $urls[$pid] = $url;
        }


        return $urls;
    }

    /**
     * 根据图片地址获取图片ID
     * @param string $pictureUrl
     */
    public static function getPidByUrl($pictureUrl)
    {
        //$patten = '/^http\:\/\/[a-zA-Z0-9]?[a-zA-Z0-9\-\.]*\/[a-zA-Z0-9]+\/([a-zA-Z0-9]+).[a-zA-Z]+$/i';
        $patten = '/^http\:\/\/[a-zA-Z0-9]?[a-zA-Z0-9\-\.]*\/[a-zA-Z0-9]+\/([a-zA-Z0-9]+)(\.[a-zA-Z]+)?$/i';

        if (preg_match($patten, $pictureUrl, $matches))
        {
            $pid = $matches[1];
            return $pid;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取用户头像url
     * @param string $uid
     * @param int $width
     */
    public static function getHeadPicUrl($uid, $width = 50)
    {
        $mod = $uid % 4 + 1;
        return sprintf(self::HEAD_PIC_URL, $mod, $uid, $width, '5659021755/0');
    }

    /**
     * 获取图床标准图片尺寸
     * @param string $type
     */
    public static function getImageBedSizeInfo($type)
    {
        $sizeInfo = array(
            'w' => 0,
            'h' => 0,
            'r' => 0.0,
            'i' => false,
            'o' => false,
            'f' => 'JPEG',
            't' => '',
        );

        switch ($type)
        {
            case 'mw1024':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 1024;
                break;

            case 'bmiddle':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 440;
                break;

            case 'small':
                $sizeInfo['t'] = 'I';
                $sizeInfo['w'] = $sizeInfo['h'] = 200;
                break;

            case 'thumbnail':
                $sizeInfo['t'] = 'I';
                $sizeInfo['w'] = $sizeInfo['h'] = 120;
                break;

            case 'nmw690':
                break;

            case 'mw690':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 690;
                $sizeInfo['i'] = true;
                break;

            case 'mw205':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 205;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'cmw205':
                $sizeInfo['t'] = 'II';
                $sizeInfo['r'] = 3.0 / 5.0;
                $sizeInfo['w'] = 205;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'mw215':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 215;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'mw240':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 240;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'thumb300':
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 300;
                break;

            case 'thumb180':
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 180;
                break;

            case 'thumb150':
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 150;
                break;

            case 'square':
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 80;
                break;

            case 'thumb50':
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 50;
                break;

            case 'thumb30' :
                $sizeInfo['t'] = 'IV';
                $sizeInfo['w'] = $sizeInfo['h'] = 30;
                break;
            case 'mw720':
                break;

            case 'wap720':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 720;
                $sizeInfo['i'] = true;
                break;

            case 'wap360':
                $sizeInfo['t'] = 'I';
                $sizeInfo['r'] = 3.0 / 10.0;
                $sizeInfo['w'] = $sizeInfo['h'] = 360;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'wap320':
                $sizeInfo['t'] = 'III';
                $sizeInfo['w'] = 320;
                $sizeInfo['h'] = 480;
                $sizeInfo['o'] = 'bmiddle';
                break;

            case 'wap180':
                $sizeInfo['t'] = 'I';
                $sizeInfo['r'] = 3.0 / 10.0;
                $sizeInfo['w'] = $sizeInfo['h'] = 180;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'wap128':
                $sizeInfo['t'] = 'III';
                $sizeInfo['w'] = 128;
                $sizeInfo['h'] = 160;
                $sizeInfo['o'] = 'small';
                break;

            case 'wap50':
                $sizeInfo['t'] = 'I';
                $sizeInfo['w'] = $sizeInfo['h'] = 50;
                $sizeInfo['r'] = 3.0 / 10.0;
                $sizeInfo['o'] = 'thumbnail';
                break;

            case 'webp720':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 720;
                $sizeInfo['i'] = true;
                $sizeInfo['f'] = 'WEBP';
                break;

            case 'webp360':
                $sizeInfo['t'] = 'I';
                $sizeInfo['w'] = $sizeInfo['h'] = 360;
                $sizeInfo['r'] = 3.0 / 10.0;
                $sizeInfo['f'] = 'WEBP';
                $sizeInfo['o'] = 'mw690';
                break;

            case 'webp180':
                $sizeInfo['t'] = 'I';
                $sizeInfo['w'] = $sizeInfo['h'] = 180;
                $sizeInfo['f'] = 'WEBP';
                $sizeInfo['r'] = 3.0 / 10.0;
                $sizeInfo['o'] = 'mw690';
                break;

            case 'mw2048':
                break;

            case 'woriginal':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 2048;
                break;

            case 'large':
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 2048;
                break;

            default :
                $sizeInfo['t'] = 'II';
                $sizeInfo['w'] = 2048;
                $sizeInfo['i'] = true;
                break;
        }

        return $sizeInfo;
    }


    /**
     * 根据图片id获取图片尺寸信息
     * @param string $pictureId
     * @param string $type
     */
    public static function getImageGeometryById($pictureId, $type = 'large')
    {
        if (strlen($pictureId) < 32 || $pictureId[22] < '1')
        {
            return false;
        }

        $w = base_convert(substr($pictureId, 23, 3), 36, 10);
        $h = base_convert(substr($pictureId, 26, 3), 36, 10);
        $c = false;

        $result = array(
            'width'  => 0,
            'height' => 0,
            'croped' => false,
        );

        $imageSizeInfo = self::getImageBedSizeInfo($type);

        if ($imageSizeInfo['o'])
        {
            $ret =  self::getImageGeometryById($pictureId, $imageSizeInfo['o']);

            if (!$ret)
            {
                return false;
            }

            $w = $ret['width'];
            $h = $ret['height'];
            $c = $ret['croped'];
        }

        switch ($imageSizeInfo['t'])
        {
            case 'I' :
                if ($w <= $imageSizeInfo['w'] && $h <= $imageSizeInfo['h'])
                {
                    $result['width'] = $w;
                    $result['height'] = $h;
                    break;
                }

                if ($imageSizeInfo['r'] > 0.0)
                {
                    $r = $w / $h;

                    if ($r < $imageSizeInfo['r'])
                    {
                        $result['croped'] = true;

                        /* too hight */
                        if ($w <= ($imageSizeInfo['w'] * $imageSizeInfo['r']))
                        {
                            $result['width'] = $w;
                            $result['height'] = $imageSizeInfo['h'];
                            break;
                        }
                        else
                        {
                            $h = floor($w / $imageSizeInfo['r']);
                        }
                    }
                    else if ($r > 1 / $imageSizeInfo['r'])
                    {
                        $result['croped'] = true;

                        /* too wide */
                        if ($h <= ($imageSizeInfo['h'] * $imageSizeInfo['r']))
                        {
                            $result['width'] = $imageSizeInfo['w'];
                            $result['height'] = $h;
                            break;
                        }
                        else
                        {
                            $w = floor($h / $imageSizeInfo['r']);
                        }
                    }
                }

                if ($w > $h)
                {
                    $result['width'] = $imageSizeInfo['w'];
                    $result['height'] = floor($h * ($imageSizeInfo['w'] / $w));
                }
                else
                {
                    $result['height'] = $imageSizeInfo['h'];
                    $result['width'] = floor($w * ($imageSizeInfo['h'] / $h));
                }

                break;

            case 'II' :
                if ($w <= $imageSizeInfo['w'])
                {
                    $result['width'] = $w;
                    $result['height'] = $h;
                    break;
                }

                if ($imageSizeInfo['r'] > 0.0)
                {
                    $r = $w / $h;

                    if ($r < $imageSizeInfo['r'])
                    {
                        $h = floor($w / $imageSizeInfo['r']);
                        $result['croped'] = true;
                    }
                    else if ($r > 1 / $imageSizeInfo['r'])
                    {
                        $result['width'] = $imageSizeInfo['w'];
                        $result['height'] = $h;
                        $result['croped'] = true;
                        break;
                    }
                }

                $result['width'] = $imageSizeInfo['w'];
                $result['height'] = floor($h * ($imageSizeInfo['w'] / $w));
                break;

            case 'III' :
                $p = $w / $h;
                $q = $imageSizeInfo['w'] / $imageSizeInfo['h'];

                if ($p > $q)
                {
                    $result['width'] = $imageSizeInfo['w'];

                    if ($w < $imageSizeInfo['w'])
                    {
                        $result['height'] = $h;
                        break;
                    }

                    $result['height'] = floor($h * ($imageSizeInfo['w'] / $w));
                }
                else
                {
                    $result['height'] = $imageSizeInfo['h'];

                    if ($h < $imageSizeInfo['h'])
                    {
                        $result['width'] = $w;
                        break;
                    }

                    $result['width'] = floor($w * ($imageSizeInfo['h'] / $h));
                }

                break;

            case 'IV' :
                $result['width'] = $imageSizeInfo['w'];
                $result['height'] = $imageSizeInfo['h'];
                break;

            default :
                return false;
        }

        if ($c)
        {
            $result['croped'] = true;
        }

        return $result;
    }

    /**
     * 根据图片url获取图片尺寸信息
     * @param string $pictureUrl
     * @param string $type
     */
    public static function getImageGeometryByUrl($pictureUrl, $type = 'large')
    {
        return self::getImageGeometryById(self::getPidByUrl($pictureUrl), $type);
    }

    /**
     * 根据图片id获取图片格式信息
     * @param string $pictureId
     * @param string $type
     */
    public static function getImageFormatById($pictureId, $type = 'bmiddle')
    {
        if ($pictureId[9] == 'w')
        {
            if ($pictureId[21] == 'j')
            {
                $s = self::getImageBedSizeInfo($type);
                return $s['f'];
            }

            return 'GIF';
        }

        return false;
    }

    /**
     * 根据图片url获取图片格式信息
     * @param string $pictureUrl
     * @param string $type
     */
    public static function getImageFormatByUrl($pictureUrl, $type = 'bmiddle')
    {
        return self::getImageFormatById(self::getPidByUrl($pictureUrl), $type);
    }

    /**
     * 根据图片id获取large图片文件大小
     * @param string $pictureId
     * @param string $unit
     */
    public static function getImageLargeFileSizeById($pictureId, $unit = 'kb')
    {
        $size = 0;

        if (strlen($pictureId) >= 32 && $pictureId[22] >= '2')
        {
            $dn = base_convert(substr($pictureId, 29, 3), 36, 10);
            $base = $dn & 0x3FF;
            $exp = ($dn >> 10) & 0x3;
            $rem = ($dn >> 12) & 0xF;
            $size = ($base) << ($exp * 10);
            $size += (($rem * 93) << (($exp > 0) ? ($exp - 1) : $exp) * 10);
        }

        if ('kb' == strtolower($unit))
        {
            $size = ceil($size / 1024);
        }

        if ('mb' == strtolower($unit))
        {
            $size = round(($size / 1024 / 1024), 2);
        }

        return $size;
    }

    /**
     * 根据图片url获取large图片文件大小
     * @param string $pictureUrl
     * @param string $unit
     */
    public static function getImageLargeFileSizeByUrl($pictureUrl, $unit = "kb")
    {
        return self::getImageLargeFileSizeById(self::getPidByUrl($pictureUrl), $unit);
    }
}
