<?php

class Controller_basepage extends STpl {

    public $_time;
    public $_userid;
    public $_username;
    public $_last_time;
    public $_last_ip;
    public $_level;
    public $_groupid;
    public $_groupname;
    public $_is_system;
    public $_licenseData;
    public $limit = 20;

    public function __construct() {
        parent::__construct();
        session_start();
        header("Content-type:text/html;charset=utf-8");
        $this->_time = time();
        $this->loadLoginUser();
        $this->loadSidebar();
        $this->assign('entry', SlightPHP::$entry);

        // ajax 信息输出 处理
        $this->outData['append'] = array();     // 追加
        $this->outData['html'] = array();       // 替换内容
        $this->outData['remove'] = array();     // 删除
        $this->outData['data'] = '';            // 方法中的数据
        $this->outData['runFunction'] = '';            // 方法中的数据
        $this->outData['method'] = 'write';     // write需要写入    alert 只做提示
    }

    // 获取值
    public function get($key, $type = 'string', $default = '', $required = true) {
        $value = SUtil::getStr($_GET[$key], $type, $default);
        if ($required && $value === "") {
            $this->showMsg("请填写必填项");
            exit;
        }
        return $value;
    }

    // ajax获取值
    public function ajget($key, $type = 'string', $default = '', $required = true) {
        $value = SUtil::getStr($_GET[$key], $type, $default);
        if ($required && $value === "") {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '请填写必填项';
            $this->printJson();
            exit;
        }
        return $value;
    }

    // 获取值
    public function post($key, $type = 'string', $default = '', $required = true) {
        $value = SUtil::getStr($_POST[$key], $type, $default);
        if ($required && $value === "") {
            $this->showMsg("请填写必填项");
            exit;
        }
        return $value;
    }

    // ajax获取值
    public function ajpost($key, $type = 'string', $default = '', $required = true) {
        $value = SUtil::getStr($_POST[$key], $type, $default);
        if ($required && $value === "") {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '请填写必填项';
            $this->printJson();
            exit;
        }
        return $value;
    }

