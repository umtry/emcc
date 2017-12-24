<?php
class Model_ss_user extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'user_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_user';
	}
    
    /**
	 * 更新用户登录成功的信息
	*/
	public function update_login($userid,$user_pass=''){
		if(empty($userid)){
            return false;
        }
		$where = array('user_id' => $userid);
		$loginSucInfo = array(
            'last_time'=>$this->_time,
            'last_ip'=>SUtil::getIp()
		);
		if($user_pass){
			$loginSucInfo['pass_random'] = SUtil::random(6,0);
			$loginSucInfo['user_pass'] = md5($user_pass.SConstant::SYSUSERKEY.$loginSucInfo['pass_random']);
		}
		$re = $this->update($where, $loginSucInfo);
		if($re==false||!$loginSucInfo['pass_random']){//未更新成功或者未更新pass_random那么返回原来的sn
			$one = $this->getOne($where,array('pass_random'));
			$loginSucInfo['pass_random'] = $one['pass_random'];
		}
		return $loginSucInfo['pass_random'];
	}
    
    /**
	 * 修改密码 
	 * @param string $newpwd
	 * @param booler
	*/
    public function change_pwd($userid,$newpwd){
        if(empty($userid) || empty($newpwd)){
            return false;
        }
        $where = array('user_id' => $userid);
        $info['pass_random'] = SUtil::random(6,0);
        $info['user_pass'] = md5($newpwd.SConstant::SYSUSERKEY.$info['pass_random']);
		$re = $this->update($where, $info);
        if($re === false){
            return false;
        }
        return true;
    }
    /**
	 * 检查用户名是否占用 
	 * @param string $username
	 * @param array|false 已占用或未占用
	*/
	public function check_name_used($username){
		return $this->getOne(array('user_name'=>$username), 'user_id,user_name');
	}
    
}

/**
	End file,Don't add ?> after this.
*/