<?php
class Model_ss_alogin extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_alogin';
	}
    
    //写日志
    public function createLog($user_id,$user_name,$note,$status=1){
        if(empty($user_id)){
            return false;
        }
        $logDate = array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'note'=>$note,
            'status'=>$status,
            'add_time'=>$this->_time,
            'add_ip'=>SUtil::getIP()
        );
        $adminlog_id = $this->insert($logDate);
        return $adminlog_id;
    }
    
}

/**
	End file,Don't add ?> after this.
*/