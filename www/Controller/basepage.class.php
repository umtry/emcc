<?php
class Controller_basepage extends STpl {
    public $_time;
    public $_user_id;
    public $_user_name;
    public $_last_time;
    public $_last_ip;
    public $_level;
    public $_groupid;
    public $gp = array();
    public $limit = 20;

    public function __construct() {
        parent::__construct();
        session_start();
        header("Content-type:text/html;charset=utf-8");
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $this->_time = time();
        $this->loadLoginUser();
        // ajax 信息输出 处理
        $this->outData['append'] = array();
        $this->outData['html'] = array();
        $this->outData['remove'] = array();
        $this->outData['data'] = '';
        $this->outData['runFunction'] = '';
        $this->outData['method'] = 'write';
    }
    
    // 获取值
    public function get($key,$type='string',$default='',$required=true){
        $value = SUtil::getStr($_GET[$key],$type,$default);
        if($required && $value===""){
            $this->showMsg("请填写必填项");
            exit;
        }
        return $value;
    }
    // ajax获取值
    public function ajget($key,$type='string',$default='',$required=true){
        $value = SUtil::getStr($_GET[$key],$type,$default);
        if($required && $value===""){
            $this->printJson(array("status"=>0,"msg"=>"请填写必填项"));
            exit;
        }
        return $value;
    }
    // 获取值
    public function post($key,$type='string',$default='',$required=true){
        $value = SUtil::getStr($_POST[$key],$type,$default);
        if($required && $value===""){
            $this->showMsg("请填写必填项");
            exit;
        }
        return $value;
    }
    // ajax获取值
    public function ajpost($key,$type='string',$default='',$required=true){
        $value = SUtil::getStr($_POST[$key],$type,$default);
        if($required && $value===""){
            $this->printJson(array("status"=>0,"msg"=>"请填写必填项"));
            exit;
        }
        return $value;
    }
    
    function nocacheHeader() {
        @header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        @header('Cache-Control: no-cache, must-revalidate, max-age=0');
        @header('Pragma: no-cache');
    }

    public function printJson($data = null) {
        $jcb = SUtil::getStr($_REQUEST['jsoncallback']);
        if ($jcb) {//如果是跨域操作
            echo $jcb . "(" . json_encode($data) . ");";
        } else {
            echo json_encode($data);
        }
        exit;
    }

    public function loadLoginUser() {
        if (isset($_COOKIE['user_name'], $_COOKIE['user_id'],$_COOKIE['userkey'],$_COOKIE['groupid'])) {
            $_user_name = $_COOKIE['user_name'];
            $_user_id = (int)$_COOKIE['user_id'];
            $_userkey = $_COOKIE['userkey'];
            $_last_time = $_COOKIE['last_time'];
            $_last_ip = $_COOKIE['last_ip'];
            $_groupid = $_COOKIE['groupid'];
            $userModel = new Model_ss_user();
            $userArr = $userModel->getOne(array('user_id'=>$_user_id),array('pass_random'));
            $pass_random = $userArr['pass_random'];
            $userkey = md5($_user_id . $_user_name . SConstant::SYSUSERKEY . $pass_random);
            if ($_userkey == $userkey&&$this->_time-$_COOKIE['action_time']<SConstant::LOGOUTLIMIT) {
                SUtil::ssetcookie(array('action_time'=>$this->_time),0, '/', SConstant::COOKIE_DOMAIN);
                $this->_user_name = $_user_name;
                $this->_user_id = $_user_id;
                $this->_groupid = $_groupid;
                $this->_last_time = $_last_time;
                $this->_last_ip = $_last_ip;
                $this->assign('user_name', $this->_user_name);
                $this->assign('user_id', $this->_user_id);
                $this->assign('groupid', $this->_groupid);
                $this->assign('last_ymd',date('Y-m-d H:i:s',$this->_last_time));
                $this->assign('last_ip',$this->_last_ip);
                $this->assign('nowtime', $this->_time);
                $this->assign('nowymd', date('Y-m-d', $this->_time));
            } else {
                $array = array('user_name' => '', 'user_id' => '', 'userkey' => '', 'groupid'=>'');
                SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
            }
        }
    }
    

    //state 1为正确 2为错误 其它为警告
    public function showMsg($msg='message', $backurl='', $second=3, $state=0) {
        $params['msg'] = $msg;
        $params['url'] = $backurl;
        $params['second'] = $second;
        $params['state'] = $state;
        echo $this->render('common/showmsg.html', $params);
        exit;
    }

