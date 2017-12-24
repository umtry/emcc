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
/**
 * @package SlightPHP
 */
if(!class_exists("SlightPHP",false)):
final class SlightPHP{
	/**
	 * @var string
	 */
	public static $appDir=".";
    public static $dataurl = '';
    
    /**
	 * @var string
	 */
	public static $templatesDir="default";

	/**
	 * @var string
	 */
	public static $pathInfo="";

	/**
	 * current zone
	 * @var string
	 */
	public static $zone;
	/**
	 * @var string
	 */
	public static $defaultZone="Controller";
	
	/**
	 * current page
	 * @var string
	 */
	public static $page;
	/**
	 * @var string
	 */
	public static $defaultPage="main";
	/**
	 * current entry
	 * @var string
	 */
	public static $entry;
	/**
	 * @var string
	 */
	public static $defaultEntry="index";
	/**
	 * split flag of zone,classs,method
	 *
	 * @var string
	 */
	public static $splitFlag="/";

	/**
	 * zoneAlias
	 *
	 * @var array
	 */
	public static $zoneAlias;

    public static $urlFormat="-";
    public static $urlSuffix="html";
    
    /**
     * routingRules
     * 特殊路由规则
     */
    public static $routingRules="";
    
	/**
	 * @param string $zone
	 * @param string $alias
	 * @return boolean
	 */
	public static function setZoneAlias($zone,$alias){
		self::$zoneAlias[$zone]=$alias;
		return true;
	}
	/**
	 * @param string $zone
	 * @return string | boolean
	 */
	public static function getZoneAlias($zone){
		return isset(self::$zoneAlias[$zone]) ? self::$zoneAlias[$zone] : false;
	}
	
    //Fenngle添加
    public static function setUrlFormat($value){
        self::$urlFormat=$value;
        return true;
    }
    public static function getUrlFormat(){
        return self::$urlFormat;
    } 
    public static function setUrlSuffix($value){
        self::$urlSuffix=$value;
        return true;
    }    
    public static function getUrlSuffix(){
        return self::$urlSuffix;
    }
    
    
    /**
     *  系统生成路径 
     */
    public static function createUrl($route,$params=array()) {	
        $uf = SlightPHP::$urlFormat;
        $sux = '.'.SlightPHP::$urlSuffix;	
        $url = rtrim($route,SlightPHP::$urlFormat);
        $routeNew = str_replace('/','',$route);		
        switch ($routeNew) {
            case 'xxxxxx':
                return $url;
                break;
            default:
                if(!empty($params)) {			
                    foreach($params as $key=>$value) {
                        $tmp.= $key.$uf.$value.$uf;
                    }
                    $tmp = rtrim($tmp,$uf);
                    $fvar = substr($url,strlen($url)-1,1);
                    if($fvar == '/') {
                        $url=rtrim($url.$tmp,SlightPHP::$urlFormat);
                    } else {
                        $url=rtrim($url.SlightPHP::$urlFormat.$tmp,SlightPHP::$urlFormat);				
                    }									
                }
                return $route==='' ? $url : $url.$sux;	
                break;
        }
    }
    
    /**
    * 框架所在绝对路径
    */
    public static function getSDir(){
	return str_replace("\\", DIRECTORY_SEPARATOR, dirname(__FILE__)) . DIRECTORY_SEPARATOR;
    }
    
	/**
	 * defaultZone set
	 * 
	 * @param string $zone
	 * @return boolean
	 */

	public static function setDefaultZone($zone){
		self::$defaultZone = $zone;
		return true;
	}
	/**
	 * defaultZone get
	 * 
	 * @return string
	 */

