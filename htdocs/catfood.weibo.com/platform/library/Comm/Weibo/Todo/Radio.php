<?php
//TODO 专业版
class Comm_Weibo_Radio
{

    // 获取微电台信息
    const GET_RADIO_INFO = 'http://i.service.t.sina.com.cn/radio/radio/getdjinfobyuid.php';

    /**
     * 获得获得微电台数据
     *
     * @param int $uid
     *            接口：http://i.service.t.sina.com.cn/radio/radio/getdjinfobyuid.php
     *
     *            参数：uid （Post方式传递官方微博id）
     *
     *
     *
     *            例如：curl -d "uid=1764974680"
     *            http://i.service.t.sina.com.cn/radio/radio/getdjinfobyuid.php
     *
     *
     *
     *            返回值：
     *
     *            array(5) {
     *
     *            ["errno"]=>int(1)
     *
     *            ["errmsg"]=>string(6) "成功"
     *
     *            ["radio_link"]=>string(37)
     *            http://radio.weibo.com/sichuan/fm1026
     *
     *            ["publink"]=>string(42)
     *            http://verified.weibo.com/group/1764974680
     *
     *            ["djinfo"]=>array(11) {
     *
     *            [0]=>array(4) {
     *
     *            ["uid"]=>string(10) "1497181774"
     *
     *            ["url"]=>string(27) http://weibo.com/cityfmboya
     *
     *            ["screen_name"]=>string(6) "博亚"
     *
     *            ["intro"]=>string(16) "FM102.6主持人"
     *
     *            }
     *
     *            … …
     *
     *            }
     *
     *            }
     *
     *
     */
    public static function getRadioData($uid)
    {
        $result = array();
        $source = Comm_Config::get('env.platform_api_source');
        $url = self::GET_RADIO_INFO;
        $response = Tool_Http::post($url, array(
                'uid' => $uid
       ));
        $result = json_decode($response, true);
        if ($result ['errno'] != 1)
        {
            Tool_Log::info($url . '|' . $response);
            return array();
        }
        return $result;
    }
}