    function pageBar($count, $limit, $i_page, $inPath, $anchor = '') {
        if(empty($anchor) && !empty($_SERVER['QUERY_STRING'])){
            $anchor = '?'.$_SERVER['QUERY_STRING'];
        }
        $pagenum = ceil($count / $limit);
        $i_page = max(1, $i_page);
        $page = min($pagenum, $i_page);
        $prepg = $page - 1;
        $nextpg = $page == $pagenum ? 0 : $page + 1;
        $offset = ($page - 1) * $limit;
        $startdata = $count ? ($offset + 1) : 0;
        $enddata = min($offset + $limit, $count);
        $rule = "{$inPath[1]}/{$inPath[2]}";
        $pars = SUtil::getUrlParams($inPath);
        if (array_key_exists('page', $pars)) {
            unset($pars['page']);
        }
        $params['totalSize'] = $count;
        $params['pageSize'] = $limit;
        $params['first'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => 1))) . $anchor;
        if (!empty($nextpg)) {
            $params['nextpg'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => $nextpg))) . $anchor;
        } else {
            $params['nextpg'] = null;
        }
        $params['nextpg_1'] = $nextpg;
        $params['prepg_1'] = $prepg;
        if (!empty($prepg)) {
            $params['prepg'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => $prepg))) . $anchor;
        } else {
            $params['prepg'] = null;
        }
        $params['last'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => $pagenum))) . $anchor;
        $params['startdata'] = $startdata;
        $params['enddata'] = $enddata;
        $params['currpage'] = $i_page;
        $params['pagenum'] = $pagenum;
        if ($pagenum > SConstant::PAGE_NUM) {
            if ($i_page >= SConstant::PAGE_NUM) {
                $params['start'] = (int)($i_page - SConstant::PAGE_NUM / 2);
                $params['max'] = SConstant::PAGE_NUM;
                if ($pagenum - $params['start'] < SConstant::PAGE_NUM) {
                    $params['start'] = $pagenum - SConstant::PAGE_NUM;
                }
            } else {
                $params['start'] = 0;
                $params['max'] = SConstant::PAGE_NUM;
            }
        } else {
            $params['start'] = 0;
            $params['max'] = SConstant::PAGE_NUM;
        }
        for ($i = $params['start']; $i < min(($params['start']+$params['max']),$params['pagenum']); $i++) {
            $params['pages'][$i]['page'] = $i + 1;
            $params['pages'][$i]['url'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => $params['pages'][$i]['page']))) . $anchor;
        }
        $this->assign('_p',$params);
    }

    /*
    *     权限处理函数
    *    @param int $object_id;对象ID
    *   @param true|false $is_true;
    *    @return null;
    */
    public function isCanUse($object_id, $is_true = true){
        if($is_true){
            if(!empty($object_id)){
                $licenseModel = new Model_ss_license();
                $licenseInfo = $licenseModel->getOne(array('license_id'=>$object_id, 'is_true'=>1));
                //验证ID是否同用户组一致
                $userModel = new Model_ss_user();
                $sql = "select * from ss_user as su "
                    . "inner join ss_group as sg on(sa.group_id=sg.group_id) "
                    . "where su.user_id={$this->_userid} and su.status=1 and sg.user_type=2 and sg.license_data='".$_COOKIE['ld']."' limit 1";
                $rs = $userModel->query("ss_user", $sql);
                if(empty($rs)){
                    $array = array('username' => '', 'userid' => '', 'userkey' => '', 'groupid'=>'', 'ld'=>'');
                    SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
                    $this->showMsg('您的权限不是最新权限，请重新登录!');
                }
                if(!empty($licenseInfo['license_id'])){
                    $isCanDo = SUtil::isCanDo($licenseInfo['license_id'],0);
                    if($isCanDo == false){
                        $this->showMsg('您没有权限访问该栏目！');
                    }
                }
            }
        }
    }


	/**
	 * 文件上传方法
	 *
	 * @param array $_file 文件流
	 * @param array $_size 大小尺寸 eg. array('80x80','120x120');
	 * @param string $nomask 是否不加水印
	 * @param string $dirname 自定义路径
	 * @param string $sizelimit 文件大小限制 单位Byte
	 * @param string $allowtype 允许类型
	 * @return array 上传成功的路径 或错误信息
	 */
    public function getUploadFilePath($_file, $dirname='../upload', $sizelimit = '', $allowtype=SConstant::UPLOADPICTYPE) {
        if (!empty($_file['name'])) {
            $_file['name'] = strtolower($_file['name']);
            $ext = strtolower(end(explode(".", $_file['name'])));
            $ary = explode(',', $allowtype);
            if (!in_array($ext, $ary))
                return array('status' => 0, 'error' => 1, 'msg' => "不允许上传的文件类型,上传文件类型只能为：" . $allowtype . "!");

            if (!$sizelimit)
                $sizelimit = SConstant::UPLOADIMGSIZE;
            if ($_file['size'] > $sizelimit) {
                $fallowsize = round($sizelimit / 1024, 2) . 'kb' . ' 实际上传文件大小为：' . round($_file['size'] / 1024, 2) . 'kb';
                return array('status' => 0, 'error' => 1, 'msg' => "文件大小超出规定限制：{$fallowsize},请重新上传!");
            }
            $dirname = $dirname?(trim($dirname, '/').'/'):'';
            $query['path']= $dirname . date('Ym/d', time()) . '/' ;

            if (!file_exists($query['path'])){
                if (!mkdir($query['path'],0777,true)){
                    return array('error' => 1 ,'status'=>0, 'msg' => '创建文件夹失败');
                }
            }
			$query['name'] = md5(time().rand(1,100000000).uniqid()) . '.' . $ext;
            $fullName = $query['path'].$query['name'];
            if (!move_uploaded_file($_file['tmp_name'],$fullName)) {
                return array('error' => 1 ,'status'=>0, 'msg' => '上传错误');
            }
			//$imgurl = substr($fullName,2);
            $imgurl = ltrim($fullName,"\.\.");
            $imgurl = str_replace("/upload","",$imgurl);
			return array('error' => 0, 'url' => $imgurl, 'size' => $_file['size'], 'type' => $ext);
         } else {
            return array('error' => 1, 'url' => '', 'msg' => "请选择要上传的文件!");
        }
    }

    public function isLogin($redirect=false) {
        if(!$this->_user_id || !$this->_user_name){
            if($redirect){
                header("Content-type: text/html; charset=utf-8");
                header("Location:/login.html");
                exit;
            }
            return false;
        }

        return true;
    }

    // 退出
    public function logout() {
        $array = array('user_name' => '', 'user_id' => '', 'userkey' => '', 'groupid'=>'', 'ld'=>'');
        SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
    }
}

/**
	End file,Don't add ?> after this.
*/