	public static function getDefaultZone(){
		return self::$defaultZone;
	}
	/**
	 * defaultClass set
	 * 
	 * @param string $page
	 * @return boolean
	 */
	public static function setDefaultPage($page){
		self::$defaultPage = $page;
		return true;
	}
	/**
	 * getDefaultClass get
	 * 
	 * @return string
	 */
	public static function getDefaultPage(){
		return self::$defaultPage;
	}
	/**
	 * defaultMethod set
	 * 
	 * @param string $entry
	 * @return boolean
	 */
	public static function setDefaultEntry($entry){
		self::$defaultEntry = $entry;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string $method
	 */
	public static function getDefaultEntry(){
		return self::$defaultEntry;
	}
	/**
	 * splitFlag set
	 * 
	 * @param string $flag
	 * @return boolean
	 */
	public static function setSplitFlag($flag){
		self::$splitFlag = $flag;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string
	 */
	public static function getSplitFlag(){
		return self::$splitFlag;
	}
	/**
	 * appDir set && get
	 * IMPORTANT: you must set absolute path if you use extension mode(extension=SlightPHP.so)
	 *
	 * @param string $dir
	 * @return boolean
	 */

	public static function setAppDir($dir){
		self::$appDir = $dir;
		return true;
	}
    
    public static function setTemplatesDir($dir){
        self::$templatesDir = $dir;
		return true;
    }
    
	public static function setPathInfo($pathInfo){
		self::$pathInfo = $pathInfo;
		return true;
	}
	/**
	 * appDir get
	 * 
	 * @return string
	 */
	public static function getAppDir(){
		return self::$appDir;
	}
    /**
	 * templatesDir get
	 * 
	 * @return string
	 */
	public static function getTemplatesDir(){
		return self::$templatesDir;
	}
	/**
	 * debug status set
	 *
	 * @param boolean $debug
	 * @return boolean
	 */
	public static function setDebug($debug){
		self::$_debug = $debug;
		return true;
	}
	/**
	 * debug status get
	 * 
	 * @return boolean 
	 */
	public static function getDebug(){
		return self::$_debug;
	}
    
    /**
     * 设置特殊路由规则
     */
    public static function setRoutingRule($route){
        self::$routingRules = $route;
        return true;
    }
    /**
     * 获取特殊路由规则
     */
    public static function getRoutingRule(){
        return self::$routingRules;
    }

	/**
	 * main method!
	 *
	 * @param string $path
	 * @return boolean
	 */

	final public static function run($path=""){
        @date_default_timezone_set('PRC');
        mb_internal_encoding("UTF-8");

        if($_SERVER['SERVER_PORT'] == '443') define('HTTPS', true);
        //{{{
        $splitFlag = preg_quote(SlightPHP::$splitFlag,"/");
        $path_array = array();
        if(!empty($path)){
            $isPart = true;
            $path_array = preg_split("/[$splitFlag\/]/",$path,-1,PREG_SPLIT_NO_EMPTY);
        }else{
            $isPart = false;
            $url    = !empty($_SERVER['REDIRECT_SCRIPT_URL'])?$_SERVER['REDIRECT_SCRIPT_URL']:(!empty($_SERVER["PATH_INFO"])?$_SERVER["PATH_INFO"]:"");
            if(!empty($url))$path_array = preg_split("/[$splitFlag\/]/",$url,-1,PREG_SPLIT_NO_EMPTY);
        }

        $zone    = !empty($path_array[0]) ? $path_array[0] : SlightPHP::$defaultZone ;
        $page    = !empty($path_array[1]) ? $path_array[1] : SlightPHP::$defaultPage ;
        $entry    = !empty($path_array[2]) ? $path_array[2] : SlightPHP::$defaultEntry ;
             
        if(SlightPHP::$zoneAlias && ($key = array_search($zone,SlightPHP::$zoneAlias))!==false){
            $zone = $key;
        }
        if(!$isPart){    
            SlightPHP::$zone    = $zone;
            SlightPHP::$page    = $page;
            SlightPHP::$entry    = $entry;
        }else{   
            if($zone == SlightPHP::$zone && $page == SlightPHP::$page && $entry == SlightPHP::$entry){
                SlightPHP::debug("part ignored [$path]");
                return;
            }
        }
        
        //加载特殊路由规则
        if(!empty(SlightPHP::$routingRules)){
            foreach(SlightPHP::$routingRules as $val){
                if(empty($val['sp_preg_match']) && empty($val['se_preg_match'])){
                    if($page == $val['source_page'] && $entry == $val['source_entry']){
                        $page = $val['des_page'];
                        $entry = $val['des_entry'];
                    }
                }
                
                if($val['sp_preg_match']){
                    if(preg_match("/".$val['sp_preg_match']."/",$page) && $entry == $val['source_entry']){
                        $_GET[$val['source_page']] = $page;
                        $page = $val['des_page'];
                        $entry = $val['des_entry'];
                    }
                }
                
                if($val['se_preg_match']){
                    if($page == $val['source_page'] && preg_match("/".$val['se_preg_match']."/",$entry)){
                        $_GET[$val['source_entry']] = $entry;
                        $entry = $val['des_entry'];
                        $page = $val['des_page'];
                    }
                }
                
            }
        }

        $app_file = SlightPHP::$appDir . DIRECTORY_SEPARATOR . $zone . DIRECTORY_SEPARATOR . $page . ".page.php";
        if(!file_exists($app_file)){
            SlightPHP::debug("file[$app_file] not exists");
            return false;
        }else{       
            require_once(realpath($app_file));
        }
        
        $method = "Page".$entry;
        $classname = $zone ."_". $page;
        self::$dataurl = $page.'_'.$entry;     
        
        if(!class_exists($classname)){
            SlightPHP::debug("class[$classname] not exists");
            return false;
        }
        $classInstance = new $classname;
        if(!method_exists($classInstance,$method)){
            SlightPHP::debug("method[$method] not exists in class[$classname]");
            return false;
        }
        $path_array[0] = $zone;
        $path_array[1] = $page;
        $path_array[2] = $entry;
        return call_user_func(array(&$classInstance,$method),$path_array);

    }

	/**
	 * @var boolean
	 */
	private static $_debug=0;

	/*private*/
	private static function debug($debugmsg){
		if(self::$_debug){
			error_log($debugmsg);
			echo "<!--slightphp debug: ".$debugmsg."-->";
		}
	}
}
endif;
