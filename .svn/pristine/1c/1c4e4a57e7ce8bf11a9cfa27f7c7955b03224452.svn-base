<?php

class Tool_Http
{
    private static function _getFull($http)
    {
        $full = $http->getResponseInfo();
        if ($full)
        {
            $full['content'] = $http->getResponseContent();
        }
        else
        {
            $full = array('content' => $http->getResponseContent());
        }

        return $full;
    }


    /**
     * HTTP GET请求
     * @param  string  $url     请求地址
     * @param  array   $header  头内容数组
     * @param  boolean $full    是否返回全内容
     * @param  integer $timeout 超时时间,毫秒
     * @return string           返回请求结果
     */
    public static function get($url, $header = array(), $full = false, $timeout = 1000)
    {
        $http = new Comm_HttpRequest();
        $http->setUrl($url);
        $http->setMethod('GET');
        $http->setConnectTimeout($timeout);
        $http->setTimeout($timeout);

        if (is_array($header))
        {
            foreach ($header as $key => $value)
            {
                $http->addHeader($key, $value);
            }
        }

        $http->send();

        return $full ? self::_getFull($http) : $http->getResponseContent();
    }

    /**
     * HTTP POST请求
     * @param  string  $url     请求地址
     * @param  array   $post    POST的数据
     * @param  array   $header  头内容数组
     * @param  boolean $full    是否返回全内容
     * @param  integer $timeout 超时时间,毫秒
     * @return string           返回请求结果
     */
    public static function post($url, $post = array(), $header = array(), $full = false, $timeout = 1000)
    {
        $http = new Comm_HttpRequest();
        $http->setUrl($url);
        $http->setMethod('POST');
        $http->setConnectTimeout($timeout);
        $http->setTimeout($timeout);

        if (is_array($header))
        {
            foreach ($header as $key => $value)
            {
                $http->addHeader($key, $value);
            }
        }

        if (is_array($post))
        {
            foreach ($post as $key => $value)
            {
                $http->addPostField($key, $value, 'urlEncode');
            }
        }

        $http->send();

        return $full ? self::_getFull($http) : $http->getResponseContent();
    }

    /**
     * 文件上传请求
     * @param  string  $url     请求地址
     * @param  string  $name    文件名参数
     * @param  string  $file    文件路径
     * @param  array   $post    POST的数据
     * @param  array   $header  头内容数组
     * @param  boolean $full    是否返回全内容
     * @param  integer $timeout 超时时间,毫秒
     * @return string           返回请求结果
     */
    public static function upload($url, $name, $file, $post = array(), $header = array(), $full = false, $timeout = 3000)
    {
        $http = new Comm_HttpRequest();
        $http->setUrl($url);
        $http->setMethod('POST');
        $http->addPostFile($name, $file);
        $http->setConnectTimeout($timeout);
        $http->setTimeout($timeout);

        if (is_array($header))
        {
            foreach ($header as $key => $value)
            {
                $http->addHeader($key, $value);
            }
        }

        if (is_array($post))
        {
            foreach ($post as $key => $value)
            {
                $http->addPostField($key, $value, 'urlEncode');
            }
        }

        $http->send();

        return $full ? self::_getFull($http) : $http->getResponseContent();
    }

    /**
     * Http 服务调用的封装
     * @param  [type]  $url     请求url
     * @param  array   $data    请求参数
     * @param  string  $method  调用方法,GET|POST
     * @param  integer $timeout 超时时间,毫秒
     * @param  string  $format  返回格式
     * @return string|array     失败抛异常
     */
    public static function callServiceByUrl($url, $data = array(), $method = 'GET', $timeout = 2000, $format='json')
    {
        $http = new Comm_HttpRequest();
        $http->setUrl($url);
        $http->setMethod($method);
        $http->setConnectTimeout($timeout);
        $http->setTimeout($timeout);

        foreach ($data as $key => $val)
        {
            if ('POST' == $method)
            {
                // 如果是上传文件，要把@去掉，addPostFile会加上
                if (is_string($val) && 0 === strpos($val, '@'))
                {
                    $val = ltrim($val, '@');
                    $http->addPostFile($key, $val);
                }
                else
                {
                    $http->addPostField($key, $val, 'urlEncodeRaw');
                }
            }
            else
            {
                $http->addQueryField($key, $val, 'urlEncodeRaw');
            }
        }

        if (false === $http->send())
        {
            $msg = sprintf('[httprequest]request error[url:%s][data:%s][error:%s]',
                $url, json_encode($data), $http->getErrorMsg()); 

            Tool_Log::error($msg);
            throw new Comm_Exception_Program($msg);
        }

        $res = $http->getResponseContent();

        if ('json' == $format)
        {
            $arr = json_decode($res, true);
            if (!is_array($arr))
            {
                $msg = sprintf('[httprequest]invalid json result[url:%s][data:%s][result:%s]',
                    $url, json_encode($data), $res);

                Tool_Log::error($msg);
                throw new Comm_Exception_Program($msg);
            }

            return $arr;
        }
        
        return $res;
    }

    /**
     * RESTful方式
     * @param  [type]  $method GET/PUT/DELETE等
     * @param  [type]  $url    [description]
     * @param  [type]  $data   [description]
     * @param  boolean $full   [description]
     * @param  integer $timeout 超时时间,毫秒
     * @return [type]          [description]
     */
    public static function restful($method, $url, $data, $full = false, $timeout = 1000)
    {
        $http = new Comm_HttpRequest();
        $http->setUrl($url);
        $http->setMethod($method);
        $http->postFields = $data;
        $http->setConnectTimeout($timeout);
        $http->setTimeout($timeout);
        $http->send();

        return $full ? self::_getFull($http) : $http->getResponseContent();
    }
}