    public function ajrequest($key, $type = 'string', $default = '', $required = true) {
        $value = SUtil::getStr($_REQUEST[$key], $type, $default);
        if ($required && $value === "") {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '请填写必填项';
            $this->printJson();
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
        $data = isset($data) ? $data : $this->outData;
        $jcb = SUtil::getStr($_REQUEST['jsoncallback']);
        if ($jcb) {//如果是跨域操作
            echo $jcb . "(" . json_encode($data) . ");";
        } else {
            echo json_encode($data);
        }
        exit;
    }

    //获取栏目
    public function loadSidebar() {
        $sysnavModel = new Model_ss_sysnav();
        $licenseModel = new Model_ss_license();
        $sysnav = $sysnavModel->getList(array(1), "*", "order by sort desc");
        foreach ($sysnav as $key => $val) {
            $rsdata = $licenseModel->getList(array("sysnav_id" => $val['sysnav_id'], "status" => 1, "license_type" => 1, "license_url <>''"), "license_id,license_name,license_url", "order by sort desc");
            $sysnav[$key]['licenses'] = $rsdata;
            $licid_arr = array();
            foreach ($rsdata as $v) {
                $licid_arr[] = $v['license_id'];
            }
            $sysnav[$key]['licid_arr'] = $licid_arr;
        }
        $this->assign('sidebar_sysnav', $sysnav);
    }

    public function loadLoginUser() {
        if (isset($_COOKIE['admin_username'], $_COOKIE['admin_userid'], $_COOKIE['admin_userkey'], $_COOKIE['admin_groupid'])) {
            $_username = $_COOKIE['admin_username'];
            $_userid = (int) $_COOKIE['admin_userid'];
            $_userkey = $_COOKIE['admin_userkey'];
            $_last_time = $_COOKIE['admin_lasttime'];
            $_last_ip = $_COOKIE['admin_lastip'];
            $_groupid = $_COOKIE['admin_groupid'];
            $_groupname = $_COOKIE['admin_groupname'];
            $_is_system = $_COOKIE['admin_system'];
            $_licenseData = $_COOKIE['admin_ld'];
            $adminModel = new Model_ss_admin();
            $adminArr = $adminModel->getOne(array('user_id' => $_userid), array('pass_random,port'));
            $pass_random = $adminArr['pass_random'];
            $port = $adminArr['port'];
            $lgkey = md5($_userid . $_username . $_groupid . $_is_system . SConstant::ADMINUSERKEY . $pass_random . $_licenseData);
            if ($_userkey == $lgkey && $this->_time - $_COOKIE['admin_acttime'] < SConstant::LOGOUTLIMIT) {
                SUtil::ssetcookie(array('admin_acttime' => $this->_time), 0, '/', SConstant::COOKIE_DOMAIN);
                $this->_username = $_username;
                $this->_userid = $_userid;
                $this->_groupid = $_groupid;
                $this->_groupname = $_groupname;
                $this->_last_time = $_last_time;
                $this->_last_ip = $_last_ip;
                $this->_licenseData = $_licenseData;
                $this->assign('username', $this->_username);
                $this->assign('userid', $this->_userid);
                $this->assign('groupid', $this->_groupid);
                $this->assign('groupname', $this->_groupname);
                $this->assign('ld', explode(",", $this->_licenseData));
                $this->assign('lastymd', date('Y-m-d H:i:s', $this->_last_time));
                $this->assign('lastip', $this->_last_ip);
                $this->assign('nowtime', $this->_time);
                $this->assign('nowymd', date('Y-m-d', $this->_time));
                $this->assign('port', $port);
                //呼叫服务器ip
                $call_server_ip = SUtil::getSettings("call_server_ip","callcenter");
                $this->assign('call_server_ip', $call_server_ip);
            } else {
                $array = array('admin_username' => '', 'admin_userid' => '', 'admin_userkey' => '', 'admin_groupid' => '', 'admin_ld' => '');
                SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
            }
        }
    }

    //state 1为正确 2为错误 其它为警告
    public function showMsg($msg = 'message', $backurl = '', $second = 3, $state = 0) {
        $params['msg'] = $msg;
        $params['url'] = $backurl;
        $params['second'] = $second;
        $params['state'] = $state;
        $this->srender('common/showmsg.html', $params);
        exit;
    }

    function pageBar($count, $limit, $i_page, $inPath, $anchor = '') {
        if (empty($anchor) && !empty($_SERVER['QUERY_STRING'])) {
            $anchor = '?' . $_SERVER['QUERY_STRING'];
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
                $params['start'] = (int) ($i_page - SConstant::PAGE_NUM / 2);
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
        for ($i = $params['start']; $i < min(($params['start'] + $params['max']), $params['pagenum']); $i++) {
            $params['pages'][$i]['page'] = $i + 1;
            $params['pages'][$i]['url'] = SlightPHP::createUrl($rule, array_merge($pars, array('page' => $params['pages'][$i]['page']))) . $anchor;
        }
        $this->assign('_p', $params);
    }

    /**
     * 权限处理
     *
     * @param mixed $app_url     检查地址
     * @param mixed $isTip       是否退出提示
     */
    public function isCanUse($object_id, $is_true = true, $is_system = 0) {
        if ($is_true) {
            if ($is_system == 1) {
                if ($_COOKIE['admin_system'] != 1) {//系统管理员可操作，对应adminuser表中的is_system
                    $this->showMsg('您没有权限访问该栏目！');
                }
            }
            if (!empty($object_id)) {
                $licenseModel = new Model_ss_license();
                $licenseInfo = $licenseModel->getOne(array('license_id' => $object_id, 'status' => 1));
                //验证ID是否同用户组一致
                $adminModel = new Model_ss_admin();
                $sql = "select * from ss_admin as sa "
                    . "inner join ss_group as sg on(sa.group_id=sg.group_id) "
                    . "where sa.user_id={$this->_userid} and sa.status=1 and sg.user_type=1 and sg.license_data='" . $_COOKIE['admin_ld'] . "' limit 1";
                $rs = $adminModel->query("ss_admin", $sql);
                //$userCount = $adminModel->getCount(array('user_id'=>$this->_userid, 'license_data'=>$_COOKIE['admin_ld'], 'status'=>1));

                if (empty($rs)) {
                    $array = array('admin_username' => '', 'admin_userid' => '', 'admin_userkey' => '', 'admin_groupid' => '', 'admin_ld' => '', 'admin_system' => "");
                    SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
                    $this->showMsg('您的权限不是最新权限，请重新登录!', SConstant::SSADMIN, 3, 1);
                }
                if (!empty($licenseInfo['license_id'])) {
                    $isCanDo = SUtil::isCanDo($licenseInfo['license_id']);
                    if ($isCanDo == false) {
                        $this->showMsg('您没有权限访问该栏目！');
                    }
                }
            }
        }
        $this->assign('_use_id', $object_id);
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
    public function getUploadFilePath($_file, $dirname = '../upload', $sizelimit = '', $allowtype = SConstant::UPLOADPICTYPE) {
        if (!empty($_file['name'])) {
            $_file['name'] = strtolower($_file['name']);
            $_file['name'] = explode(".", $_file['name']);
            $ext = end($_file['name']);
            $ary = explode(',', $allowtype);
            if (!in_array($ext, $ary))
                return array('status' => 0, 'error' => 1, 'msg' => "不允许上传的文件类型,上传文件类型只能为：" . $allowtype . "!");

            if (!$sizelimit)
                $sizelimit = SConstant::UPLOADIMGSIZE;
            if ($_file['size'] > $sizelimit) {
                $fallowsize = round($sizelimit / 1024, 2) . 'kb' . ' 实际上传文件大小为：' . round($_file['size'] / 1024, 2) . 'kb';
                return array('status' => 0, 'error' => 1, 'msg' => "文件大小超出规定限制：{$fallowsize},请重新上传!");
            }
            $dirname = $dirname ? (trim($dirname, '/') . '/') : '';
            $query['path'] = $dirname . date('Ym/d', time()) . '/';

            if (!file_exists($query['path'])) {
                if (!mkdir($query['path'], 0777, true)) {
                    return array('error' => 1, 'status' => 0, 'msg' => '创建文件夹失败');
                }
            }
            $query['name'] = md5(time() . rand(1, 100000000) . uniqid()) . '.' . $ext;
            $fullName = $query['path'] . $query['name'];
            if (!move_uploaded_file($_file['tmp_name'], $fullName)) {
                return array('error' => 1, 'status' => 0, 'msg' => '上传错误');
            }
            //$imgurl = substr($fullName,2);
            $imgurl = ltrim($fullName, "\.\.");
            $imgurl = str_replace("/upload", "", $imgurl);
            return array('error' => 0, 'url' => $imgurl, 'size' => $_file['size'], 'type' => $ext);
        } else {
            return array('error' => 1, 'url' => '', 'msg' => "请选择要上传的文件!");
        }
    }

    public function isLogin($redirect = false) {
        if (!$this->_userid || !$this->_username) {
            if ($redirect) {
                header("Content-type: text/html; charset=utf-8");
                header("Location:" . SConstant::SSADMIN . "/login/index.html");
                exit;
            }
            return false;
        }

        return true;
    }

    // 退出
    public function logout() {
        $array = array('admin_username' => '', 'admin_userid' => '', 'admin_userkey' => '', 'admin_groupid' => '', 'admin_ld' => '', 'admin_system' => "");
        SUtil::ssetcookie($array, -1, '/', SConstant::COOKIE_DOMAIN);
    }

}

/**
	End file,Don't add ?> after this.
*/