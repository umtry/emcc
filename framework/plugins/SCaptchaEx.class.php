<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."captcha/captchaex.php");
/**
 * @package SlightPHP
 */
class SCaptchaEx extends SimpleCaptchaEx{
	static $session_prefix="capcha_seed_";
	function __construct(){
		$this->wordsFile = "";
		$this->session_var = SCaptcha::$session_prefix ;
		$this->minWordLength = 4;
		$this->maxWordLength = 4;
		$this->width = 96;
		$this->height = 30;
		$this->Yamplitude = 12;
		$this->Xamplitude = 3;
		$this->scale=3;
		$this->blur = true;
		$this->imageFormat="jpeg";
		$this->transprent=false;
		$this->debug=false;
	}
	
	static function check($captcha_code){
        $captcha_code = strtolower($captcha_code);
		$_cachename = SCaptcha::$session_prefix.$captcha_code;
        
		if($captcha_code && $word = $_SESSION[$_cachename]) {
			if(strtolower($word) == strtolower($captcha_code)) {
				return true;
			}
		}
		return false;
	}
	
	static function del($captcha_code){
		$cache = new SCacheEx('memcache');
		$_cachename = SCaptcha::$session_prefix.$captcha_code;
		if($captcha_code && !empty($cache)) {//有缓存则读缓存
			$cache->del($_cachename);
			return true;
		}else{
			return false;
		}
	}
}