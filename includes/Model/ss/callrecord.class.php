<?php
class Model_ss_callrecord extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_callrecord';
	}
    
    //列表
    function get_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['user_id'])){
            $condition['user_id'] = $cond['user_id'];
        }
        if(!empty($cond['admin_id'])){
            $condition['admin_id'] = $cond['admin_id'];
        }
        if(!empty($cond['call_type'])){
            $condition['call_type'] = $cond['call_type'];
        }
        if(!empty($cond['answer_status'])){
            $condition['answer_status'] = $cond['answer_status'];
        }
        if(!empty($cond['phone'])){
            $condition['phone'] = $cond['phone'];
        }
        if(!empty($cond['stime'])){
            $condition[] = "call_time>".$cond['stime'];
        }
        if(!empty($cond['etime'])){
            $condition[] = "call_time<=".$cond['etime'];
        }
        $rs['list'] = $this->getList($condition, '*', $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
    
    //删除
    function del($id){
        if(empty($id)){
            $this->setError(-1, '参数错误');
            return false;
        }
        $rs = $this->delete(array('id'=>$id));
        if($rs === false){
            $this->setError(-1, '删除失败');
            return false;
        }
        return true;
    }
}

/**
	End file,Don't add ?> after this.
*/