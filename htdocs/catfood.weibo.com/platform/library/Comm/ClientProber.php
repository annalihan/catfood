<?php
class Comm_ClientProber
{
    public $userAgent = '';
    private $_agentValues = array(
        'browser' => false, 
        'platform' => false,
        'mobile' => false,
        'robot' => false,
    );

    private static $_instance;

    public static function getInstance()
    {
        if (empty(self::$_instance))
        {
            self::$_instance = new Comm_ClientProber();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->_initAgents();
    }

    public function getAgentByType($type)
    {
        if (is_array($type))
        {
            $values = array();

            foreach ($type as $value)
            {
                $values[$value] = $this->getAgentByType($value);
            }

            return $values;
        }

        return isset($this->_agentValues[$type]) ? $this->_agentValues[$type] : false;        
    }

    /**
     * 获取User Agent
     * @param  array  $type [description]
     * @return [type]       [description]
     */
    public static function getAgent($type = array('browser', 'platform', 'mobile'))
    {
        return self::getInstance()->getAgentByType($type);
    }

    public static function getAgentString()
    {
        return self::getInstance()->userAgent;
    }

    /**
     * 是否为手机端浏览器
     * @return boolean [description]
     */
    public static function isFromMobile()
    {
        $mobile = self::getInstance()->getAgentByType('mobile');
        return $mobile !== false;
    }

    /**
     * 是否为PC端浏览器
     * @return boolean [description]
     */
    public static function isFromPC()
    {
        $mobile = self::getInstance()->getAgentByType('mobile');
        return $mobile === false;
    }

    /**
     * 是否是IE
     * @return boolean [description]
     */
    public static function isIE()
    {
        $browser = self::getInstance()->getAgentByType('browser');

        return ($browser == 'MSIE' || $browser == 'Internet Explorer');
    }

    /**
     * 获取browser version
     * @return [type]       [description]
     */
    public static function getVersion()
    {
        return self::getInstance()->getAgentByType('version');
    }

    /**
     * 获取browser
     * @return [type]       [description]
     */
    public static function getBrowser()
    {
        return self::getInstance()->getAgentByType('browser');
    }

    private function _initAgents()
    {
        $this->userAgent = Comm_Context::getServer('HTTP_USER_AGENT', '');
        if ($this->userAgent == '')
        {
            return;
        }

        $this->_initBrowser();

        $types = array('platform', 'mobile', 'robot');
        foreach ($types as $type)
        {
            $this->_initOther($type);
        }
    }

    private function _initBrowser()
    {
        $browsers = Comm_Config::get('ua.browser');

        foreach ($browsers as $search => $name)
        {
            if (stripos($this->userAgent, $search) !== false)
            {
                $this->_agentValues['browser'] = $name;
                
                /*//TODO 针对不同的浏览器
                switch ($name)
                {
                    case 'MSIE':
                    case 'Internet Explorer':
                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }*/

                if (preg_match('#' . preg_quote($search) . '[^0-9.]*+([0-9.][0-9.a-z]*)#i', $this->userAgent, $matches))
                {
                    $this->_agentValues['version'] = $matches[1];
                }
                else
                {
                    $this->_agentValues['version'] = false;
                }
            }
        }
    }

    private function _initOther($type)
    {
        $group = Comm_Config::get("ua.{$type}");
        if (is_array($group) === false)
        {
            return false;
        }

        foreach ($group as $search => $name)
        {
            if (stripos($this->userAgent, $search) !== false)
            {
                $this->_agentValues[$type] = $name;
            }
        }
    }
}
