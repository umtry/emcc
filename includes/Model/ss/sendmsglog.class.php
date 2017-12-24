<?php
class Model_ss_sendmsglog extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'sl_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_sendmsg_log';
	}
    
    //列表
    function get_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['phone'])){
            $condition['phone'] = $cond['phone'];
        }
        if(!empty($cond['stype'])){
            $condition['stype'] = $cond['stype'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
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