<?php
class WapSso {
	private $_gsid_url = "http://i.api.weibo.cn/interface/f/login/getUidFromGsid.php?gsid=%s";
	private $_cookie_time = 172800;	//登陆后,cookie保存2天时间
	private $_priv_key = "X3*&$#cl_ub&wap322102"; //私钥
	private $_live_time = 600 ;//10分钟内有动作算活动
	public function __construct() {
	
	}

	//is_jump ==true表示，未登陆成功，则跳转到登陆页
	public function isLogin($is_jump = true) {
		if (!$this->_isLogin()) {
			//未登陆,则销毁所有cookie
			$this->_destroyCookie();
			if($is_jump) {
				$this->_goToLogin();
			}
			return false;
		}
		//登陆状态
		return true;
	}
	//获取用户uid
	public function getCookieUid() {
		if($this->_uid) {
			return $this->_uid;	
		}
		return $_COOKIE['cb_uid'];	
	}

	private function _goToLogin() {
		/*
		   backURL
		   登录成功返回地址，需要urlencode

		   backTitle
		   登录页面返回链接标题，GBK编码，需要urlencode

		   ns
		   是否需要接受手机新浪网SESSION参数，1为不接受，默认为0(接受)

		   revalid
		   是否强制手动登录，1为不使用gsid自动登录，2为不使用gsid和网关信息自动登录，默认为0(支持自动登录)
		 */

        $request_uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        $call_back = "http://club.weibo.cn".$request_uri;
        $url = "http://3g.sina.com.cn/prog/wapsite/sso/login.php?backURL=%s&backTitle=%s&ns=0&revalid=0";
        $url = sprintf($url, urlencode($call_back), urlencode(iconv("UTF-8", "GBK", "达人特权")));
		header("Location:".$url);
		exit;
	}

	//检查用户是否为登陆状态
	private function _isLogin() {
		$cookie_gsid = $_GET['gsid'];
        if (!isset($_COOKIE['gsid_CTandWM'])) {
            $expires = time()+$this->_cookie_time;
            $this->_setDomainSidToken('gsid_CTandWM', $cookie_gsid , $expires);
        }
		if($cookie_gsid == '') {
			$cookie_gsid = $_COOKIE['gsid_CTandWM'];
		}
        //来自内嵌客户端标记 0|1 非客户端|客户端
        $ua = 0;
        if ($_COOKIE['ua'] == 1) {
            $ua = 1;
        } else if (strpos($_SERVER['HTTP_X_USER_AGENT'], '__weibo__') !== false) {
            $ua  = 1;
        } 
		//uid
		$cookie_uid = intval($_COOKIE['cb_uid']);
		//加密串
		$cookie_key = $_COOKIE['cb_k'];
		//上次登陆时间
		$cookie_timestamp = intval($_COOKIE['cb_t']);
		if($cookie_gsid && ($cookie_uid =='' || $cookie_key =='' || $cookie_timestamp == '' ) ) {
			$uid = $this->_getUidFromGsid($cookie_gsid);
			if(!$uid) {
				//验证不通过，未登陆状态
				return false;
			}
            /*
            //检查uid 防止恶意修改cookie
            if($uid != $_COOKIE['_WEIBO_UID'] && isset($_COOKIE['_WEIBO_UID']) && $ua != 1) {
                return false;
            }
             */
			//验证通过,种cookie
			return $this->_createLoginCookie($uid, $cookie_gsid);
		}
        /*
        //检查uid 防止恶意修改cookie
        $weibo_cn_uid = $_COOKIE['_WEIBO_UID'];
        if ($cookie_uid != $weibo_cn_uid  && $ua != 1) {
            return false;
        }
        */
		if(time() - $cookie_timestamp > $this->_cookie_time) {
			//超过有效期,失效
			return false;
		}

		$key = $this->_createKey($cookie_uid, $cookie_timestamp, $this->_priv_key, $cookie_gsid);
        //验证失败
		if ($key !== $cookie_key) {
            //内嵌客户端失败 有可能为第一次切换帐号 cookie没有完全清空导致 所以再中一遍 cookie
            if ($ua == 1 && $cookie_gsid) {
                $uid = $this->_getUidFromGsid($cookie_gsid);
                if (!$uid) {
                    return false;
                }
                return $this->_createLoginCookie($uid, $cookie_gsid);
            }
			//校验失败
			return false;	
		}
		//校验成功
		if(time() - $cookie_timestamp > $this->_live_time) {
			//当用户在活动，且大于cookie中的时间超过10分钟，小于cookie_time天，那么重新生成新cookie，以延续登陆状态
			$this->_createLoginCookie($cookie_uid, $cookie_gsid);
		}
		return true;
	}

	//生成key
	private function _createKey($uid, $timestamp, $priv_key, $gsid) {
			return md5($uid."_".$timestamp."_".$priv_key."_".$gsid);
	}

	private function _getUidFromGsid($gsid) {
		$url = sprintf($this->_gsid_url, $gsid);
		$re = "";
		if(!$this->_CurlByGet($url, $re, 3)) {
			return false;	
		}
		//检测返回值是否是合法的uid
		if (!is_numeric($re) || strlen($re) < 5) {
		    return false;
		}
		//返回用户信息
		return $re;
	}

	private function _CurlByGet($url, &$re, $timeout=1) {
		$re = false;
		$retry = 1;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		for($i=1;$i<=$retry;$i++) {
			if($re !== false) break;
			$re = curl_exec($ch);
			if ( is_string($re) && strlen($re) ) {
				curl_close($ch);
				$return = 'info';
			} else {
				if($i == $retry) {
					$curl_error = curl_error($ch);
					curl_close($ch);
				}
			}

			if($return=='info') return true;
		}
		return false;
	}
	//创建登陆状态cookie
	private function _createLoginCookie($uid, $gsid) {
		if(!$uid)  {
			return false;	
		}
		$timestamp = time();
		$key = $this->_createKey($uid, $timestamp, $this->_priv_key, $gsid);
		$expires = $timestamp+$this->_cookie_time;
		$this->_setCookieFunc('cb_uid', $uid, $expires);
		$this->_setCookieFunc('cb_t', $timestamp, $expires);
		$this->_setCookieFunc('cb_k', $key, $expires);
		$this->_setCookieFunc('gsid_CTandWM', $gsid, $expires);
		$this->_uid = $uid;	//首次登陆，拿到uid
		return true;
	}
	//退出，销毁cookie
	private function _destroyCookie() {
		$expires = time() - 3600;
		$this->_setCookieFunc('cb_uid', '', $expires);
		$this->_setCookieFunc('cb_t', '', $expires);
		$this->_setCookieFunc('cb_k', '', $expires);
		$this->_setCookieFunc('gsid_CTandWM', '', $expires);
		return true;
	}
	private function _setCookieFunc($name, $value, $endtime) {
		return setcookie($name, $value, $endtime, "/", "club.weibo.cn", false);
	}
    public function _setDomainSidToken($name, $value, $endtime) {
		return setcookie($name, $value, $endtime, "/", "weibo.cn", false);
    }
}
?>
