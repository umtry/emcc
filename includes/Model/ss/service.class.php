<?php
class Model_ss_service extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_service';
	}
    //列表
    function get_list($cond,$fields,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
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