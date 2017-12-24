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
class SUtil{
    public static function getUrlParams($inPath) {
        $count = count($inPath);
        if($inPath[$count-1] == SlightPHP::$urlSuffix) unset($inPath[$count-1]);
        $newary = array();
        for ($i = 3; $i < count($inPath); $i++) {
            if ($i % 2) {
                $newary[$inPath[$i]] = @$inPath[$i + 1];
            }
        }
        //unset($newary[SlightPHP::$urlSuffix]);
        return $newary;
    }
    /**
    * Get Request Value
    * @param array $data ($_GET,$_POST)
    * @param string $key
    * @param bool $isnum true|false
    */
    static function getRequestValue(array $data,$key,$isnum=false,$default=null,$minLength=0,$maxLength=0){
        if(!isset($data[$key]) || empty($data[$key]) || ($isnum && !is_numeric($data[$key])) ||
            strlen($data[$key])<$minLength || ($maxLength!=0 && strlen($data[$key])>$maxLength)) {
            return $default;
        }
        return $data[$key];
    }
    /**
    *
    */
    static function log($logFile,$data){
        error_log("[".date("Y-m-d H:i:s")."]$data\r\n",3,$logFile);
    }
    /**
    *
    */
    static function getIP($long=false) {
        $cip = getenv('HTTP_CLIENT_IP');
        $xip = getenv('HTTP_X_FORWARDED_FOR');
        $rip = getenv('REMOTE_ADDR');
        $srip = @$_SERVER['REMOTE_ADDR'];
        if($cip && strcasecmp($cip, 'unknown')) {
            $ip = $cip;
        } elseif($xip && strcasecmp($xip, 'unknown')) {
            $ip = $xip;
        } elseif($rip && strcasecmp($rip, 'unknown')) {
            $ip = $rip;
        } elseif($srip && strcasecmp($srip, 'unknown')) {
            $ip = $srip;
        }
        preg_match("/[\d\.]{7,15}/", $ip, $match);
        $ip = $match[0] ? $match[0] : 'unknown';
        if($long){
            return sprintf("%u",ip2long($ip));
        }
        return $ip;
    }

