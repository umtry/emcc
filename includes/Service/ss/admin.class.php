<?php
/**
 * 管理员Serv
 */
class Service_ss_admin extends Service_baseservice{
    /**
    * 登录
    * @param username
    * @param password
    * @param isCache 登录状态是否保存一周
    * @param isRemember 是否记住用户名
    * @return bool 成功或失败
    */
    public function do_login($username, $password, $isCache=false) {
        //参数检测
        if(empty($username)||empty($password)) {
            $this->setError(-1001, "账号密码不能为空");return false;
        }

        // 初始化model
        $adminModel = new Model_ss_admin();
        $groupModel = new Model_ss_group();
        $aloginModel = new Model_ss_alogin();

        $userArr = $adminModel->getOne(array('user_name' => $username));

        $writepassword = $password;//用户输入密码
        $password = md5($password.SConstant::ADMINUSERKEY.$userArr['pass_random']);

        if((!$userArr || ($password !== $userArr['user_pass']))){
            $this->setError(-1002, "密码错误");
            return false;
        }elseif($userArr['status'] == 0){//密码匹配上了但是被锁定了
            //写入登录日志
            $aloginModel->createLog($userArr['user_id'], $userArr['user_name'],'用户账号被锁定['.$userArr['lock_note'].']<br />'.$_SERVER['HTTP_USER_AGENT'],0);
            $this->setError(-1003, '用户账号被锁定['.$userArr['lock_note'].']');return false;
        }else{//登录成功
            $pass_random = $adminModel->update_login($userArr['user_id'],$writepassword);
            //获取组权限
            $gdata = $groupModel->getOne(array("group_id"=>$userArr['group_id'],"user_type"=>1), "license_data,group_name");
            //写cookie
            $loginInfo = array(
                'admin_userid' => $userArr['user_id'],
                'admin_username' => $userArr['user_name'],
                'admin_groupid' => $userArr['group_id'],
                'admin_groupname' => $gdata['group_name'],
                'admin_system' => $userArr['is_system'],
                'admin_lasttime' => $userArr['last_time'],
                'admin_lastip' => $userArr['last_ip'],
                'admin_acttime' => $this->_time,
                'admin_ld' => $gdata['license_data'],
                'admin_userkey' => md5($userArr['user_id'] . $userArr['user_name'] . $userArr['group_id'] . $userArr['is_system'] . SConstant::ADMINUSERKEY . $pass_random . $gdata['license_data'])
            );
			
            $expireTime = 0;
            if ($isCache) $expireTime = 3600 * 24 * 7;
            SUtil::ssetcookie($loginInfo, $expireTime, '/', SConstant::COOKIE_DOMAIN);
            $userArr['admin_userkey'] = $loginInfo['admin_userkey'];
            
            //写入登录日志
            $aloginModel->createLog($userArr['user_id'], $userArr['user_name'],'成功登录',1);
            return $userArr;
        }
    }
}
