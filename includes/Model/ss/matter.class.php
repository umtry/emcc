<?php
class Model_ss_matter extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_matter';
	}
    
    //列表
    function get_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['user_id'])){
            $condition['user_id'] = $cond['user_id'];
        }
        if(!empty($cond['real_name'])){
            $condition['real_name'] = $cond['real_name'];
        }
        if(!empty($cond['intention'])){
            $condition['intention'] = $cond['intention'];
        }
        if(!empty($cond['answer_state'])){
            $condition['answer_state'] = $cond['answer_state'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
        }
        if(!empty($cond['stime'])){
            $condition[] = "add_time>".$cond['stime'];
        }
        if(!empty($cond['etime'])){
            $condition[] = "add_time<=".$cond['etime'];
        }
        $rs['list'] = $this->getList($condition, '*', $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
    
}

/**
	End file,Don't add ?> after this.
*/