    static function validEmail($email){
        return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email);
    }
    
    /**
    * get rand string
    */
    static function getRandString($len) {
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = '';
        for ($i=0; $i<$len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
    /**
    * 产生随机数
    * @param $length 产生随机数长度
    * @param $type 返回字符串类型
    * @param $hash  是否由前缀，默认为空. 如:$hash = 'zz-'  结果zz-823klis
    * @return 随机字符串 $type = 0：数字+字母
    $type = 1：数字
    $type = 2：字符
    */
    public static function random($length, $type = 0, $hash = '') {
        if ($type == 0) {
            $chars = '23456789abcdefghijkmnpqrstuvwxyz';
        } else if ($type == 1) {
            $chars = '0123456789';
        } else if ($type == 2) {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
        }
        $max = strlen ( $chars ) - 1;
        mt_srand ( ( double ) microtime () * 1000000 );
        for($i = 0; $i < $length; $i ++) {
            $hash .= $chars [mt_rand ( 0, $max )];
        }
        return $hash;
    }

    //生成二维码
    public static function getQrImg($chl,$widhtHeight ='120',$EC_level='L',$margin='1'){
        $chl = urlencode($chl);
        $qr = 'http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl;
        return $qr;
    }

    /**
    * 验证手机号码
    * Enter description here ...
    * @param unknown_type $phone
    */
    public static function validPhone($phone){
        if(strlen($phone)!=11) return false;
        return @eregi("^(13[0-9]{1}|15[0-9]{1}|18[0-9]{1}|14[0-9]{1})[0-9]{8}$",$phone);
    }

    /**
    * 安全过滤数据
    *
    * @param string    $str        需要处理的字符
    * @param string    $type        返回的字符类型，支持，string,int,float,html
    * @param maxid        $default    当出现错误或无数据时默认返回值
    * @param boolean  $checkempty 强制转化为正数
    * @return         mixed        当出现错误或无数据时默认返回值
    */
    public static function getStr($str, $type='string' ,$default='', $checkempty=false,$pnumber=false) {
        switch ($type) {
            case 'string': //字符处理
                $_str = strip_tags($str);
                $_str = str_replace("'", '&#39;', $_str);
                $_str = str_replace("\"", '&quot;', $_str);
                $_str = str_replace("\\", '', $_str);
                $_str = str_replace("\/", '', $_str);
                break;
            case 'int': //获取整形数据
                $_str = (int) $str;
                break;
            case 'float': //获浮点形数据
                $_str = (float) $str;
                break;
            case 'html': //获取HTML，防止XSS攻击
                $_str = self::reMoveXss($str);
                break;
            case 'time':
                $_str = $str?strtotime($str):'';
                break;
            default: //默认当做字符处理
                $_str = strip_tags($str);
                break;
        }
        if($checkempty==true){
            if(empty($str)){
                header("content-type:text/html;charset=utf-8;");
                exit("非法操作！");
            }
        }
        if ($str === '' || $str === NULL)return $default;
        if($type=="int" || $type=="float"){
            $_str = $pnumber==true?abs($_str):$_str;
            return $_str;
        }
        return trim($_str);
    }
    //过滤XSS攻击
    static function reMoveXss($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', '<script', 'object'/*, 'iframe', 'frame', 'frameset', 'ilayer' , 'layer' */, 'bgsound', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;
    }

    /**
    * 以数组的方式获取 form 表单 get/post 传过来的值
    *
    * @param string $arrKey     表单数组名
    * @param array $nameRules   数组形式 以键值对应的方式 传递规则
    * @param string $method     获取表单传递的方式 默认为post
    * @param string $special    例外 不需要验证字符串 以逗号隔开
    * @param string $pnumber    如果是负数 则强制替换成正数 将-替换为空
    * @return array
    */
    public static function getArr($arrKey,$nameRules,$method='post',$special='',$pnumber=''){
        $data = array();

        // 获取数据
        switch(strtolower($method)){
            case 'post':
                $data = $_POST[$arrKey];
                break;
            case 'get':
                $data = $_GET[$arrKey];
                break;
            default:
                return $data;
                break;
        }

        $useKeys = array_keys($nameRules);

        // 处理数据 过滤 以及验证
        //$ruleArr = explode(',',$nameRules);
        $specialArr = $special?explode(',',$special):array('');
        $pnumberArr = $pnumber?explode(',',$pnumber):array('');
        foreach($data as $key=>$val){
            if(!in_array($key,$useKeys)){
                exit('非法操作!');
            }
            $data[$key] = self::getStr($val,$nameRules[$key],'',!in_array($key,$specialArr));

            // 强制过滤一次 负数
            if(($nameRules[$key] == 'int' || $nameRules[$key]=='float') && in_array($key,$pnumberArr)){
                $data[$key] = abs($data[$key]);
            }
        }
        return $data;
    }

    /**
    * 取消HTML特殊字符 防止XSS
    * @param $string 可以为字符或者数组
    * @return $string 可以为字符或者数组
    */
    public static function shtmlspecialchars($string) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::shtmlspecialchars($val);
            }
        } else {
            $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
                str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
        }
        return $string;
    }

    /**
    * 取消HTML特殊字符 防止XSS
    * @param $array 可以为字符或者数组
    * @return $array 可以为字符或者数组
    */
    public static function specialhtml($array) {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (!is_array($value)) {
                    $array[$key] = htmlspecialchars($value);
                } else {
                    self::specialhtml($array[$key]);
                }
            }
        } else {
            $array = htmlspecialchars($array);
        }
    }

    /**
    * cookie设置
    * @param $var 设置的cookie名
    * @param $value 设置的cookie值
    * @param $life 设置的过期时间：为整型，单位秒 如60表示60秒后过期
    * @param $path 设置的cookie作用路径
    * @param $domain 设置的cookie作用域名
    */
    public static function ssetcookie($array, $life=0, $path = '/', $domain = "") {
        if(empty($domain)){
            $domain = SUtil::getCookieDomain();
        }
        $_cookName_ary = array_keys($array);
        for ($i = 0; $i < count($array); $i++) {
            setcookie($_cookName_ary[$i], $array[$_cookName_ary[$i]], $life ? (time() + $life) : 0, $path,$domain,NULL,true);
            //$_SESSION[$_cookName_ary[$i]] = $array[$_cookName_ary[$i]];
        }
    }

    /**
    * 返回用户是否有权限访问该栏目
    * @param int $license_id;
    * @return true|false
    */
    public static function isCanDo($license_id='', $is_admin=1){
        if($_COOKIE['admin_system'] == 1){
            return true;//如果是超级管理员，直接通过
        }
        if($is_admin){
            $licenseData = SUtil::getStr($_COOKIE['admin_ld'],'string');
        }else{
            $licenseData = SUtil::getStr($_COOKIE['ld'],'string');
        }
        $licenseArray = explode(",", $licenseData);
        if(in_array($license_id,$licenseArray)){
            return true;
        }
        return false;//否则返回false
    }

    //得到用户的登录验证key
    public static function getUserPassrandom($user_id){
        $userModel = new Model_ss_user();
        $one = $userModel->getOne(array('user_id'=>$user_id),array('pass_random'));
        $pass_random = $one['pass_random'];
        return $pass_random;
    }
    //得到admin的登录验证key
    public static function getAdminPassrandom($user_id){
        $userModel = new Model_ss_admin();
        $one = $userModel->getOne(array('user_id'=>$user_id),array('pass_random'));
        $pass_random = $one['pass_random'];
        return $pass_random;
    }
    /**
    * 验证空数组
    *
    * @param mixed $data
    */
    public static function isEmptyArr($data){
        if(!is_array($data) || count($data)<1){
            return true;
        }
        return false;
    }

    /**
    * IsMobile函数:检测参数的值是否为正确的中国手机号码格式
    * 返回值:是正确的手机号码返回手机号码,不是返回false
    */
    public static function IsMobile($Argv) {
        $RegExp = '/^(?:13|14|15|18)[0-9]\d{8}$/';
        //return preg_match($RegExp,$Argv)?$Argv:false;
        return preg_match($RegExp, $Argv) ? true : false;
    }

    /**
    *  验证是否为url
    *
    * @param string $str  url地址
    * @param boolean $exp_results   是否返回结果
    */
    public static function IsUrl($str,$exp_results=false){
        $RegExp = '/^(?:http\:\/\/)?[\w\.]+?\.(?:com|cn|mobi|net|org|so|co|gov|tel|tv|biz|cc|hk|name|info|asia|me|in).+$/';
        if(!preg_match($RegExp,$str,$m)){
            return false;
        }
        if($exp_results == true){
            return $m;
        }
        return true;
    }
    
    /**
    * get substr support chinese
    * return $str
    */
    static function getSubStr($str,$length,$postfix='...',$encoding='UTF-8') {
        $realLen = mb_strwidth($str,$encoding);
        if(!is_numeric($length) or $length*2>=$realLen) {
            return htmlspecialchars($str, ENT_QUOTES,$encoding);
        }
        $str = mb_strimwidth($str,0,$length*2,$postfix,$encoding);
        return htmlspecialchars($str, ENT_QUOTES,$encoding);
    }
    /* 截取utf8字符串,完美支持中文
    *     @param: 待截取的字符串,从第几个字符开始截取，截取长度，如超过长度是否加“..”
    */
    public static function utf8Substr($str, $from, $len, $adddot) {
        if ($adddot && mb_strlen($str, 'utf-8') > intval($len)) {
            return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str) . "..";
        } else {
            return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
        }
    }
    /**
    * 全站统一 字符长度计算方法  中文占2个 英文数字占1个
    */
    public static function cp_strlen($str){
        return (strlen($str) + mb_strlen($str,'UTF8')) / 2;
    }

    /**
    * 防止重复提交表单
    *
    * @param boolean $checked  是否检查表单重复提交
    * @param int $time_out     设置重复提交过期时间 单位 秒
    * @param boolean $exit_flag 是否退出
    * @return boolean
    */
    public static function prevent_post($checked=true,$time_out=5,$exit_flag=false){
        if($checked== false) return true;
        //使用cookie处理重复提交
        $request = array_merge($_POST,$_GET);
        $postname = '_pt_'.$_COOKIE['userid'].'_'.self::getMd5($_REQUEST);
        $preSub = $_COOKIE[$postname];
        if($preSub==1){
            if(true == $exit_flag){
                echo json_encode(array('status'=>'-5','flag'=>false));
                exit;
            }
            return false;
        }
        SUtil::ssetcookie(array($postname=>1),$time_out);
        return true;
    }

    /**
    * 返回当前页面的URL
    */
    public static function cur_page_url(){
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80"){
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }else{
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
    * 过淲敏感词汇
    *
    * @param unknown_type $word
    * @return 1,存在 0,不存在
    */
    function isFilterWords($word){
        $filterwords = file(ROOT_DATA.DIRECTORY_SEPARATOR.'filedata'.DIRECTORY_SEPARATOR.'filterwords.txt');
        foreach ($filterwords as $k=>$v){
            $filterwords[$k]=trim($v);
        }
        $str = implode('|',$filterwords);
        if(preg_match("/$str/",$word,$match)==1){//\n是匹配过滤字符后面的回车字符的
            return 1;
        }else {
            return 0;
        }
    }
    /**
    * 替换敏感词汇
    *
    * @param unknown_type $word
    * @return string
    */
    public static function filterWords($word){
        $filterwords = file(ROOT_DATA.DIRECTORY_SEPARATOR.'filedata'.DIRECTORY_SEPARATOR.'filterwords.txt');
        foreach ($filterwords as $k=>$v){
            $filterwords[$k]=trim($v);
        }
        $str = @implode('|',$filterwords);
        $content = preg_replace("/$str/i",'***',$word);
        return $content;
    }
    /**
    * 密码是否够强
    * @param string $pass
    * @return bool 是or否
    */
    public static function isStrongPass($pass) {
        $RegExp = '/^[a-zA-z0-9\_\W]{6,16}$/'; //由大小写字母跟数字下划线组成并且长度在6-16字符直接
        return preg_match($RegExp, $pass)? true : false;
    }
    /**
    * 计算密码强度
    */
    public static function getPassLevel($password){
        $partArr = array('/[0-9]/', '/[a-z]/', '/[A-Z]/', '/[\W_]/');
        $score = 0;

        //根据长度加分
        $score += strlen($password);
        //根据类型加分
        foreach($partArr as $part) {
            if(preg_match($part, $password)) $score += 5;//某类型存在加分
            $regexCount = preg_match_all($part, $password, $out);//某类型存在，并且存在个数大于2加2份，个数大于5加7份
            if($regexCount >= 5) {$score += 7;}
            elseif($regexCount >= 2) {$score += 2;}
        }
        //重复检测
        $repeatChar = '';
        $repeatCount = 0;
        for($i=0; $i<strlen($password); $i++) {
            if ($password{$i} == $repeatChar) $repeatCount++;
            else $repeatChar = $password{$i};
        }
        $score -= $repeatCount * 2;
        //等级输出
        $level = 0;
        if($score <= 10) { //弱
            $level = 1;
        } elseif($score <= 25) { //一般
            $level = 2;
        } elseif($score <= 37) { //很好
            $level = 3;
        } elseif($score <= 50) { //极佳
            $level = 4;
        }else{
            $level = 4;
        }
        //如果是密码为123456
        if(in_array($password, array('123456','abcdef'))){
            $level = 1;
        }
        return $level;
    }
    /**
    * isUsername函数:检测是否符合用户名格式
    * $Argv是要检测的用户名参数
    * $RegExp是要进行检测的正则语句
    * 返回值:符合用户名格式返回用户名,不是返回false
    */
    public static function isUsername($Argv) {
        if(self::cp_strlen($Argv)>16 || self::cp_strlen($Argv)<4) return false;
        //$RegExp = '/^(?:\w|[\x00-\xff])+$/'; //可包含 中文，字母，数字，下划线
        $RegExp = '/^(?:\w|[\x{4e00}-\x{9fa5}])+$/u';
        $stara = mb_substr($Argv, 0, 1, 'utf-8');
        $sRegExp = '/^\d|[a-zA-Z]*$/'; //判断首字符是否为字母
        return preg_match($RegExp, $Argv) && preg_match($sRegExp, $stara) ? true : false;
    }

    /*
    * GIF图片加水印 生成缩略图
    */
    function getNewGif($picpath,$text="",$width=0,$height=0,$water=""){
        if($text=="" && $width==0 && $height==0 && $water==""){
            return false;
        }
        $simagick = new SImagick();
        $simagick->open($picpath);
        if($width != 0 && $height != 0){
            $simagick->resize_to($width, $height, 'scale_fill');
        }
        if($text != ""){
            $simagick->add_text($text, 5, 15, 0, array("fill_color"=>"#ffffff"));
        }
        if($water != ""){
            $simagick->add_watermark($water, 10, 50);
            //$simagick->add_watermark('1024i.gif', 10, 50);
        }
        $simagick->save_to($picpath);
    }
    /*
    * 图片加水印 生成缩略图 另存为新图 gif=1时生成gif的缩略图
    */
    function getNewPic($picpath,$newpath,$width=0,$height=0,$text="",$water="",$gif=0){
        if($text=="" && $width==0 && $height==0 && $water==""){
            return false;
        }
        $simagick = new SImagick();
        $simagick->open($picpath);
        if($width != 0 && $height != 0){
            if($gif == 1){
                $simagick->resize_to($width, $height, 'force-one');
            }else{
                $simagick->resize_to($width, $height, 'force');
            }
        }
        if($text != ""){
            $simagick->add_text($text, 5, 15, 0, array("fill_color"=>"#ffffff"));
        }
        if($water != ""){
            $simagick->add_watermark($water, 10, 50);
            //$simagick->add_watermark('1024i.gif', 10, 50);
        }
        $simagick->save_to($newpath);
    }

    /*
    * 抓取远程图片
    * 图片会保存到static/u/下
    */
    function getImage($url,$dirname="/upload/pic/",$source=""){
        if($url == ""){
            return false;
        }
        if($source == "youku"){
            $ext = ".jpg";
        }else{
            $ext = strrchr($url, ".");
            if ($ext != ".gif" && $ext != ".jpg" && $ext != ".png") {
                return false;
            }
        }

        $filename = uniqid().$ext;

        $path="";

        $dirname = $dirname?(trim($dirname, '/').'/'):'';
        $path1 = $dirname . date('Y-m/d', time()).'/';
        $path = "../upload/u/".$path1;
        if (!file_exists($path)) SUtil::Mkpath($path);
        $pathname = $path.$filename;

        //文件 保存路径
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        //文件大小
        $fp2 = @fopen($pathname, "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return SConstant::IMAGE_URL."/u/".$path1.$filename;
    }
    function Mkpath($path){
        $dirs=array();
        $path=preg_replace('/(\/){2,}|(\\\){1,}/','/',$path); //only forward-slash
        $dirs=explode("/",$path);
        $path="";
        foreach ($dirs as $element){
            $path.=$element."/";
            if(!is_dir($path)){
                if(!mkdir($path,0777)){ echo "创建目录出错于 : ".$path; return 0; }
            }
        }
    }

    /**
    * 转4个0金额转2个0
    * @param unknown_type $float
    */
    public static function price_cut($float){
        $strArr = explode('.',$float);
        $count = 2;
        for($i=3;$i>1;$i--){
            if(@$strArr[1]{$i}!=0){
                $count=$i+1;break;
            }
        }
        $float = implode('.',$strArr);
        return sprintf("%.{$count}f",$float);
    }

    /**
    * 文件缓存设置 
    * 
    * @param mixed $key     key
    * @param mixed $values  数据 string|array
    */
    public static function fileCacheSet($key,$values){
        $fileModel = SCache::getCacheEngine('file');
        $fileModel->set($key,$values);
    }
    /**
    * 获取文件缓存内容
    * 
    * @param mixed $key  key值
    */
    public static function fileCacheGet($key){
        $fileModel = SCache::getCacheEngine('file');
        return $fileModel->get($key);
    }
    
    /*
     * 获取网站配置信息
     */
    public static function getSettings($key="",$setg = "site"){
        $setModel = new Model_ss_settings();
        $value = $setModel->getSettings($setg);
        if($key){
            return $value[$setg][$key];
        }else{
            return $value;
        }
    }
    /*
     * 获取cookie根域名
     */
    public static function getCookieDomain() {
        $site_url = SUtil::getSettings("siteurl");
        $site_url = ltrim($site_url, "http://");
        if(empty($site_url) || $site_url == "localhost" || $site_url == "127.0.0.1"){
            return "";
        }
        $domain = SUtil::getUrlToDomain($site_url);
        return ".".$domain;
    }
    
    /**
    * 取得根域名
    * @param type $domain 域名
    * @return string 返回根域名
    */
   function getUrlToDomain($domain) {
       $re_domain = '';
       $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
       $array_domain = explode(".", $domain);
       $array_num = count($array_domain) - 1;
       if ($array_domain[$array_num] == 'cn') {
           if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
               $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
           } else {
               $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
           }
       } else {
           $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
       }
       return $re_domain;
   }
   
   /*
     * 获得可用的模版
     */
    public static function getTemplatelists() {
        /* 获得可用的模版 */
        $available_templates = array();
        $template_dir        = @opendir(ROOT_THEMES);
        while ($file = readdir($template_dir)){
            if ($file != '.' && $file != '..' && is_dir(ROOT_THEMES . "/" . $file) && $file != '.svn' && $file != 'index.htm'){
                $available_templates[] = $file;
            }
        }
        @closedir($template_dir);
        return $available_templates;
    }
    
    
    /**
    * curl post提交
    * 
    * @param mixed $url
    * @param mixed $var
    * @param mixed $timeout
    * @param mixed $type
    * @param mixed $referer
    * @return string
    */
    public static function http_curl($url, $var="", $timeout = 120, $type = 'get', $referer = ''){
        $curl = curl_init(); 

        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $theHeaders);
        if(!empty($referer)){
            curl_setopt($curl, CURLOPT_REFERER, $referer);
        }
        //curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        //curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        if($type == 'post'){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true); 

        $data['str'] = curl_exec($curl); 
        $data['status'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $data['errno'] = curl_error($curl);

        curl_close($curl);  

        return $data;
    }
   
}
?>
