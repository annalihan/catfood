<?php
//TODO
class Comm_Weibo_Api_Request_Vote extends Comm_Weibo_Api_Request_Abstract
{
    public static $voteApiServerName = 'http://vote.i.t.sina.com.cn';
    public static $voteVersion = 4;

    public function __construct($url, $method, $type)
    {
        parent::__construct($url, $method);
        if (strtoupper($method) == "POST")
        {
            $this->httpRequest->addPostField("type", $type);
            $this->httpRequest->addPostField("version", self::$voteVersion);
        }
        else
        {
            $this->httpRequest->addQueryField("type", $type);
            $this->httpRequest->addQueryField("version", self::$voteVersion);
        }
    }

    /**
     * 投票接口URL拼接方法
     * @param string $resource 请求的资源（企业微博、微博、手机等）
     */
    public static function assembleVoteApiUrl($resource)
    {
        $url = self::$voteApiServerName . '/' . $resource;
        return $url;
    }

    /**
     * 添加当前用户uid规则的统一方法
     */
    public function supportCuid()
    {
        parent::addRule("cuid", "int", true);
    }

    /**
     * 添加来源规则的统一方法
     */
    public function supportFrom()
    {
        parent::addRule("from", "string", false);
    }

    public function getResult($throwException = true, $default = array())
    {
        parent::send();

        $content = $this->httpRequest->getResponseContent();
        $httpCode = $this->httpRequest->getResponseInfo('http_code');
        if ($httpCode != '200' || empty($content))
        {
            if ($throwException)
            {
                throw new Comm_Weibo_Exception_Api('Http Error:' . $httpCode, $httpCode);
            }
            else
            {
                return $default;
            }
        }

        $result = Tool_Misc::jsonDecode($content);

        if ($result['code'] !== 'A00006')
        {
            $msg = isset($result['msg']) ? $result['msg'] : $result['error'];
            if ($throwException)
            {
                throw new Comm_Weibo_Exception_Api($msg, $result['code']);
            }
            else
            {
                return $default;
            }
        }

        return $result['data'];
    }
}
