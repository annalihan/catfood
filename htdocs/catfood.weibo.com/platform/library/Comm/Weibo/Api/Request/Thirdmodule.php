<?php
//TODO
class Comm_Weibo_Api_Request_ThirdModule extends Comm_Weibo_Api_Request_Abstract
{
    public function __construct($url, $method = false)
    {
        parent::__construct($url, $method);

        $this->httpRequest->addCookie("SUE", isset($_COOKIE["SUE"]) ? $_COOKIE["SUE"] : '', true);
        $this->httpRequest->addCookie("SUP", isset($_COOKIE["SUP"]) ? $_COOKIE["SUP"] : '', true);
    }
}
