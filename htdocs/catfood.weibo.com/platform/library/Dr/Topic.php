<?php
class Dr_Topic extends Dr_Abstract
{
    const GET_TOPIC_LIST_URL = 'http://i.huati.weibo.com/page/usertopic?page_id=%s&uid=%s';

    /**
     * 获取用户话题列表
     * @param  [type]  $pageId   [description]
     * @param  [type]  $userId   [description]
     * @param  boolean $textOnly [description]
     * @return [type]            [description]
     */
    public static function getUserTopicList($pageId, $userId, $textOnly = true)
    {
        if (empty($pageId) || empty($userId))
        {
            return false;
        }

        try
        {
            $ownerId = substr($pageId, 6);
            $cacheObject = new Cache_Topic();
            $rtnArr = $cacheObject->getUserTopicList($ownerId); // get topic info from cache first

            if (!empty($rtnArr) && is_array($rtnArr))
            {
                return $rtnArr;
            }

            $rtnArr = array();
            $topicList = self::_getUserTopicList($pageId, $userId);
            if (empty($topicList))
            {
                return array();
            }

            if (!empty($topicList['data']['items']))
            {
                foreach ($topicList['data']['items'] as $key => $value)
                {
                    $valueArr = explode("#", $value['title']);
                    
                    if (!empty($valueArr['1']))
                    {
                        $rtnArr[$key] = $valueArr['1'];
                    }
                }
            }

            $cacheObject->createUserTopicList($ownerId, $rtnArr); // set cache
            
            return $rtnArr;
        }
        catch (Exception $e)
        {
            return array();
        }
    }

    /**
     * 获取和该用户相关的话题列表
     * {
     * error_code: 1000,
     * error_msg: "成功",
     * cache: 300,
     * data: {
     * title: "话题",
     * title_link: "http://huati.weibo.com/?from=page_100606_weibo&wvr=5.1&loc=huatititle",
     * title_link_blank: true,
     * desc: "",
     * icon_src: "",
     * icon_link: "",
     * icon_link_blank: false,
     * more_text: "更多»",
     * more_link: "http://huati.weibo.com/profile/1642041107?from=page_100606_weibo&wvr=5.1&mod=huatimore",
     * more_link_blank: true,
     * total_number: 10,
     * items: [
     * {
     * text: "[url='http://huati.weibo.com/k/%E8%8D%94%E6%9E%9D%E6%98%A5%E6%99%9A%E5%BE%AE%E7%9B%B4%E6%92%AD?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E8%8D%94%E6%9E%9D%E6%98%A5%E6%99%9A%E5%BE%AE%E7%9B%B4%E6%92%AD' alt='%E8%8D%94%E6%9E%9D%E6%98%A5%E6%99%9A%E5%BE%AE%E7%9B%B4%E6%92%AD' target='_blank']#荔枝春晚微直播#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有493人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/27535?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E6%B1%9F%E8%8B%8F%E8%9B%87%E5%B9%B4%E6%98%A5%E6%99%9A' alt='%E6%B1%9F%E8%8B%8F%E8%9B%87%E5%B9%B4%E6%98%A5%E6%99%9A' target='_blank']#江苏蛇年春晚#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有17764人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/k/%E5%A4%A9%E4%BA%AE?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E5%A4%A9%E4%BA%AE' alt='%E5%A4%A9%E4%BA%AE' target='_blank']#天亮#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有86人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/390560?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E7%BD%91%E7%9B%98%E6%8C%81%E4%B9%85%E6%88%98%EF%BC%8C%E5%BE%AE%E7%9B%98%E7%83%AD%E4%B8%AA%E8%BA%AB' alt='%E7%BD%91%E7%9B%98%E6%8C%81%E4%B9%85%E6%88%98%EF%BC%8C%E5%BE%AE%E7%9B%98%E7%83%AD%E4%B8%AA%E8%BA%AB' target='_blank']#网盘持久战，微盘热个身#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有96人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/19301?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E5%BE%AE%E7%9B%98%E7%AD%BE%E5%88%B0' alt='%E5%BE%AE%E7%9B%98%E7%AD%BE%E5%88%B0' target='_blank']#微盘签到#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有6812人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/419988?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E6%96%B0%E6%B5%AA%E9%82%AE%E7%AE%B1%E4%BB%B2%E7%A7%8B%E6%94%B6%E8%8E%B7%E5%AD%A3' alt='%E6%96%B0%E6%B5%AA%E9%82%AE%E7%AE%B1%E4%BB%B2%E7%A7%8B%E6%94%B6%E8%8E%B7%E5%AD%A3' target='_blank']#新浪邮箱仲秋收获季#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有82人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/22117?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E5%B8%A6%E7%9D%80%E5%BE%AE%E5%8D%9A%E5%8E%BB%E6%97%85%E8%A1%8C' alt='%E5%B8%A6%E7%9D%80%E5%BE%AE%E5%8D%9A%E5%8E%BB%E6%97%85%E8%A1%8C' target='_blank']#带着微博去旅行#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有469279人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/309553?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E6%98%9F%E5%BA%A7%E6%AD%A7%E8%A7%86' alt='%E6%98%9F%E5%BA%A7%E6%AD%A7%E8%A7%86' target='_blank']#星座歧视#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有3350人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/411838?from=page_100606_weibo&wvr=5.1&mod=huati' title='%E8%81%8C%E5%9C%BA%E4%B8%BD%E4%BA%BA' alt='%E8%81%8C%E5%9C%BA%E4%B8%BD%E4%BA%BA' target='_blank']#职场丽人#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有1293人赞过"
     * }
     * }
     * },
     * {
     * text: "[url='http://huati.weibo.com/84750?from=page_100606_weibo&wvr=5.1&mod=huati' title='NextDay' alt='NextDay' target='_blank']#NextDay#[/url]",
     * operate: {
     * type: "1",
     * params: {
     * text: "有6人赞过"
     * }
     * }
     * }
     * ]
     * }
     * }
     *
     * @param unknown $pageId
     * @param unknown $userId
     * @throws Comm_Exception_Program
     * @return multitype: mixed
     */
    private static function _getUserTopicList($pageId, $userId)
    {
        try
        {
            if (empty($pageId) || !is_numeric($userId))
            {
                throw new Comm_Exception_Program('argument $uid must be not empty and is numeric');
            }

            $url = sprintf(self::GET_TOPIC_LIST_URL, $pageId, $userId);
            $response = Tool_Http::get($url);
            $result = json_decode($response, true);

            if ($result ['errorCode'] != 1000)
            {
                //Tool_Log::error($url . '|' . $response);
                return array();
            }

            return $result;
        }
        catch (Exception $e)
        {
            return array();
        }
    }
}