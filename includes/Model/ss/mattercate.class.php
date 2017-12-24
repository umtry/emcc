<?php
class Model_ss_mattercate extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'st_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_matter_cate';
	}
    
    //列表
    function get_list($cond,$orderby,$fields="*",$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['st_id'])){
            $condition['st_id'] = $cond['st_id'];
        }
        if(!empty($cond['parent_id'])){
            $condition['parent_id'] = $cond['parent_id'];
        }
        if(!empty($cond['st_name'])){
            $condition['st_name'] = $cond['st_name'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
        }
        $rs['list'] = $this->getList($condition, $fields, $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
}

/**
	End file,Don't add ?> after this.
*/